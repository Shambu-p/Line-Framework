<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/27/2021
 * Time: 12:21 AM
 */

namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class ForbiddenRoute extends LineError {

    public string $title = "Forbidden Route!";
    public string $description = "The address that you are trying to access is forbidden!";
    public string $urgency = "immediate";


    function __construct(){
        $this->file = "Model file";
        parent::__construct($this->description, 0, null);
    }

}
