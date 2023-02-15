<?php

namespace Absoft\Line\Core\FaultHandling\Errors;

use Absoft\Line\Core\FaultHandling\Errors\LineError;
use \Throwable;

class ControllerNotFound extends LineError
{

    public string $title = "ControllerNotFound Exception";
    public string $urgency = "immediate";


    function __construct($controller_name, $file, $line, $code = 0, Throwable $previous = null){

        $this->description = "
        There is no Controller named ' $controller_name '. 
        this might be because the controller not defined in routes.php 
        or may be it is defined incorrectly.";

        $this->file = $file." on line ".$line;

        parent::__construct($this->description, $code, $previous);

    }

}
