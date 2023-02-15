<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 9:42 PM
 */
namespace Absoft\Line\Core\HTTP;

use Absoft\Line\Core\HTTP\Encode\ControlRequestConstructor;
use Absoft\Line\Core\HTTP\Encode\ViewRequestConstructor;

class Route {


    /**
     * @var array
     * this field will hold all the routes available for this particular client
     */
    private static $route_array = [
        "get" => [],
        "post" => []
    ];
    private static $default = [];


    /**
     * @param $method
     * @param string $route_name
     * @param array $request
     * this method take route name and requests
     * as result it will generate or construct URL
     * this method can be used in views for generating link.
     * @return string
     */
    private static function routeAddress($method, string $route_name, array $request){

        $construction = new ControlRequestConstructor($method, $route_name, $request);
        return $construction->getRequest()->link;

    }

    public static function route_address($route_name, $request = [], $method = "post"){
        return self::routeAddress($method, $route_name, $request);
    }

    /**
     * @param $route_name
     * @param array $request
     *
     * this method take route name and request array
     * as a result it will redirect to that route and pass the request
     * the method uses the URL generated in routeAddress method
     *
     * this method is important for developers to redirect
     * from one controller to another controller
     * @param string $method
     */
    public static function goRoute($route_name, $request = [], $method = "post"){

        header("location: ".self::routeAddress($method, $route_name, $request));
        die();

    }

    /**
     * @param $route_name
     * @param $method
     * method is by default get method therefore by default the route will be looked for in
     * get method routes.
     * @param $request
     * @return Request
     */
    public static function display($route_name, $request = [], $method = "get"){

        $construction = new ViewRequestConstructor($method, $route_name, $request);
        return $construction->getRequest();

    }

    /**
     * @param $route_name
     * @param array $request
     * @param string $method
     * method is by default post method therefore by default the route will be looked for in
     * post method routes.
     * @return Request
     */
    public static function route($route_name, $request = [], $method = "post"){

        $construction = new ControlRequestConstructor($method, $route_name, $request);
        return $construction->getRequest();

    }

    /**
     * @param $route_name
     * @param $callback
     * this callback can be a view address in templates folder
     * or it can be function which will be executed.
     * functions can be specified in two ways
     * first:
     * Route::post("/route_name", function ($request){
     *      //do something
     * });
     *
     * second:
     * Route::post("/route_name", [controller_object_instance, 'method_name'], ["param1", "param2", "param3"]);
     * @param array $parameters
     */
    public static function post($route_name, $callback, $parameters = []){
        self::set("post", $route_name, $callback, $parameters);
    }

    /**
     * @param $route_name
     * @param $callback
     * this callback can be a view address in templates folder
     * or it can be function which will be executed.
     * functions can be specified in two ways
     * first:
     * Route::get("/route_name", function ($request){
     *      //do something
     * });
     *
     * second:
     * Route::get("/route_name", [controller_object_instance, 'method_name'], ["param1", "param2", "param3"]);
     * @param array $parameters
     */
    public static function get($route_name, $callback, $parameters = []){
        self::set("get", $route_name, $callback, $parameters);
    }

    /**
     * @param $method
     * @param $route_name
     * @param $request
     * this method take page name and sub page name of a view and request for that particular view
     * and as a result it will generate URL to invoke that view.
     * @return string
     */
    private static function viewAddress($method, $route_name, $request){

        $construction = new ViewRequestConstructor($method, $route_name, $request);
        return $construction->getRequest()->link;

    }

    public static function get_view($route_name, $request = []){
        return self::viewAddress("get", $route_name, $request);
    }

    public static function pots_view($route_name, $request = []){
        return self::viewAddress("post", $route_name, $request);
    }

    /**
     * @param string $route_name
     * @param array $request
     *
     * this method uses viewAddress method to generate view URL and
     * by using the generated URL it will redirect to that particular view as result.
     * @param string $method
     */
    public static function view(string $route_name, $request = [], $method = "get"){

        header("location: ".self::viewAddress($method, $route_name, $request));
        die();

    }

    /**
     * @param $method_type
     * @param string $route_name
     * @param $callback
     * @param array $parameters
     *
     * this method save route name and route parameters in route_array field
     */
    public static function set($method_type, string $route_name, $callback, $parameters = []){

        $name_list = explode("/", $route_name);

        Route::$route_array[$method_type] = self::createTree(Route::$route_array[$method_type], $name_list, [
            "callback" => $callback,
            "parameters" => $parameters,
            "name" => $route_name
        ], 1);

    }

    public static function createTree($big_array, $name_array, $route, $index = 0){

        if(sizeof($name_array) - 1 == $index){

            if(isset($big_array["children"][$name_array[$index]])){
                $big_array["children"][$name_array[$index]]["callback"] = $route["callback"];
                $big_array["children"][$name_array[$index]]["parameters"] = $route["parameters"];
            }else{
                $big_array["children"][$name_array[$index]] = $route;
            }

            return $big_array;

        }

        if(isset($big_array["children"][$name_array[$index]])){
            $big_array["children"][$name_array[$index]] = self::createTree($big_array["children"][$name_array[$index]], $name_array, $route, $index + 1);
        }else{
            $big_array["children"][$name_array[$index]] = self::createTree([
                "children" => [],
                "parameters" => [],
                "callback" => []
            ], $name_array, $route, $index + 1);
        }

        return $big_array;

    }

    /**
     * @param string $type
     * @param string $route_name
     * @return string
     * this method will take route name as parameter and then
     * it will return all the parameters needed to this particular route as string
     */
    public static function getRoute(string $type, string $route_name){

        $current = null;
        $index = 1;
        $name_list = explode("/", $route_name);

        if(isset(self::$route_array[$type]["children"][$name_list[$index]])){
            $current = self::$route_array[$type]["children"][$name_list[$index]];
            $index += 1;
        }else{
            return null;
        }

        while(isset($name_list[$index])){

            if(isset($current["children"][$name_list[$index]])){
                $current = $current["children"][$name_list[$index]];
                $index += 1;
            }else{
                break;
            }

        }

        return isset($current["name"]) ? $current : null;

    }

    /**
     * @return array
     * this method will return all the routes available for this particular client
     */
    public static function allRoutes(){
        return Route::$route_array;
    }

}
