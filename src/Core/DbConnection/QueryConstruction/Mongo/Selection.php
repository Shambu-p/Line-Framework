<?php
/**
 * @author Abnet Kebede
 * @meta time: 10:33 am date: sunday april 4 2013 E.C.
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\Mongo;


use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Driver\Query as MongoQuery;

class Selection implements Query
{

    private $query = null;
    private $values = [];

    function __construct($table, $filter){

        $this->query = new MongoQuery($filter);
        $this->values["table"] = $table;

    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }

}