<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 6/19/2020
 * Time: 11:00 PM
 */

namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class EmptyArrayError extends LineError {

    public string $title = "Empty Array Passed!";


    function __construct($message){
        $this->description = $message;
        $this->file = "Model file";
        parent::__construct($message, 0, null);
    }

}