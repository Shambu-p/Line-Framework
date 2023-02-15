<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;
use Throwable;

class ErrorWaning extends LineError{

    public string $title = "ERROR WARNING!!!";

    function __construct($description, $error_file, $code = 0, Throwable $previous = null){

        $this->file = $error_file;
        $this->description = $description;
        parent::__construct($description, $code, $previous);

    }

}