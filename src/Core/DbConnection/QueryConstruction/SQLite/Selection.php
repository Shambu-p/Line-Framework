<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/18/2020
 * Time: 9:44 PM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\Modeling\Models\Model;

class Selection implements Query {

    private $values = array();
    private $query = "";
    private $query_constructor;
    private $model;

    function __construct(Model $model){
        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);
    }

    /**
     * @throws DBConnectionError
     */
    function fetch(){
        $this->query = $this->query_constructor->fetchSelection();
        $this->values = $this->query_constructor->getValues();
        return $this->model->fetch($this);
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
                $this->query_constructor->limit($argument[0], $argument[1]);
                break;
            case "filter":
                $this->query_constructor->filter($argument[0]);
                break;
            case "count":
                $this->query_constructor->count($argument[0], (isset($argument[1]) ? $argument[1] : null));
                break;
            case "min":
                $this->query_constructor->min($argument[0], (isset($argument[1]) ? $argument[1] : null));
                break;
            case "select":
                $this->query_constructor->select($argument[0], (isset($argument[1]) ? $argument[1] : null));
                break;
            case "max":
                $this->query_constructor->max($argument[0], (isset($argument[1]) ? $argument[1] : null));
                break;
            case "sum":
                $this->query_constructor->sum($argument[0], (isset($argument[1]) ? $argument[1] : null));
                break;
            case "average":
                $this->query_constructor->average($argument[0], (isset($argument[1]) ? $argument[1] : null));
                break;
            case "order":
                $this->query_constructor->order($argument[0], (isset($argument[1]) ? $argument[1] : true));
                break;
            case "group":
                $this->query_constructor->group($argument[0]);
                break;
        }

    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }
}
