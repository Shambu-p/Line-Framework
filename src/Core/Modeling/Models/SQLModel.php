<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 3/1/2021
 * Time: 11:11 AM
 */

namespace Absoft\Line\Core\Modeling\Models;

use Absoft\Line\App\Files\Resource;
use Absoft\Line\Core\DbConnection\Database;
use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\Deletion;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\Insertion;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\JointSelection;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\Selection;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\Update;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\WriteQuery;
use Absoft\Line\Core\FaultHandling\Errors\ControllersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\DbBuilders\Builder;
use PDOStatement;

class SQLModel implements ModelInterface {

    public array $MAINS;
    public string $TABLE_NAME;
    public array $HIDDEN;
    public string $DATABASE;
    public string $DATABASE_NAME;

    /** @var Database|null  */
    public $DATABASE_OBJECT = null;

    /**
     * @param Database|null $db
     * @throws DBConnectionError
     */
    public function setDB($db = null) {
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
     * @throws ControllersFolderNotFound
     */
    function getEntity(){

        $entity_name = Resource::searchObject($this->TABLE_NAME.".php", "builders");
        return new $entity_name;

    }

    /**
     * @param array $search_array
     * @param array $other
     * @return array|bool|PDOStatement
     * @throws DBConnectionError
     * @throws OperationFailed|ExecutionException
     */
    public function advancedSearch(Array $search_array, $other = []) {

        $filter = [];
        $condition = [];
        $join = [];
        $extra = [];
        $count = 0;

        foreach(array_merge($this->MAINS, $this->HIDDEN) as $key) {

            if(isset($search_array[$key]) && $key != ":join") {

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

        foreach($search_array[":join"] as $value) {

            if(is_array($value)) {

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

            if(isset($other["order_by"]["att"]) && in_array($other["order_by"]["att"], $this->MAINS)){

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

        if(!$result_data){
            return [];
        }

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

                if(in_array($join[":on"], $model->MAINS)){

                    if(in_array($join[":parent"], $this->MAINS)){

                        $return["table"] = $name;
                        $return["as"] = $representation = $join[":as"] ? $join[":as"] : null;
                        $return["on"] = $this->TABLE_NAME.".".$join[":parent"]." = ".($representation ? $representation : $model->TABLE_NAME).".".$join[":on"];

                        foreach($model->MAINS as $key){

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

    /**
     * @param Query $query
     * @return bool|PDOStatement
     * @throws DBConnectionError|ExecutionException
     */
    public function execute(Query $query) {
        $con = $this->DATABASE_OBJECT ? $this->DATABASE_OBJECT : new Database($this->DATABASE, $this->DATABASE_NAME);
        return $con->execute($query);
    }

    public function searchRecord():Selection {
        return new Selection($this);
    }

    public function deleteRecord():Deletion {
        return new Deletion($this);
    }

    public function updateRecord():Update {
        return new Update($this);
    }

    public function addRecord():Insertion {
        return new Insertion($this);
    }

    public function query(string $query_string): WriteQuery {
        return new WriteQuery($this, $query_string);
    }

    public function findRecord($key) {}

}
