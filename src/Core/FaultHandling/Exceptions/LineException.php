<?php


namespace Absoft\Line\Core\FaultHandling\Exceptions;


abstract class LineException extends \Exception {

    public string $title;
    public $file;
    public string $description;

    function __construct($description, $code, $previous) {
        parent::__construct($description, $code, $previous);
    }

}