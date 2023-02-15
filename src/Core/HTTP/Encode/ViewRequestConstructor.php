<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 8:51 PM
 */
namespace Absoft\Line\Core\HTTP\Encode;

use Absoft\Line\Core\FaultHandling\Errors\MissingParameter;
use Absoft\Line\Core\FaultHandling\Errors\RouteNotFound;
use Absoft\Line\Core\HTTP\DataTransfer;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Route;

class ViewRequestConstructor extends RequestConstructor
{

    /**
     * @var Request
     */
    public $request;


    function __construct($method, $route_name, $request, $files = []){
        parent::__construct($method, $route_name, $request, $files);
    }

    /**
     * @param $route_name string
     * this method accept route name as string
     * for page routing the parent and sub folder name will be
     * written with dot in between.
     * here the header of the request will be produced
     */
    function headerConstruction(string $route_name)
    {

        $return = new \stdClass();
        $return->page_name = "";
        $return->sub_page = "";
        $arr = explode(".", $route_name);

        if(sizeof($arr) == 2 && $arr[0] && $arr[1]){

            $return->page_name = $arr[0];
            $return->sub_page = $arr[1];

        }

        $this->request->header = $return;

    }

    /**
     * @param string $method
     * @param string $route_name
     * @param array $request
     * @param array $files
     * TODO here is where the request construction organized by using the other methods
     */
    function mainConstruction(string $method, string $route_name, array $request, array $files) {

        $route = Route::getRoute($method, $route_name);

        if(!$route){
            return;
        }

        $this->request->route = $route;

        $this->requestConstruction($route_name, $request);
        $this->fileConstruction($files);
        $this->request->type = "view";

    }

    /**
     * @param string $route_name
     * @param array $request_array
     */
    function requestConstruction(string $route_name, array $request_array){

        $request = '';
        $params_array = $this->request->route["parameters"];


        foreach ($params_array as $parameter => $val){

            if(isset($request_array[$parameter])){
                $request .= "/".$request_array[$parameter];
            }else{
                break;
//                throw new MissingParameter("/pages/".$header->page_name."/".$header->sub_page, $value, __FILE__, __LINE__);
            }

        }

        $this->request->request = $request_array;
//        DataTransfer::set("/pages/".$header->page_name."/".$header->sub_page, $req_array);
        $this->request->link = Request::hostAddress().$route_name.$request;

    }
}
