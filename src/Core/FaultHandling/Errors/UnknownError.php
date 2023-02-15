<?php


namespace Absoft\Line\Core\FaultHandling\Errors;

class UnknownError extends LineError {

    function __construct($description, $code = 0, $previous = null) {
        parent::__construct($description, $code, $previous);
    }

}