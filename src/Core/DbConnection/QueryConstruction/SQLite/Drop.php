<?php


namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;


use Absoft\Line\Core\DbConnection\QueryConstruction\Query;

class Drop implements Query
{

    private $query;
    private $values = [];

    function __construct($table_name){

        $this->query = "drop table ".$table_name;

    }

    function getQuery()
    {
        return $this->query;
    }

    function getValues()
    {
        return $this->values;
    }
}