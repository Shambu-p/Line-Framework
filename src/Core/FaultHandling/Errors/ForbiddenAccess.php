<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/27/2021
 * Time: 12:49 AM
 */

namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class ForbiddenAccess extends LineError {

    public string $title = "Forbidden Access Exception";
    public string $description = "Access Denied!";
    public string $urgency = "immediate";


    function __construct($code = 0, $previous = null){
        $this->file = "Unknown file";
        parent::__construct($this->description, $code, $previous);
    }

}
