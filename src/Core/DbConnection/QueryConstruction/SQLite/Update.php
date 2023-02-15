<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/19/2020
 * Time: 10:30 AM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\Modeling\Models\Model;

class Update implements Query {

    private $query = "";
    private $values = [];
    private $query_constructor;
    private $model;

    function __construct(Model $model){
        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);
    }

    function __call($method, $argument){

        switch ($method){
            case "set":
                $this->query_constructor->set($argument[0], $argument[1]);
                break;
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
                $this->query_constructor->limit($argument[0]);
                break;
            case "order":
                $this->query_constructor->order($argument[0], (isset($argument[1]) ? $argument[1] : true));
                break;
        }

    }

    /**
     * @return bool
     * @throws DBConnectionError
     */
    function update():bool {
        $this->query = $this->query_constructor->fetchUpdate();
        $this->values = $this->query_constructor->getValues();
        return $this->model->executeUpdate($this);
    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }
}
