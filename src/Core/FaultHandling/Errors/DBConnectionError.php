<?php

namespace Absoft\Line\Core\FaultHandling\Errors;

use Absoft\Line\Core\FaultHandling\Errors\LineError;

class DBConnectionError extends LineError {

    public string $title = "Database Connection Failed!";
    public string $urgency = "immediate";


    function __construct($srv, $host, $db_name, $message){
        $this->description = "tried to connect $srv Database server <br> location: $host<br> to database named: $db_name <br>".$message;
        $this->file = "Database connection file";
        parent::__construct($message, 0, null);
    }

}