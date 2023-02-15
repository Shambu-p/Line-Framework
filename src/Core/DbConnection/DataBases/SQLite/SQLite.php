<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 12/7/2019
 * Time: 9:15 AM
 */

namespace Absoft\Line\Core\DbConnection\DataBases\SQLite;

use Absoft\Line\App\Security\IpCheck;
use Absoft\Line\Core\DbConnection\DataBases\Connection;
use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Application\conf\DirConfiguration;
use DateTime;
use Exception;
use PDO;
use \PDOStatement;

class SQLite implements Connection {

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

//        Database::$mysql = (Database::$mysql == null) ? $this->getConnection() : Database::$mysql;
        $this->DB = $this->getConnection();

    }

    /**
     * @return PDO|null
     * @throws DBConnectionError
     */
    function getConnection(){

        try{

            $dns = "sqlite:".str_replace("{{main_folder}}", DirConfiguration::$_main_folder, $this->HOST_ADDRESS);

            return new PDO($dns, '', '', array(
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));

        }catch(Exception $e){
            throw new DBConnectionError("sqlite ", $this->HOST_ADDRESS, $this->DB_NAME, $e->getMessage());
        }

    }

    /**
     * @param Query $query
     * @return null
     * @throws ExecutionException
     */
    function executeFetch(Query $query){

        $data = [];
        $db = $this->DB;
        $status = null;
        $statement = null;

        $statement = $db->prepare($query->getQuery());

        if(sizeof($query->getValues()) == 0){
            $status = $statement->execute();
        }else{
            $status = $statement->execute($query->getValues());
        }

        if($status){

            while($row = $statement->fetch( PDO:: FETCH_ASSOC)){
                $data[] = $row;
            }

            return $data;

        }else{

            $this->generateLog($query->getQuery(), IpCheck::clientIp(), IpCheck::clientIp());
            throw new ExecutionException($statement->errorInfo()[2]);

        }

    }

    /**
     * @param Query $query
     * @return array|bool|PDOStatement
     */
    function executeInReturn(Query $query){

        $db = $this->DB;
        $status = null;
        $statement = null;

        $statement = $db->prepare($query->getQuery());

        if(sizeof($query->getValues())){
            $status = $statement->execute($query->getValues());
        }else{
            $status = $statement->execute();
        }

        if($status){
            return $statement;
        }else{
            $this->generateLog($query->getQuery(), IpCheck::clientIp(), IpCheck::clientIp());
        }

        return [];

    }

    /**
     * @param Query $sql
     * @return null
     */
    function executeUpdate(Query $sql){

        $db = $this->DB;
        $status = null;
        $statement = null;

        $statement = $db->prepare($sql->getQuery());

        if(sizeof($sql->getValues()) == 0){
            $status = $statement->execute();
        }else{
            $status = $statement->execute($sql->getValues());
        }

        if($status){
            return true;
        }else{

            $this->generateLog($sql->getQuery(), "localhost", "localhost");
            new ExecutionException($statement->errorInfo()[2]);

        }

        return false;

    }

    /**
     * @param $sql
     * @return bool
     * @throws ExecutionException
     */
    function execute(Query $sql){

        $db = $this->DB;
        $status = null;
        $statement = null;

        $statement = $db->prepare($sql->getQuery());

        if(sizeof($sql->getValues()) == 0){
            $status = $statement->execute();
        }else{
            $status = $statement->execute($sql->getValues());
        }

        if($status){
            return $status;
        }else{

            $this->generateLog($sql->getQuery(), "localhost", "localhost");
            throw new ExecutionException($statement->errorInfo()[2]);

        }

    }

    /**
     * @param $sql
     * @param $srvname
     * @param $ip
     * @return int|string
     */
    function generateLog($sql, $srvname, $ip){

        try{

            $nd = new DateTime();
            $time = $nd->format("H:i:s");
            $date = $nd->format("Y-m-j");
            $log = "$ip, $srvname, $sql, $time, $date \n";
            file_put_contents(DirConfiguration::$_main_folder."/failure_log.txt", $log, FILE_APPEND);
            return 1;

        }catch(Exception $e){
            return $e->getMessage();
        }

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
