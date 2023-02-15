<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class ClassNotFound extends LineError {

    public string $title = "ClassNotFound Exception";
    public string $urgency = "immediate";


    function __construct($class_name, $file, $line, $code = 0, \Throwable $previous = null){

        $this->description = "
        There is no Class named ' $class_name '. 
        this might be because the class were not defined  
        or may be it is defined incorrectly.";

        $this->file = $file." on line ".$line;

        parent::__construct($this->description, $code, $previous);

    }

}
