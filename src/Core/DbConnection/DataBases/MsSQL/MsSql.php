<?php
/**
 * Created by PhpStorm.
 * User: Abnet Kebede
 * Date: 1/18/2020
 * Time: 11:16 PM
 */

namespace Absoft\Line\Core\DbConnection\DataBases\MsSQL;

use Absoft\Line\Core\DbConnection\Database;
use Absoft\Line\Core\DbConnection\DataBases\Connection;
use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Exception;
use PDO;
use PDOStatement;

class MsSql implements Connection {

    public $HOST_ADDRESS = null;
    public $DB_NAME = null;
    private $DB_USERNAME = null;
    private $DB_PASSWORD = null;
    private $DB;

    /**
     * MsSql constructor.
     * @param array $db_info
     * @throws DBConnectionError
     */
    function __construct(array $db_info){

        $this->HOST_ADDRESS = $db_info['HOST_ADDRESS'];
        $this->DB_NAME = $db_info['DB_NAME'];
        $this->DB_USERNAME = $db_info['DB_USERNAME'];
        $this->DB_PASSWORD = $db_info['DB_PASSWORD'];

        $this->DB = $this->getConnection();
//        Database::$mssql = Database::$mssql ? Database::$mssql : $this->getConnection();

    }

    /**
     * @return PDO|null
     * @throws DBConnectionError
     */
    function getConnection(){

        $return[] = null;

        try{

            $dns = "sqlsrv:Server=".$this->HOST_ADDRESS.";Database=".$this->DB_NAME;
            $pdo = new PDO($dns, $this->DB_USERNAME, $this->DB_PASSWORD);

            $pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            return $pdo;

        } catch(Exception $e) {
            throw new DBConnectionError("mssql", $this->HOST_ADDRESS, $this->DB_NAME, $e->getMessage());
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
