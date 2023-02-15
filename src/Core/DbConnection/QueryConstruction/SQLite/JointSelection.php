<?php


namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;


use Absoft\Line\Core\DbConnection\QueryConstruction\Query;

class JointSelection implements Query {

    private $query = "";
    private $values = [];

    function __construct($table, $filter, $joins, $condition = [], $limit = []){

        $this->select($filter);
        $this->from($table);

        foreach($joins as $join){
            $this->join($join);
        }

        //print_r($condition);
        if(sizeof($condition) > 0){
            $this->where($condition);
        }

        if(isset($other["order_by"]) && isset($other["order_by"]["att"])){
            $this->orderBy($other["order_by"]);
        }

        if(isset($limit["start"]) && isset($limit["length"])){
            $this->query .= " limit ".$limit["start"].", ".$limit["length"];
        }

    }

    private function select($filter){

        //select column1, column2, column3 from table_name, table2, table3 where column = value
        $this->query = "select ";

        if(sizeof($filter) == 0){
            $this->query .= "*";
            return;
        }

        for($i = 0; $i < sizeof($filter); $i ++){
            if($i == 0){
                $this->query = $this->query.$filter[$i]["table"].".".$filter[$i]["name"]." as ".$filter[$i]["table"]."_".$filter[$i]["name"];
            }else{
                $this->query = $this->query.", ".$filter[$i]["table"].".".$filter[$i]["name"]." as ".$filter[$i]["table"]."_".$filter[$i]["name"];
            }
        }

    }

    private function from($table){
        $this->query = $this->query." from ". $table;
    }

    private function join(Array $join_array){

        if(isset($join_array["as"])){
            $this->query .= " left join ".$join_array["table"]." as ".$join_array["as"]." on ".$join_array["on"];
            return;
        }

        $this->query .= " left join ".$join_array["table"]." on ".$join_array["on"];

    }

    private function where($condition){

        $this->query .= " where ";
        $this->query .= QueryConstructor::conditionDerdari($condition);
        $cnt = [];

        foreach($condition as $value){

            $name = str_replace(".", "_", $value["name"]);

            if(isset($cnt[$name])){
                $cnt[$name] += 1;
            }else{
                $cnt[$name] = 1;
            }

            $name = "condition_".$cnt[$name].$name;
            $this->values[$name] = $value['value'];

        }

    }

    function orderBy($order_by){

        /*

        $array = [
            "att" => "column",
            "det" => 1
        ]

         */

        $this->query .= " order by ".$order_by["att"];

        if($order_by["det"] == "1"){
            $this->query .= " asc";
        }else{
            $this->query .= " desc";
        }

    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }
}
