<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 8:52 PM
 */
namespace Absoft\Line\Core\HTTP\Encode;


use Absoft\Line\Core\FaultHandling\Errors\MissingParameter;
use Absoft\Line\Core\FaultHandling\Errors\RouteNotFound;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Route;

class ControlRequestConstructor extends RequestConstructor {

    /**
     * @var Request
     */
    public $request;


    /**
     * ControlRequestConstructor constructor.
     * @param $method
     * @param string $route_name
     * @param array $request
     * @param array $files
     */
    function __construct($method, string $route_name, array $request, $files = []){
        parent::__construct($method, $route_name, $request, $files);
    }

    /**
     * @param $route_name string
     * this method accept route name as string
     * for page routing the parent and sub folder name will be
     * written with dot in between.
     * here the header of the request will be produced
     */
    function headerConstruction(string $route_name){

        $return = new \stdClass();
        $return->controller = "";
        $return->mtd = "";
        $arr = explode(".", $route_name);

        if(sizeof($arr) == 2 && $arr[0] && $arr[1]){

            $return->controller = $arr[0];
            $return->mtd = $arr[1];

        }

        $this->request->header = $return;

    }


    /**
     * @param string $method
     * @param string $route_name
     * @param array $request
     * @param array $files
     * TODO here is where the request construction organized by using the other methods
     * @throws MissingParameter
     * @throws RouteNotFound
     */
    function mainConstruction(string $method, string $route_name, array $request, array $files){

        try {

            $route = Route::getRoute($method, $route_name);

            if(!$route){
                return;
            }

            $this->request->route = $route;

            if(strtolower($method) == "get"){
                $this->requestConstruction($route_name, $request);
            }else{
                $this->request->link = Request::hostAddress().$route_name;
            }
            $this->fileConstruction($files);
            $this->request->type = "control";

        } catch (MissingParameter $e) {
            throw $e;
        } catch (RouteNotFound $e) {
            throw $e;
        }

    }

    /**
     * @param string $route_name
     * @param array $request_array
     */
    function requestConstruction(string $route_name, array $request_array){

        $request = '';
        $route_parameter = $this->request->route["parameters"];

        foreach ($route_parameter as $value){

            if(isset($request_array[$value])){
                $request .= "/".$request_array[$value];
            }
            else{
//                throw new MissingParameter("/".$header->controller."/".$header->mtd, $value, __FILE__, __LINE__);
                break;
            }

        }

        $this->request->request = $request_array;
        $this->request->link = Request::hostAddress().$route_name.$request;

    }


}
