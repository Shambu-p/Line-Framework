<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/28/2021
 * Time: 6:03 PM
 */

namespace Absoft\Line\Core\FaultHandling\Errors;


class OperationFailed extends LineError {

    public string $title = "Operation Failed!";
    public string $urgency = "immediate";


    function __construct($message){
        $this->description = $message;
        $this->file = "unknown file";
        parent::__construct($message, 0, null);
    }

}
