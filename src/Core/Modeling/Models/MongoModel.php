<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 3/1/2021
 * Time: 11:05 AM
 */

namespace Absoft\Line\Core\Modeling\Models;


use Absoft\Line\Core\DbConnection\Database;
use MongoCollection;
use MongoConnectionException;
use MongoDB;

class MongoModel {

    /**
     * @var MongoDB
     */
    private $model;
    public $TABLE_NAME;
    public $DATABASE_NAME = "first";

    /**
     * MongoModel constructor.
     * @throws MongoConnectionException
     */
    function __construct(){
        $this->model = Database::getMongo($this->DATABASE_NAME);
    }

    /**
     * @return MongoCollection
     */
    function client(){
        try {
            return $this->model->selectCollection($this->TABLE_NAME);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

}
