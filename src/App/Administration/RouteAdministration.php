<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 11/20/2021
 * Time: 8:05 PM
 */

namespace Absoft\Line\App\Administration;

use Application\conf\DirConfiguration;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\HTTP\Route;

class RouteAdministration {


    /**
     * @param string $route_name
     * @param string $paramters
     * @return bool
     * @throws OperationFailed
     */
    public static function createRoute(string $route_name, string $paramters){
        
        $all_routes = Route::allRoutes();

        if(isset($all_routes[$route_name])){
            throw new OperationFailed("Route Already Exist!");
        }
        
        $file = file_get_contents(DirConfiguration::$_main_folder . "/apps/conf/route.php");
        $route_add = "\r\nRoute::set(\"$route_name\", \"$paramters\");";
        file_put_contents(DirConfiguration::$_main_folder . "/apps/conf/route.php", $file.$route_add);

        return true;

    }

    /**
     * @param string $route_name
     * @return bool
     */
    public static function deleteRoute($route_name){
        
        $all_routes = Route::allRoutes();

        if(!isset($all_routes[$route_name])){
            return true;
        }

        $current = "\r\nRoute::set(\"$route_name\", \"".$all_routes[$route_name]."\");";
        
        $file = file_get_contents(DirConfiguration::$_main_folder . "/apps/conf/route.php");
        $file = str_replace($current, "", $file);
        file_put_contents(DirConfiguration::$_main_folder . "/apps/conf/route.php", $file);

        return true;

    }

    /**
     * @param string $route_name
     * @param array $changed
     * 
     * 
     * @throws OperationFailed
     */
    public static function change($route_name, array $changed){

        $all_routes = Route::allRoutes();

        if(!isset($all_routes[$route_name])){
            throw new OperationFailed("Route named $route_name Does not Exist!");
        }

        $current = "Route::set(\"$route_name\", \"".$all_routes[$route_name]."\");";
        $new = "Route::set(\"".$changed["name"]."\", \"".$changed["parameters"]."\");";
        
        $file = file_get_contents(DirConfiguration::$_main_folder . "/apps/conf/route.php");
        $file = str_replace($current, $new, $file);
        file_put_contents(DirConfiguration::$_main_folder . "/apps/conf/route.php", $file);

    }

}