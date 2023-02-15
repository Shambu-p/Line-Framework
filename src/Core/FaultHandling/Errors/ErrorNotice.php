<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;
use Throwable;

class ErrorNotice extends LineError {

    public string $title = "ERROR NOTICE!!!";
    public string $description;

    function __construct($description, $error_file, $code = 0, Throwable $previous = null){

        $this->file = $error_file;
        $this->description = $description;
        parent::__construct($description, $code, $previous);

    }

}