<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 6/19/2020
 * Time: 11:00 PM
 */

namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class RouteNotFound extends LineError {

    public string $title = "RouteNotFound Exception";
    public string $urgency = "immediate";


    function __construct($route_name = "unknown", $code = 0, $previous = null) {

        $this->description = "there is no route named $route_name ";
        $this->file = "SystemConstructor/App/Routing/route.php";
        parent::__construct($this->description, $code, $previous);

    }

}
