<?php

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;


use Absoft\Line\Core\Modeling\Models\Model;

class RecordManipulation {

    private $condition;
    private $select;
    private $insert;
    private $from;
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

    public $query = "";
    public $values = [];


    function __construct(Model $model) {

        $this->model = $model;

        if($model){
            $this->from = "from " . $model->TABLE_NAME;
            $this->update = "update " . $model->TABLE_NAME;
            $this->delete = "delete from " . $model->TABLE_NAME;
            return $this;
        }

        return null;

    }

    function greater($column, $value){
        $this->term($column, $value, ">");
    }

    function less($column, $value, $conjunction = "and"){
        $this->term($column, $value, "<");
    }

    function orLess($column, $value){
        $this->term($column, $value, "<", "or");
    }

    function orGreater($column, $value){
        $this->term($column, $value, ">", "or");
    }

    function orTerm($column, $value){
        $this->term($column, $value, "or");
    }

    function where($column, $value){
        $this->term($column, $value);
    }

    function orWhere($column, $value){
        $this->term($column, $value, "=", "or");
    }

    function term($column, $value, $equation = "=", $conjunction = "and"){

        if(in_array($column, $this->model->MAINS)){

            if($this->counter["term"] == 0){
                $this->condition = "where " . $column . " " . $equation . " :param_" . $column . "_" . $this->counter["term"];
            }else{
                $this->condition .= " ". $conjunction. " " . $column . " " . $equation . " :param_" . $column . "_" . $this->counter["term"];
            }

            $this->values[":param_" . $column . "_" . $this->counter["term"]] = $value;
            $this->counter["term"] += 1;

        }

    }

    function like($column, $value) {
        $this->term($column, "%$value%", "like");
    }

    function orLike($column, $value){
        $this->term($column, "%$value%", "like", "or");
    }



    function set($column, $value){

        if(in_array($column, $this->model->HIDDEN)){

            if($this->counter["set"] == 0){
                $this->set = "set " . $column . " = " . ":set_" . $column . "_" . $this->counter["set"];
            }else{
                $this->set .= ", " . $column . " = " . ":set_" . $column . "_" . $this->counter["set"];
            }

            $this->values[":set_" . $column . "_" . $this->counter["set"]] = password_hash($value, PASSWORD_DEFAULT);
            $this->counter["set"] += 1;

        }else if(in_array($column, $this->model->MAINS)){

            if($this->counter["set"] == 0){
                $this->set = "set " . $column . " = " . ":set_" . $column . "_" . $this->counter["set"];
            }else{
                $this->set .= ", " . $column . " = " . ":set_" . $column . "_" . $this->counter["set"];
            }

            $this->values[":set_" . $column . "_" . $this->counter["set"]] = $value;
            $this->counter["set"] += 1;

        }

    }



    function filter(array $columns){

        $found = [];
        $str = $this->counter["filter"];
        foreach ($columns as $column){
            if(in_array($column, $this->model->MAINS)){
                $found[] = $column;
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
                $this->select = !$as ? "select $column as $as" : "select $column";
            }else{
                $this->select .= !$as ? ", $column as $as" : ", $column";
            }
            $this->counter["filter"] += 1;
        }

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
                $this->select = !$as ? "select $method($column) as $as" : "select $method($column)";
            }else{
                $this->select .= !$as ? ", $method($column) as $as" : ", $method($column)";
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



    function limit($offset, $start = 0){
        $this->limit = "limit " . ($start == null ? "" : $start . ", ") . $offset;
    }

    function order($column, $order){

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
        return ($this->select ?? "select *") . " " . ($this->from ?? "") . " " . ($this->condition ?? "") . " " . ($this->group ?? "") . " " . ($this->order ?? "") . " " . ($this->limit ?? "");
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