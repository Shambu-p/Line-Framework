<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 12/7/2019
 * Time: 9:15 AM
 */

namespace Absoft\Line\Core\DbConnection\DataBases\MySQL;

use Absoft\Line\Core\DbConnection\Database;
use Absoft\Line\Core\DbConnection\DataBases\Connection;
use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Exception;
use PDO;
use \PDOStatement;

class MySql implements Connection {

    public $HOST_ADDRESS = null;
    public $DB_NAME = null;
    private $DB_USERNAME;
    private $DB_PASSWORD;
    private $DB;

    /**
     * MySql constructor.
     * @param array $db_info
     * @throws DBConnectionError
     */
    function __construct(array $db_info){

        $this->HOST_ADDRESS = $db_info['HOST_ADDRESS'];
        $this->DB_NAME = $db_info['DB_NAME'];
        $this->DB_USERNAME = $db_info['DB_USERNAME'];
        $this->DB_PASSWORD = $db_info['DB_PASSWORD'];

        $this->DB = $this->getConnection();

    }

    /**
     * @return PDO|null
     * @throws DBConnectionError
     */
    function getConnection(){

        try{

            $dns = "mysql:host=". $this->HOST_ADDRESS.";dbname=".$this->DB_NAME;
            $pdo = new PDO($dns, $this->DB_USERNAME, $this->DB_PASSWORD);
            $pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            return $pdo;

        }catch(Exception $e){
            throw new DBConnectionError("mysql", $this->HOST_ADDRESS, $this->DB_NAME, $e->getMessage());
        }

    }

    /**
     * @param Query $query
     * @return bool|PDOStatement
     * @throws ExecutionException
     */
    function executeFetch(Query $query) {
        return $this->execute($query);
    }

    /**
     * @param Query $query
     * @return array|bool|PDOStatement
     * @throws ExecutionException
     */
    function executeInReturn(Query $query) {
        return $this->execute($query);
    }

    /**
     * @param Query $sql
     * @return null
     * @throws ExecutionException
     */
    function executeUpdate(Query $sql) {
        return $this->execute($sql);
    }

    /**
     * @param $sql
     * @return bool|PDOStatement
     * @throws ExecutionException
     */
    function execute(Query $sql) {
        return Database::dbExecute($this->DB, $sql);
    }

    function beginTransaction(){
        return $this->DB->beginTransaction();
    }

    function commit(){
        return $this->DB->commit();
    }

    function rollback(){
        return $this->DB->rollBack();
    }

    /**
     * @return int|string
     */
    function lastInsertId(){
        return $this->DB->lastInsertId();
    }

}
