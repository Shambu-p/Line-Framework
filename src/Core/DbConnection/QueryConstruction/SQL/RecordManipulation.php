<?php

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQL;


use Absoft\Line\App\Files\Resource;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Models\Model;
use Absoft\Line\Core\Modeling\Models\ModelInterface;

class RecordManipulation {

    private $condition;
    private $select;
    private $insert;
    private $from;
    private $join;
    private $update;
    private $set;
    private $order;
    private $group;
    private $limit;
    private $delete;
    private $counter = [
        "filter" => 0,
        "set" => 0,
        "term" => 0,
        "order" => 0,
        "insert" => 0
    ];

    /**
     * @var Model
     */
    private $model;

    /**
     * @var Model
     */
    private $join_model = null;
    private $join_alias;

    public $query = "";
    public $values = [];


    function __construct(ModelInterface $model) {

        $this->model = $model;

        if($model){
            $this->from = "from " . $model->TABLE_NAME;
            $this->update = "update " . $model->TABLE_NAME;
            $this->delete = "delete from " . $model->TABLE_NAME;
            return $this;
        }

        return null;

    }

    function greater($column, $value, $escape_value = false){
        $this->term($column, $value, ">", "and", $escape_value);
    }

    function less($column, $value, $escape_value = false){
        $this->term($column, $value, "<", "and", $escape_value);
    }

    function orLess($column, $value, $escape_value = false){
        $this->term($column, $value, "<", "or", $escape_value);
    }

    function orGreater($column, $value, $escape_value = false){
        $this->term($column, $value, ">", "or", $escape_value);
    }

    function where($column, $value, $escape_value = false){
        $this->term($column, $value, "=", "and", $escape_value);
    }

    function orWhere($column, $value, $escape_value = false){
        $this->term($column, $value, "=", "or", $escape_value);
    }

    function term($column, $value, $equation = "=", $conjunction = "and", $escape_value = false){

        if(in_array($column, $this->model->MAINS)){

            if($this->counter["term"] == 0){
                $this->condition = "where " . $column . " " . $equation . ($escape_value ? " $value" : (" :param_" . $column . "_" . $this->counter["term"]));
            }else{
                $this->condition .= " ". $conjunction. " " . $column . " " . $equation . ($escape_value ? " $value" : (" :param_" . $column . "_" . $this->counter["term"]));
            }

            if(!$escape_value){
                $this->values[":param_" . $column . "_" . $this->counter["term"]] = $value;
                $this->counter["term"] += 1;
            }

        }

    }

    function like($column, $value) {
        $this->term($column, "%$value%", "like");
    }

    function orLike($column, $value) {
        $this->term($column, "%$value%", "like", "or");
    }



    function set($column, $value, $escape_value = false){

        if($this->counter["set"] == 0){
            $set = "set " . $column . " = " . ($escape_value ? $value : (":set_" . $column . "_" . $this->counter["set"]));
        }else{
            $set = $this->set . ", " . $column . " = " . ($escape_value ? $value : (":set_" . $column . "_" . $this->counter["set"]));
        }

        if(in_array($column, $this->model->HIDDEN)) {

            $this->set = $set;

            if(!$escape_value){
                $this->values[":set_" . $column . "_" . $this->counter["set"]] = password_hash($value, PASSWORD_DEFAULT);
                $this->counter["set"] += 1;
            }

        }else if(in_array($column, $this->model->MAINS)) {

            $this->set = $set;

            if(!$escape_value) {
                $this->values[":set_" . $column . "_" . $this->counter["set"]] = $value;
                $this->counter["set"] += 1;
            }

        }

        unset($set);

    }



    function filter(array $columns){

        $found = [];
        $str = $this->counter["filter"];
        foreach ($columns as $column){

            if(in_array($column, $this->model->MAINS)) {
                $found[] = $column;
                $this->counter["filter"] += 1;
            }
            else if($this->join_model && in_array($column, $this->join_model->MAINS)) {
                $found[] = $this->join_model->TABLE_NAME.".$column as ".($this->join_alias ?? $this->join_model->TABLE_NAME."_".$column);
                $this->counter["filter"] += 1;
            }

        }

        if($this->counter["filter"] == 0){
            $this->select = "select *";
            return;
        }

        if($str == 0){
            $this->select .= "select " . implode(", ", $found);
        }else{
            $this->select .= ", " . implode(", ", $found);
        }

    }

