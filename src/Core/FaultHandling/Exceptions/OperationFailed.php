<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/28/2021
 * Time: 6:03 PM
 */

namespace Absoft\Line\Core\FaultHandling\Exceptions;

class OperationFailed extends LineException {

    public string $title = "Operation Failed!";
    public string $description = "";


    function __construct($message, $code = 0, $previous = null){

        $this->description = $message;
        $this->file = "unknown file";
        parent::__construct($this->description, $code, $previous);

    }

}
