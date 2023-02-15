<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 8:47 PM
 */
namespace Absoft\Line\Core\HTTP;

use Absoft\Line\Core\Engines\HTTP\Engine;
use Absoft\Line\Core\FaultHandling\Errors\MissingParameter;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;

class Request
{

    /**
     * @var \stdClass
     * this field will hold the controller name and method in the case of control request
     * in the case of page request it will hold the template address
     */
    public $template;

    /**
     * @var array
     * this filed will hold the request body which were sent
     * by using POST or GET method
     */
    public $request;

    /**
     * @var \stdClass
     * this filed will hold file requests if there was file upload
     * other wise it will hold empty object
     */
    public $file;

    /**
     * @var string
     * this filed will hold the link which initiate this construction
     * if the request were not from the client then the link will be constructed
     * if the request were from client then it will be saved here
     */
    public $link;

    /**
     * @var string
     * this filed will hold the type of request
     * wither the request is view or control request
     */
    public $type;

    /**
     * @var string
     * this filed will hold the type of request
     * wither the request is view or control request
     */
    public $case;

    /**
     * @var string
     * this filed will hold the issued route name
     */
    public $route_name;

    /**
     * @var object|array
     * this filed will hold the issued route
     */
    public $route;


    public function __construct(){

        $this->request = [];
        $this->link = "";
        $this->file = new \stdClass();
        $this->template = "";
        $this->type = "";

    }

    /**
     * @return bool
     * this method will check if the request is view request
     */
    public function isView(){

        if($this->type == "view"){
            return true;
        }

        return false;

    }


    /**
     * @return bool
     * this method will check if the request is control request
     */
    public function isControl(){

        if($this->type == "control"){
            return true;
        }

        return false;

    }

    /**
     * @return string
     */
    public static function hostAddress(){
        return ($_SERVER["SERVER_PROTOCOL"] == "HTTP/1.1" ? "http://" : "https://").$_SERVER["HTTP_HOST"];
    }

}