    function select($column, $as = null){

        if(in_array($column, array_merge($this->model->MAINS, $this->model->HIDDEN))){
            if($this->counter["filter"] == 0){
                $this->select = $as ? "select $column as $as" : "select $column";
            }else{
                $this->select .= $as ? ", $column as $as" : ", $column";
            }
            $this->counter["filter"] += 1;
        }
        else if($this->join_model && in_array($column, array_merge($this->join_model->MAINS, $this->join_model->HIDDEN))) {

            $alias = $this->join_alias ?? $this->join_model->TABLE_NAME;
            if($this->counter["filter"] == 0) {
                $this->select = $as ? "select $alias.$column as $as" : "select $alias.$column as $alias"."_".$column;
            } else {
                $this->select .= $as ? ", $alias.$column as $as" : ", $alias.$column as $alias"."_".$column;
            }

            $this->counter["filter"] += 1;

        }

    }

    function selectAll(){

        if($this->join_model) {

            foreach(array_merge($this->model->MAINS, $this->model->HIDDEN) as $column) {
                if($this->counter["filter"] == 0) {
                    $this->select = "select $column";
                }else{
                    $this->select .= ", $column";
                }
                $this->counter["filter"] += 1;
            }

            foreach(array_merge($this->join_model->MAINS, $this->join_model->HIDDEN) as $column) {
                $alias = $this->join_alias ?? $this->join_model->TABLE_NAME;
                if($this->counter["filter"] == 0) {
                    $this->select = "select $alias.$column as $alias"."_".$column;
                } else {
                    $this->select .= ", $alias.$column as $alias"."_".$column;
                }
                $this->counter["filter"] += 1;
            }

        }else{
            $this->select = "select *";
        }

        return $this->select;

    }

    function min($column, $as = null){
        $this->aggregate($column, "min", $as);
    }

    function count($column = "*", $as = null){
        $this->aggregate($column, "count", $as);
    }

    private function aggregate($column, $method, $as = null){

        if(in_array($column, array_merge($this->model->MAINS, $this->model->HIDDEN))){

            if($this->counter["filter"] == 0){
                $this->select = $as ? "select $method($column) as $as" : "select $method($column)";
            } else {
                $this->select .= $as ? ", $method($column) as $as" : ", $method($column)";
            }

            $this->counter["filter"] += 1;

        }
        else if($this->join_model && in_array($column, array_merge($this->join_model->MAINS, $this->join_model->HIDDEN))) {

            if($this->counter["filter"] == 0) {
                $this->select = $as ? "select $method(".$this->join_model->TABLE_NAME.".$column) as $as" : "select $method(".$this->join_model->TABLE_NAME.".$column)";
            } else {
                $this->select .= $as ? ", $method(".$this->join_model->TABLE_NAME.".$column) as $as" : ", $method(".$this->join_model->TABLE_NAME.".$column)";
            }

            $this->counter["filter"] += 1;

        }

    }

    function max($column, $as){
        $this->aggregate($column, "max", $as);
    }

    function sum($column, $as = null){
        $this->aggregate($column, "sum", $as);
    }

    function average($column, $as = null){
        $this->aggregate($column, "avg", $as);
    }

    /**
     * @param $join_type
     * @param $model_full_name
     * @param $main_model_column
     * @param $join_model_column
     * @param null|string $as
     * @throws OperationFailed
     */
    function join($join_type, $model_full_name, $main_model_column, $join_model_column, $as = null) {

        try{

            $this->join_model = new $model_full_name;
            if(in_array($main_model_column, $this->model->MAINS) && in_array($join_model_column, $this->join_model->MAINS)){

                $table_name = $this->join_model->TABLE_NAME;
                $alias = $this->join_alias = ($as ?? $this->join_model->TABLE_NAME);

                $this->join = "$join_type join $table_name ".($as ? "as $alias":"")." on ".$this->model->TABLE_NAME.".$main_model_column = $alias.$join_model_column ";

            }else{
                $this->join_model = null;
            }

        }catch(\Exception $exception){
            throw new OperationFailed($exception->getMessage());
        }

    }

