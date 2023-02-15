<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 3/2/2020
 * Time: 3:04 PM
 */

namespace Absoft\Line\Core\DbConnection;

use Absoft\Line\App\Security\IpCheck;
use Absoft\Line\Core\DbConnection\DataBases\MongoDB\MongoDB;
use Absoft\Line\Core\DbConnection\DataBases\MsSQL\MsSql;
use Absoft\Line\Core\DbConnection\DataBases\MySQL\MySql;
use Absoft\Line\Core\DbConnection\DataBases\Connection;
use Absoft\Line\Core\DbConnection\DataBases\SQLite\SQLite;
use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Application\conf\DBConfiguration;
use Application\conf\DirConfiguration;
use MongoConnectionException;
use PDO;
use PDOStatement;

class Database implements Connection {

    /** @var Connection  */
    public Connection $subject;

    public static array $subject_array = [];

    public array $configuration_array;



    /**
     * Database constructor.
     * @param $srv_name
     * @param $db_name
     * @throws DBConnectionError
     */
    function __construct($srv_name, $db_name){

        if(self::getDbObject($srv_name, $db_name)){
            $this->subject = &self::getDbObject($srv_name, $db_name);
            return;
        }

        $this->configuration_array = DBConfiguration::$conf;

        if(!empty($this->configuration_array) && isset($this->configuration_array[$srv_name][$db_name])){

            switch ($srv_name) {
                case "MySql":
                    self::saveDbObject($srv_name, $db_name, new MySql($this->configuration_array[$srv_name][$db_name]));
                    $this->subject = &self::getDbObject($srv_name, $db_name);
                    break;
                case "MsSql":
                    self::saveDbObject($srv_name, $db_name, new MsSql($this->configuration_array[$srv_name][$db_name]));
                    $this->subject = &self::getDbObject($srv_name, $db_name);
                    break;
                case "SQLite":
                    self::saveDbObject($srv_name, $db_name, new SQLite($this->configuration_array[$srv_name][$db_name]));
                    $this->subject = &self::getDbObject($srv_name, $db_name);
                    break;
            }

        }

    }

    /**
     * @return PDO|null
     */
    function getConnection(){
        return $this->subject->getConnection();
    }

    /**
     * @param $db_name
     * @return \MongoDB|null
     * @throws MongoConnectionException
     */
    static function getMongo($db_name){
        return isset(DBConfiguration::$conf["Mongo"][$db_name]) && sizeof(DBConfiguration::$conf["Mongo"][$db_name]) ? MongoDB::connect(DBConfiguration::$conf["Mongo"][$db_name]) : null;
    }

    /**
     * @param Query $query
     * @return array|bool
     */
    function execute(Query $query) {
        return $this->subject->execute($query);
    }

    /**
     * @param Query $query
     * @return array|bool|null
     */
    function executeUpdate(Query $query) {
        return $this->subject->executeUpdate($query);
    }

    /**
     * @param Query $query
     * @return array|bool|PDOStatement|void
     */
    function executeInReturn(Query $query){
        return $this->subject->executeInReturn($query);
    }

    /**
     * @param Query $query
     * @return bool|\PDOStatement
     */
    function executeFetch(Query $query){
        return $this->subject->executeFetch($query);
    }

    static function saveDbObject($srv_name, $db_name, $connection) {
        self::$subject_array[$srv_name][$db_name] = $connection;
    }

    static function &getDbObject($srv_name, $db_name){

        if(!isset(self::$subject_array[$srv_name][$db_name])){
            self::$subject_array[$srv_name][$db_name] = null;
        }

        return  self::$subject_array[$srv_name][$db_name];

    }

    function beginTransaction(){
        return $this->subject->beginTransaction();
    }

    function commit(){
        return $this->subject->commit();
    }

    function rollback(){
        return $this->subject->rollback();
    }

    /**
     * @return int|string
     */
    function lastInsertId(){
        return $this->subject->lastInsertId();
    }

    /**
     * @param $db
     * @param Query $query
     * @return mixed
     * @throws ExecutionException
     */
    static function dbExecute($db, Query $query){

        try {

            $statement = $db->prepare($query->getQuery());

            if(sizeof($query->getValues()) == 0) {
                $statement->execute();
            } else {
                $statement->execute($query->getValues());
            }

            return $statement;

        }catch (\Exception $exception){

            if($db->inTransaction()) {
                $db->rollBack();
            }

            self::generateLog($query->getQuery(), IpCheck::clientIp(), IpCheck::clientIp());
            throw new ExecutionException($exception->getMessage());

        }

    }

    /**
     * @param $sql
     * @param $srvname
     * @param $ip
     * @return int|string
     */
    static function generateLog($sql, $srvname, $ip){

        try{

            $nd = new \DateTime();
            $time = $nd->format("H:i:s");
            $date = $nd->format("Y-m-j");
            $log = "$ip, $srvname, $sql, $time, $date \n";
            file_put_contents(DirConfiguration::$_main_folder."/failure_log.txt", $log, FILE_APPEND);
            return 1;

        }catch(\Exception $e){
            return $e->getMessage();
        }

    }

}
