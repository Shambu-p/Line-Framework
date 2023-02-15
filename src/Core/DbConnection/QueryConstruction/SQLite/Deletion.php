<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/19/2020
 * Time: 11:12 AM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;

class Deletion implements Query {

    public $query = "";
    public $values = array();
    private $query_constructor;
    private $model;

    function __construct($model){

        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);

    }

    function __call($method, $argument){

        switch ($method){
            case "where":
                $this->query_constructor->where($argument[0], $argument[1]);
                break;
            case "orWhere":
                $this->query_constructor->orWhere($argument[0], $argument[1]);
                break;
            case "like":
                $this->query_constructor->like($argument[0], $argument[1]);
                break;
            case "orLike":
                $this->query_constructor->orLike($argument[0], $argument[1]);
                break;
            case "less":
                $this->query_constructor->less($argument[0], $argument[1]);
                break;
            case "orLess":
                $this->query_constructor->orLess($argument[0], $argument[1]);
                break;
            case "greater":
                $this->query_constructor->greater($argument[0], $argument[1]);
                break;
            case "orGreater":
                $this->query_constructor->orGreater($argument[0], $argument[1]);
                break;
            case "limit":
                $this->query_constructor->limit($argument[0], null);
                break;
        }

    }

    function delete(){
        $this->query = $this->query_constructor->fetchDelete();
        $this->values = $this->query_constructor->getValues();
        $this->model->executeUpdte($this);
    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }

}
