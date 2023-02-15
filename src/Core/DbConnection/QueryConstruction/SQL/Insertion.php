<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/18/2020
 * Time: 10:19 PM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQL;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\Modeling\Models\ModelInterface;

class Insertion implements Query {

    private $query = "";
    private $values = [];
    private $model;
    private $query_constructor;

    function __construct(ModelInterface $model){
        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);
    }

    /**
     * @param array $array
     */
    function add(array $array){
        $this->query_constructor->add($array);
    }

    /**
     * @return bool|\PDOStatement
     * @throws DBConnectionError|ExecutionException
     */
    function insert() {
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
