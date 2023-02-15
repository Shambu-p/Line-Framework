<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 6:01 PM
 */

namespace Absoft\Line\Core\HTTP\Decode;

use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\HTTP\JSONResponse;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Route;

class RequestAnalyzer {

    /**
     * @var Request
     * this field will hold the request object to be constructed.
     */
    private $request;

    /**
     * RequestAnalyzer constructor.
     * this method will initiate the request analysis by taking input from engine or
     * by initiating address analyzer
     */
    function __construct() {

        $this->request = new Request();
        $this->addressAnalyzer();

    }

    /**
     * this method will setup arguments, which are sent using get or post for use
     */
    private function getArguments(){

        $route = $this->request->route;
        $req_string = str_replace($route["name"], "", $_SERVER["REQUEST_URI"]);
        $params = [];

        if((strtolower($_SERVER["REQUEST_METHOD"]) == "post")){
            $this->request->request = $_POST;
        }else{

            if($req_string == ""){
                $this->request->request = $params;
            }else{
                $req_array = explode("/", $req_string);

                $i = 1;
                foreach($route["parameters"] as $key => $value){

                    if(isset($req_array[$i])){
                        $params[$key] = $req_array[$i];
                    }else{
                        break;
                    }

                    $i += 1;

                }

                $this->request->request = $params;
            }

        }

    }

    /**
     * this method will fetch all sent file for easy use in object format
     */
    private function getFiles(){

        if(sizeof($_FILES) > 0){
            $this->request->file = json_decode(json_encode($_FILES));
        }

    }

    /**
     * return
     * this method read the request URI and then construct the request
     * by dividing the request to header, requests and request files.
     */
    private function addressAnalyzer(){

        $route_name = $_SERVER["REQUEST_URI"];
        $this->request->link = Request::hostAddress().$route_name;

        $method = strtolower($_SERVER["REQUEST_METHOD"]);

        if($route_name != "/" || $route_name != ""){

            $segment = explode("/", $route_name);
            $this->request->case = ($segment[1] == "api") ? "API" : "UI";

        }else{
            $this->request->case = "UI";
        }

        $route = Route::getRoute($method, $route_name);

        if(!$route){
            $_SERVER["REQUEST_METHOD"] = "GET";
            $method = "get";

            if($this->request->case != "UI"){
                $response = new JSONResponse();
                $response->prepareError("route not found");
                $response->respond();
                exit();
            }

            $route_name = "/404";
            $route = Route::getRoute($method, $route_name);
            $this->request->link = Request::hostAddress().$route_name;
        }

        $this->request->route_name = $route_name;
        $this->request->route = $route;
        $this->request->type = is_string($route["callback"]) ? "view" : "control";
        $this->request->template = $this->request->type == "view" ? $route["callback"] : "";

        $this->getFiles();
        $this->getArguments();

    }

    /**
     * @return Request
     * this will return the constructed request.
     */
    public function getRequest(){
        return $this->request;
    }

}
