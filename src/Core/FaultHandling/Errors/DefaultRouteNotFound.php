<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class DefaultRouteNotFound extends LineError
{

    public string $title = "RouteNotFound Exception";

    private string $description = "
    The default route were not found. <br>
    try to insert the following code to the rout configuration file <br>
    <i><b>Route::setDefault('controller_name', 'method_name');</b></i> <br> 
    make sure there is no duplicate of this method in the route configuration file.";

    public string $urgency = "immediate";


    function __construct($code = 0, $previous = null){
        $this->file = "SystemConstructor/App/Routing/route.php";
        parent::__construct($this->description, $code, $previous);
    }

}