    /**
     * @param $model_full_name
     * @param $main_model_column
     * @param $join_model_column
     * @param null $as
     * @throws OperationFailed
     */
    function leftJoin($model_full_name, $main_model_column, $join_model_column, $as = null){
        $this->join("left", $model_full_name, $main_model_column, $join_model_column, $as);
    }

    /**
     * @param $model_full_name
     * @param $main_model_column
     * @param $join_model_column
     * @param null $as
     * @throws OperationFailed
     */
    function rightJoin($model_full_name, $main_model_column, $join_model_column, $as = null){
        $this->join("right", $model_full_name, $main_model_column, $join_model_column, $as);
    }

    /**
     * @param $model_full_name
     * @param $main_model_column
     * @param $join_model_column
     * @param null $as
     * @throws OperationFailed
     */
    function innerJoin($model_full_name, $main_model_column, $join_model_column, $as = null){
        $this->join("inner", $model_full_name, $main_model_column, $join_model_column, $as);
    }

    /**
     * @param $model_full_name
     * @param $main_model_column
     * @param $join_model_column
     * @param null $as
     * @throws OperationFailed
     */
    function outerJoin($model_full_name, $main_model_column, $join_model_column, $as = null){
        $this->join("full outer", $model_full_name, $main_model_column, $join_model_column, $as);
    }



    function limit($offset, $start = 0){
        $this->limit = "limit " . ($start == null ? "" : $start . ", ") . $offset;
    }

    function order($column, $order) {

        if($this->counter["order"] == 0){
            $this->order = "order by " . $column . " " . ($order ? "ASC" : "DESC");
            $this->counter["order"] += 1;
        }else{
            $this->order .= ", " . $column . " " . ($order ? "ASC" : "DESC");
            $this->counter["order"] += 1;
        }

    }

    function group($column){
        $this->group = "group by $column";
    }

    function add(array $columns){

        if(!is_array($columns)){
            return;
        }

        $found = [];
        $inserts = [];
        $merge = array_merge($this->model->MAINS, $this->model->HIDDEN);

        foreach ($columns as $column => $value){

            if(in_array($column, $merge)){

                if($this->counter["insert"] == 0 && !in_array($column, $inserts)){
                    $inserts[] = $column;
                }

                if(in_array($column, $this->model->HIDDEN)){
                    $found[] = ":insert_" . $column . "_" . $this->counter["insert"];
                    $this->values[":insert_" . $column . "_" . $this->counter["insert"]] = password_hash($value, PASSWORD_DEFAULT);
                }else{
                    $found[] = ":insert_" . $column . "_" . $this->counter["insert"];
                    $this->values[":insert_" . $column . "_" . $this->counter["insert"]] = $value;
                }

            }

        }

        if($this->counter["insert"] == 0){
            $this->insert = "insert" . " into " . $this->model->TABLE_NAME . " (" . implode(", ", $inserts) . ") values (" . implode(", ", $found) . ")";
        }else{
            $this->insert .= ", (" . implode(", ", $found) . ")";
        }

        $this->counter["insert"] += 1;

    }



    function fetchSelection():string {

        if(empty($this->select)){
            $this->selectAll();
        }

        return $this->select . " " . ($this->from ?? "") . " " . $this->join . " " . ($this->condition ?? "") . " " . ($this->group ?? "") . " " . ($this->order ?? "") . " " . ($this->limit ?? "");

    }

    function fetchUpdate():string {
        return $this->update . " " . ($this->set ?? "") . " " . ($this->condition ?? "") . " " . ($this->order ?? "") . ($this->limit ?? "");
    }

    function fetchDelete():string {
        return $this->delete . " " . ($this->condition ?? "") . " " . ($this->limit ?? "");
    }

    function fetchInsertion():string {
        return $this->insert;
    }

    function getValues(){
        return $this->values;
    }

}