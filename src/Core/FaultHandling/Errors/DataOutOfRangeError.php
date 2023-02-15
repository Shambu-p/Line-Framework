<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class DataOutOfRangeError extends LineError {

    public string $title = "Data Out Of Range Error";
    public string $urgency = "immediate";


    function __construct($message){
        $this->description = $message;
        $this->file = "Model file";
        parent::__construct($message, 0, null);
    }

}