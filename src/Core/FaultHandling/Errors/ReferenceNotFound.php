<?php


namespace Absoft\Line\Core\FaultHandling\Errors;

use Absoft\Line\Core\FaultHandling\Errors\LineError;

class ReferenceNotFound extends LineError {

    public string $title = "ReferenceNotFound Exception";
    public string $description;
    private string $urgency = "immediate";


    function __construct($from, $to, $code = 0, $previous = null){

        $this->description = "There is no Reference to the Builder named ". $to." in Entity " . $from;
        $this->file = "system File";
        parent::__construct($this->description, $code, $previous);

    }

}
