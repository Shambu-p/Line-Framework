<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/19/2020
 * Time: 11:12 AM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQL;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\Modeling\Models\ModelInterface;

class Deletion implements Query {

    public $query = "";
    public $values = array();
    private $query_constructor;
    private $model;

    function __construct(ModelInterface $model){

        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);

    }

    /**
     * @param $column
     *          column name
     * @param $value
     *          value to be compared with
     * @param bool $escape_value
     *          set true if the value wanted to be set as it is or if the value is query
     */
    function where($column, $value, $escape_value = false){
        $this->query_constructor->where($column, $value, $escape_value);
    }

    /**
     * @param $column
     *          column name
     * @param $value
     *          value to be compared with
     * @param bool $escape_value
     *          set true if the value wanted to be set as it is or if the value is query
     */
    function orWhere($column, $value, $escape_value = false){
        $this->query_constructor->orWhere($column, $value, $escape_value);
    }

    /**
     * where condition to check the column name defined by
     * the value passed on column parameter, is greater than
     * the value passed by the value parameter
     * @param $column
     *          column name
     * @param $value
     *          value to be compared with
     * @param bool $escape_value
     *          set true if the value wanted to be set as it is or if the value is query
     */
    function greater($column, $value, $escape_value = false){
        $this->query_constructor->greater($column, $value, $escape_value);
    }

    /**
     * where condition to check the column name defined by
     * the value passed on column parameter, is less than
     * the value passed by the value parameter
     * @param $column
     *          column name
     * @param $value
     *          value to be compared with
     * @param bool $escape_value
     *          set true if the value wanted to be set as it is or if the value is query
     */
    function less($column, $value, $escape_value = false){
        $this->query_constructor->less($column, $value, $escape_value);
    }

    /**
     * where condition to check the column name defined by
     * the value passed on column parameter, is less than
     * the value passed by the value parameter
     * @param $column
     *          column name
     * @param $value
     *          value to be compared with
     * @param bool $escape_value
     *          set true if the value wanted to be set as it is or if the value is query
     */
    function orLess($column, $value, $escape_value = false){
        $this->query_constructor->orLess($column, $value, $escape_value);
    }

    function orGreater($column, $value, $escape_value = false){
        $this->query_constructor->orGreater($column, $value, $escape_value);
    }

    function like($column, $value) {
        $this->query_constructor->like($column, $value);
    }

    function orLike($column, $value) {
        $this->query_constructor->like($column, $value);
    }

    function limit($offset){
        $this->query_constructor->limit($offset, null);
    }

    /**
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function delete(){
        $this->query = $this->query_constructor->fetchDelete();
        $this->values = $this->query_constructor->getValues();
        $this->model->execute($this);
    }

    function getQuery() {
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }

}
