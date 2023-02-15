<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/19/2020
 * Time: 12:41 AM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;


use Absoft\Line\Core\DbConnection\QueryConstruction\Query;

class Creation implements Query {

    private $values = array();
    private $query;

    function __construct($name, $attributes){

        $size = sizeof($attributes);
        $foreign = [];
        $this->query = "create table  ".$name."(";

        $count = 0;

        foreach($attributes as $value){

            $type = $value->type;
            $this->query = $this->query . $value->getName(). " " .($type == "int" ? "INTEGER" : $type);

            if($value->key != "primary key" && $type != "date" && $type != "time" && $type != "text" && $type != "double" && $type != "timestamp"){
                $this->query = $this->query. "(".$value->getlength().") ";
            }

            if($value->unique){
                $this->query .= " UNIQUE ";
            }

            if(!$value->nullable && $value->key != "primary key"){
                $this->query .= " not null ";
            }

            if($value->key == "primary key" && !$value->nullable){
                $this->query = $this->query . " PRIMARY KEY";

                if($type == "int" && $value->auto_increment){
                    $this->query .= " autoincrement";
                }

            }else if($value->key == "foreign key"){
                $foreign[] = ["foreign_table" => $value->foreign, "reference" => $value->reference, "t_att" => $value->getName()];
            }


            if($type == "timestamp" && $count == ($size-1)){
                $this->query .= " default null";
            }

            if($count < ($size-1)){
                $this->query = $this->query . ", ";
            }

            $count += 1;

        }

        if(sizeof($foreign) > 0){

            foreach ($foreign as $ref){

                $this->query .= ", INDEX (".$ref["t_att"].")";
                $this->query .= ", FOREIGN KEY (".$ref["t_att"].") references ".$ref["foreign_table"]." (".$ref["reference"].")";

            }

        }

        $this->query = $this->query . ")";

    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }

}
