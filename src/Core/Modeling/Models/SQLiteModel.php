<?php


namespace Absoft\Line\Core\Modeling\Models;


use Application\conf\Resource;
use Absoft\Line\Core\DbConnection\Database;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQLite\JointSelection;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\DbBuilders\Builder;
use PDOStatement;

class SQLiteModel {

    public $MAINS;
    public $TABLE_NAME;
    public $HIDDEN;
    public $DATABASE;
    public $DATABASE_NAME;

    /** @var Database|null  */
    public $DATABASE_OBJECT = null;

    /**
     * @param Database|null $db
     * @throws DBConnectionError
     */
    public function setDB($db = null){
        $this->DATABASE_OBJECT = ($db ? $db : new Database($this->DATABASE, $this->DATABASE_NAME));
    }

    /**
     * @return Database|null
     * @throws DBConnectionError
     */
    public function getDB(){
        return $this->DATABASE_OBJECT ? $this->DATABASE_OBJECT : new Database($this->DATABASE, $this->DATABASE_NAME);
    }

    public function beginTransaction($db = null){
        return $this->DATABASE_OBJECT->beginTransaction();
    }

    public function commit(){
        return $this->DATABASE_OBJECT->commit();
    }

    public function rollback(){
        return $this->DATABASE_OBJECT->rollback();
    }

    /**
     * @return int|string
     */
    public function lastInsertId(){
        return $this->DATABASE_OBJECT->lastInsertId();
    }

    /**
     * @return Builder
     * @throws ClassNotFound
     */
    function getEntity(){

        $entity_name = 'Application\\Builders\\'.$this->TABLE_NAME;

        if(Resource::checkFile("/apps/Builders/".$this->TABLE_NAME.".php")){
            return new $entity_name;
        }else{
            throw new ClassNotFound($entity_name, __FILE__, __LINE__);
        }

    }

    /**
     * @param array $search_array
     * @param array $other
     * @return array|bool|PDOStatement
     * @throws OperationFailed|DBConnectionError
     */
    public function advancedSearch(Array $search_array, $other = []){

        $filter = [];
        $condition = [];
        $join = [];
        $extra = [];
        $count = 0;

        foreach(array_merge($this->MAINS, $this->HIDDEN) as $key => $val){

            if(isset($search_array[$key]) && $key != ":join"){

                if(is_array($search_array[$key])){

                    foreach($search_array[$key] as $cond){

                        if(is_array($cond) && isset($cond["value"]) && isset($cond["equ"]) && isset($cond["det"])){

                            $condition[] = [
                                "name" => $this->TABLE_NAME.".".$key,
                                "value" => $cond["value"],
                                "equ" => $cond["equ"],
                                "det" => $cond["det"]
                            ];

                        }

                    }

                }
                else if($search_array[$key] && !is_array($search_array[$key])){

                    $condition[] = [
                        "name" => $this->TABLE_NAME.".".$key,
                        "value" => $search_array[$key],
                        "equ" => "=",
                        "det" => "and"
                    ];

                }

                $filter[$count]["name"] = $key;
                $filter[$count]["table"] = $this->TABLE_NAME;

                unset($search_array[$key]);
                $count += 1;

            }

        }

        foreach($search_array[":join"] as $value){

            if(is_array($value)){

                $result = $this->prepareJoin($value);

                if($result["as"]){
                    $join[] = ["as" => $result["as"], "table" => $result["table"], "on" => $result["on"]];;
                }else{
                    $join[] = ["table" => $result["table"], "on" => $result["on"]];
                }

                $filter = array_merge($filter, $result["filter"]);

                if(isset($result["condition"]) && is_array($result["condition"])){
                    $condition = array_merge($condition, $result["condition"]);
                }

            }

        }

        if(isset($other["order_by"])){

            if(isset($other["order_by"]["att"]) && isset($this->MAINS[$other["order_by"]["att"]])){

                if(isset($other["order_by"]["det"]) && $other["order_by"]["det"] == "1" || $other["order_by"]["det"] == "0"){

                    $extra["order_by"]["att"] = $other["order_by"]["att"];
                    $extra["order_by"]["det"] = $other["order_by"]["det"];

                }

            }

        }

        if(isset($other["limit"]["start"]) && isset($other["limit"]["length"])){
            $extra["limit"]["start"] = intval($other["limit"]["start"]);
            $extra["limit"]["length"] = intval($other["limit"]["length"]);
        }

        $con = new Database($this->DATABASE, $this->DATABASE_NAME);
        $query = new JointSelection($this->TABLE_NAME, $filter, $join, $condition, $extra);

        $result_data = $con->executeInReturn($query);

        $data = [];
        while($row = $result_data->fetch()){
            $data[] = $row;
        }

        return $data;

    }

    /**
     * @param array $join
     * @return array
     * @throws OperationFailed
     */
    private function prepareJoin(Array $join){

        $return = [];

        if($join[":table"]){

            $name = $join[":table"];
            $model_name = "Application\\Models\\".$name."Model";
            $count = 0;

            /** @var SqlModel $model */
            $model = new $model_name;

            if($join[":on"] && $join[":parent"]){

                if(isset($model->MAINS[$join[":on"]])){

                    if(isset($this->MAINS[$join[":parent"]])){

                        $return["table"] = $name;
                        $return["as"] = $representation = $join[":as"] ? $join[":as"] : null;
                        $return["on"] = $this->TABLE_NAME.".".$join[":parent"]." = ".($representation ? $representation : $model->TABLE_NAME).".".$join[":on"];

                        foreach($model->MAINS as $key => $value){

                            if(isset($join[$key])) {

                                $return["filter"][$count]["name"] = $key;
                                $return["filter"][$count]["table"] = $representation ? $representation : $model->TABLE_NAME;

                                if(is_array($join[$key])){

                                    foreach ($join[$key] as $cond){

                                        if(is_array($cond) && isset($cond["value"]) && isset($cond["equ"]) && isset($cond["det"])){

                                            $return["condition"][] = [
                                                "name" => ($representation ? $representation : $model->TABLE_NAME).".".$key,
                                                "value" => $cond["value"],
                                                "equ" => $cond["equ"],
                                                "det" => $cond["det"]
                                            ];

                                        }

                                    }

                                }
                                else if($join[$key] && !is_array($join[$key])){
                                    $return["condition"][] = [
                                        "name" => ($representation ? $representation : $model->TABLE_NAME).".".$key,
                                        "value" => $join[$key],
                                        "equ" => "=",
                                        "det" => "and"
                                    ];
                                }

                                $count += 1;

                            }

                        }

                    }else{
                        throw new OperationFailed("Attribute ".$join[":parent"]." not found in $this->TABLE_NAME");
                    }

                }else{
                    throw new OperationFailed("Attribute ".$join[":on"]." not found in $model->TABLE_NAME");
                }

            }else{
                throw new OperationFailed(":on and :parent attributes must be set");
            }

        }else{
            throw new OperationFailed("Table name should be defined.");
        }

        return $return;

    }

    public function deleteColumn(){
        // TODO: Implement deleteColumn() method.
    }

    public function findRecord($key)
    {
        // TODO: Implement findRecord() method.
    }

    public function searchRecord()
    {
        // TODO: Implement searchRecord() method.
    }

    public function deleteRecord()
    {
        // TODO: Implement deleteRecord() method.
    }

    public function updateRecord()
    {
        // TODO: Implement updateRecord() method.
    }

    public function addRecord()
    {
        // TODO: Implement addRecord() method.
    }
}