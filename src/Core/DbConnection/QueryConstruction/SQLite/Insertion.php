<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/18/2020
 * Time: 10:19 PM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQLite;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\Modeling\Models\Model;

class Insertion implements Query {

    private $query = "";
    private $values = [];
    private $model;
    private $query_constructor;

    function __construct(Model $model){
        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);
    }

    function __call($method, $argument){

        switch ($method){
            case "add":
                $this->query_constructor->add($argument[0]);
                break;
        }

    }

    /**
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function insert(){
        $this->query = $this->query_constructor->fetchInsertion();
        $this->values = $this->query_constructor->values;
        return $this->model->execute($this);
    }

    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }

}
