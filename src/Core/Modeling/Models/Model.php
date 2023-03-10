<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/28/2021
 * Time: 12:49 PM
 */

namespace Absoft\Line\Core\Modeling\Models;


use Absoft\Line\Core\DbConnection\Database;
use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\ControllersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Exception;

class Model implements ModelInterface
{

    /**
     * @var ModelInterface
     */
    private $model;
    public array $MAINS;
    public string $TABLE_NAME;
    public array $HIDDEN;
    public array $ASSOCIATION;
    public $DATABASE = "MySql";
    public string $DATABASE_NAME = "first";
    public array $RECORD = [];


    /**
     * Model constructor.
     * @throws DBConnectionError
     */
    public function __construct(){

        switch ($this->DATABASE){

            case "SQLite":
                $this->model = new SQLiteModel();
                $this->model->TABLE_NAME = $this->TABLE_NAME;
                $this->model->HIDDEN = $this->HIDDEN;
                $this->model->DATABASE_NAME = $this->DATABASE_NAME;
                $this->model->DATABASE = $this->DATABASE;
                $this->model->MAINS = $this->MAINS;
                $this->model->setDB();
                break;
            default:
                $this->model = new SQLModel();
                $this->model->TABLE_NAME = $this->TABLE_NAME;
                $this->model->HIDDEN = $this->HIDDEN;
                $this->model->DATABASE_NAME = $this->DATABASE_NAME;
                $this->model->DATABASE = $this->DATABASE;
                $this->model->MAINS = $this->MAINS;
                $this->model->setDB();
                break;

        }

    }

    /**
     * Undocumented function
     *
     * @param array $properties_array
     * @param boolean $is_hidden
     * @return void
     */
    public function addProperties(array $properties_array, $is_hidden = false){
        if($is_hidden){
            $this->HIDDEN = array_merge($this->HIDDEN, $properties_array);
        }else{
            $this->MAINS = array_merge($this->MAINS, $properties_array);
        }
    }

    public function addAssociationProperties(array $properties_array){
        $this->ASSOCIATION = array_merge($this->ASSOCIATION, $properties_array);
    }

    public function checkProperty($name, $is_hidden = false) {
        if($is_hidden){
            return in_array($name, $this->HIDDEN);
        }else{
            return in_array($name, $this->MAINS) || in_array($name, $this->ASSOCIATION);
        }
    }

    public function checkRecord($property_name) {
        return isset($this->RECORD[$property_name]);
    }

    /**
     * this method will return the value of property 
     * name by the given parameter
     *
     * @param string $property_name
     * @return mixed|null
     */
    public function getValue(string $property_name){
        
        if($this->checkRecord($property_name)){
            return $this->RECORD[$property_name];
        }

        return null;

    }

    public function removeValue($property_name){
        
    }

    /**
     * this method will set value to property found in the model
     *
     * @param string $property_name
     * @param $value
     * @return void
     */
    public function setProperty(string $property_name, $value) {

        if($this->checkProperty($property_name)){
            $this->RECORD[$property_name] = $value;
        }

    }

    /**
     * this method will set values to properties in the model
     * which is given in associative array
     *
     * @param array $properties_value
     * this parameter should be associative-array 
     * of properties with their value
     * @return void
     */
    public function setProperties(array $properties_value){
        foreach($properties_value as $property_name => $value) {
            if($this->checkProperty($property_name)){
                $this->RECORD[$property_name] = $value;
            }
        }
    }

    /**
     * will returns all the properties in the model
     *
     * @return array
     */
    public function getProperties(){
        return array_merge($this->MAINS, $this->HIDDEN);
    }

    public function __set($name, $value){
        $this->setProperty($name, $value);
    }

    public function __get($name){
        return $this->getValue($name);
    }

    public function __isset($name){
        return $this->checkProperty($name);
    }

    public function __unset($name){
        $this->removeRecord($name);
    }

    /**
     * @param $key
     * @return array|mixed
     * @throws OperationFailed
     */
    public function findRecord($key){

        try{

            $pk = $this->getEntity()->PRIMARY_KEY;
            $query = $this->searchRecord();
            $query->where($pk, $key);
            $result = $query->fetch();

            return $result->rowCount() > 0 ? $result->fetch() : [];

        } catch (Exception $e) {
            throw new OperationFailed($e->getMessage());
        }

    }

    public function searchRecord() {
        return $this->model->searchRecord();
    }

    public function deleteRecord() {
        return $this->model->deleteRecord();
    }

    public function updateRecord() {
        return $this->model->updateRecord();
    }

    public function addRecord() {
        return $this->model->addRecord();
    }

    public function query(string $query_string) {
        return $this->model->query($query_string);
    }

    /**
     * @param Query $query
     * @return bool
     * @throws DBConnectionError|ExecutionException
     */
    function executeUpdate(Query $query):bool {
        return $this->model->executeUpdate($query);
    }

    /**
     * @param Query $query
     * @return bool|\PDOStatement
     * @throws DBConnectionError|ExecutionException
     */
    function execute(Query $query) {
        return $this->model->execute($query);
    }

    /**
     * @param Query $query
     * @return bool|\PDOStatement
     * @throws DBConnectionError|ExecutionException
     */
    function fetch(Query $query):\PDOStatement {
        return $this->model->fetch($query);
    }

    /**
     * @param array $search_array
     * @param array $other
     * @return bool|\PDOStatement
     * @throws OperationFailed|DBConnectionError|ExecutionException
     */
    public function advancedSearch(Array $search_array, $other = []){
        return $this->model->advancedSearch($search_array, $other);
    }

    /**
     * @return mixed
     * @throws ClassNotFound|ControllersFolderNotFound
     */
    function getEntity(){
        return $this->model->getEntity();
    }

    /**
     * @param null $db
     * @throws DBConnectionError
     */
    public function setDB($db = null){
        $this->DATABASE == "MySql" ? $this->model->setDB($db) : null;
    }

    /**
     * @return Database|null
     * @throws DBConnectionError
     */
    public function getDB(){
        return $this->DATABASE == "MySql" ? $this->model->getDB() : null;
    }

    public function beginTransaction($db = null){
        return $this->DATABASE == "MySql" ? $this->model->beginTransaction($db) : null;
    }

    public function commit(){
        return $this->DATABASE == "MySql" ? $this->model->commit() : null;
    }

    public function rollback(){
        return $this->DATABASE == "MySql" ? $this->model->rollback() : null;
    }

    /**
     * @return int|string|null
     */
    public function lastInsertId(){
        return $this->DATABASE == "MySql" ? $this->model->lastInsertId() : null;
    }

    public function getModel(){
        return $this->model;
    }

}
