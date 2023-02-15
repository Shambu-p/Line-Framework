<?php

namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class MissingParameter extends LineError
{

    public string $title = "MissingParameter Exception";
    public string $description;
    private string $urgency = "immediate";


    function __construct($link, $parameter, string $file, $line, $code = 0, \Throwable $previous = null) {

        $this->description = "In route ".$link." parameter named ".$parameter." missed.";
        $this->file = $file." on line ".$line;

        parent::__construct($this->description, $code, $previous);

    }

}
