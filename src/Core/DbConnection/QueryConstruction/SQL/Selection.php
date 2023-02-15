<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/18/2020
 * Time: 9:44 PM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQL;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Models\Model;
use Absoft\Line\Core\Modeling\Models\ModelInterface;

class Selection implements Query {

    private $values = array();
    private $query = "";
    private $query_constructor;
    private $model;

    function __construct(ModelInterface $model) {
        $this->model = $model;
        $this->query_constructor = new RecordManipulation($model);
    }

    /**
     * @return bool|\PDOStatement
     * @throws DBConnectionError|ExecutionException
     */
    function fetch(){
        $this->query = $this->query_constructor->fetchSelection();
        $this->values = $this->query_constructor->getValues();
        return $this->model->execute($this);
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

    function orWhere($column, $value, $escape_value = false){
        $this->query_constructor->orWhere($column, $value, $escape_value);
    }

    function greater($column, $value, $escape_value = false){
        $this->query_constructor->greater($column, $value, $escape_value);
    }

    function less($column, $value, $escape_value = false){
        $this->query_constructor->less($column, $value, $escape_value);
    }

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

    /**
     * @param $model_full_name
     *          name of the model object including namespace and name of the class
     *          such as Application\\Models\\BranchModel
     * @param $model_column_name
     *          column name in the current model object which this query is being build on
     * @param $join_column_name
     *          column name in the model which is being joined
     * @param $as
     *          alias name for the model table name it will be used one every selected column name
     *          by default it is the name of the table it self
     *
     *          on fetch it will be returned like this 'tableName_columnName'
     * @throws OperationFailed
     */
    function join($model_full_name, $model_column_name, $join_column_name, $as = null) {
        $this->query_constructor->join("", $model_full_name, $model_column_name, $join_column_name, $as);
    }

    /**
     * @param $model_full_name
     *          name of the model object including namespace and name of the class
     *          such as Application\\Models\\BranchModel
     * @param $model_column_name
     *          column name in the current model object which this query is being build on
     * @param $join_column_name
     *          column name in the model which is being joined
     * @param $as
     *          alias name for the model table name it will be used one every selected column name
     *          by default it is the name of the table it self
     *
     *          on fetch it will be returned like this 'tableName_columnName'
     * @throws OperationFailed
     */
    function leftJoin($model_full_name, $model_column_name, $join_column_name, $as = null) {
        $this->query_constructor->leftJoin($model_full_name, $model_column_name, $join_column_name, $as);
    }

    /**
     * @param $model_full_name
     *          name of the model object including namespace and name of the class
     *          such as Application\\Models\\BranchModel
     * @param $model_column_name
     *          column name in the current model object which this query is being build on
     * @param $join_column_name
     *          column name in the model which is being joined
     * @param $as
     *          alias name for the model table name it will be used one every selected column name
     *          by default it is the name of the table it self
     *
     *          on fetch it will be returned like this 'tableName_columnName'
     * @throws OperationFailed
     */
    function rightJoin($model_full_name, $model_column_name, $join_column_name, $as = null) {
        $this->query_constructor->rightJoin($model_full_name, $model_column_name, $join_column_name, $as);
    }

    /**
     * @param $model_full_name
     *          name of the model object including namespace and name of the class
     *          such as Application\\Models\\BranchModel
     * @param $model_column_name
     *          column name in the current model object which this query is being build on
     * @param $join_column_name
     *          column name in the model which is being joined
     * @param $as
     *          alias name for the model table name it will be used one every selected column name
     *          by default it is the name of the table it self
     *
     *          on fetch it will be returned like this 'tableName_columnName'
     * @throws OperationFailed
     */
    function innerJoin($model_full_name, $model_column_name, $join_column_name, $as = null) {
        $this->query_constructor->innerJoin($model_full_name, $model_column_name, $join_column_name, $as);
    }

    /**
     * @param $model_full_name
     *          name of the model object including namespace and name of the class
     *          such as Application\\Models\\BranchModel
     * @param $model_column_name
     *          column name in the current model object which this query is being build on
     * @param $join_column_name
     *          column name in the model which is being joined
     * @param $as
     *          alias name for the model table name it will be used one every selected column name
     *          by default it is the name of the table it self
     *
     *          on fetch it will be returned like this 'tableName_columnName'
     * @throws OperationFailed
     */
    function outerJoin($model_full_name, $model_column_name, $join_column_name, $as = null) {
        $this->query_constructor->outerJoin($model_full_name, $model_column_name, $join_column_name, $as);
    }

    function filter(array $columns) {
        $this->query_constructor->filter($columns);
    }

    function select($column, $as = null){
        $this->query_constructor->select($column, $as);
    }

    function min($column, $as = null){
        $this->query_constructor->min($column, $as);
    }

    function count($column = "*", $as = null){
        $this->query_constructor->count($column, $as);
    }

    function max($column, $as = null){
        $this->query_constructor->max($column, $as);
    }

    function sum($column, $as = null){
        $this->query_constructor->sum($column, $as);
    }

    function average($column, $as = null){
        $this->query_constructor->average($column, $as);
    }

    function limit($offset, $start = 0){
        $this->query_constructor->limit($offset, $start);
    }

    function order($column, $order = true){
        $this->query_constructor->order($column, $order);
    }

    function group($column){
        $this->query_constructor->group($column);
    }


    function getQuery(){
        return $this->query;
    }

    function getValues(){
        return $this->values;
    }
}
