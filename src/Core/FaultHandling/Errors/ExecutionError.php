<?php

namespace Absoft\Line\Core\FaultHandling\Errors;

use Absoft\Line\Core\FaultHandling\Errors\LineError;

class ExecutionError extends LineError {

    public string $title = "Query Execution Failed!";
    public string $urgency = "immediate";


    function __construct($message){
        $this->description = $message;
        $this->file = "Database connection file";
        parent::__construct($message, 0, null);
    }

}