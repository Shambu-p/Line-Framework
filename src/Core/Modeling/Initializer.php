<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 11/5/2020
 * Time: 6:20 PM
 */
namespace Absoft\Line\Core\Modeling;

use Absoft\Line\Core\FaultHandling\Errors\DataOutOfRangeError;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Models\Model;

abstract class Initializer
{

    public $VALUES;

    /**
     * @param $base_name
     * @return bool
     * @throws ClassNotFound
     * @throws DBConnectionError
     * @throws ExecutionException|OperationFailed
     */
    public function initialize($base_name){

        $model_name = 'Application\\Models\\'.$base_name.'Model';

        /** @var Model $model */
        if($model = new $model_name) {

            $query = $model->addRecord();
            foreach ($this->VALUES as $row){
                $query->add($row);
            }

            $result = $query->insert();
            $count = $result->rowCount();
            $size = sizeof($this->VALUES);

            if($count != $size) {
                throw new OperationFailed("All data were not set only $count out of $size");
            }

            return true;

        }else{
            throw new ClassNotFound($model_name, "initializer abstract file", __LINE__);
        }

    }

}
