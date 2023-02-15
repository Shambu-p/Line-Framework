<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\FaultHandler;

abstract class LineError extends \Exception{

    public string $title;
    public $file;
    public string $description;

    function __construct($description, $code, $previous) {
        parent::__construct($description, $code, $previous);
        FaultHandler::reportError($this->title, $this->description, $this->file);
    }

}