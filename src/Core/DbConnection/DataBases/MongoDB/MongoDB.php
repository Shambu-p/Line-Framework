<?php
/**
 * created by Aboma Teshome
 * ID: A/UR4419/09
 * Date: Monday January 18 2021
 * WebProgramming Assignment
 */
namespace Absoft\Line\Core\DbConnection\DataBases\MongoDB;

use MongoConnectionException;
use \MongoClient;

class MongoDB {

    /**
     * @param $db_info
     * @return \MongoDB
     * @throws MongoConnectionException
     */
    public static function connect($db_info){

        //$this->DB = new Manager("mongodb://localhost:27017");
        $address = (empty($db_info['DB_USERNAME']) && empty($db_info['DB_PASSWORD'])) ? "mongodb://" . $db_info['HOST_ADDRESS'] : "mongodb://".$db_info['DB_USERNAME'].":".$db_info['DB_PASSWORD']."@" . $db_info['HOST_ADDRESS'];
        $connection = new MongoClient($address);

        return $connection->selectDB($db_info['DB_NAME']);

    }

}
