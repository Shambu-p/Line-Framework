<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 6/12/2021
 * Time: 7:55 PM
 */

namespace Absoft\Line\App\Administration;


use Application\conf\DirConfiguration;
use Absoft\Line\Core\HTTP\Route;

class Administration {

    public static function routes(){

        Route::post("/Admin/all_controllers", [new AdminController(), "allControllers"], ["token" => ["required"]]);
        Route::post("/Admin/all_models", [new AdminController(), "allModels"], ["token" => ["required"]]);
        Route::post("/Admin/all_builders", [new AdminController(), "allBuilders"], ["token" => ["required"]]);

        Route::post("/Admin/new_controller", [new AdminController(), "newController"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/new_model", [new AdminController(), "newModel"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/new_builder", [new AdminController(), "newBuilder"], ["token" => ["required"], "name" => ["required"]]);

        Route::post("/Admin/initiate", [new AdminController(), "initiate"], ["token" => ["required"], "name" => ["required"]]);

        Route::post("/Admin/delete_builder", [new AdminController(), "deleteBuilder"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/delete_model", [new AdminController(), "deleteModel"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/delete_controller", [new AdminController(), "deleteController"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/schema", [new AdminController(), "schema"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/records", [new AdminController(), "records"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/drop", [new AdminController(), "dropTable"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/export", [new AdminController(), "exportTable"], ["token" => ["required"], "name" => ["required"]]);
        Route::post("/Admin/insertData", [new AdminController(), "insertData"], ["token" => ["required"], "name" => ["required"], "data" => ["required"]]);

        Route::post("/Admin/login", [new AdminController(), "login"], ["username" => ["required"], "password" => ["required"]]);
        Route::post("/Admin/logout", [new AdminController(), "logout"], ["token" => ["required"]]);
        Route::post("/Admin/change_password", [new AdminController(), "changePassword"], ["token" => ["required"], "old_password" => ["required"], "new_password" => ["required"], "username" => ["required"]]);
        Route::post("/Admin/login_update", [new AdminController(), "updateLogin"], ["token" => ["required"], "password" => ["required"]]);
        Route::post("/Admin/authorization", [new AdminController(), "authorization"], ["token" => ["required"]]);
        Route::post("/Admin/view_token", [new AdminController(), "viewAuth"], ["token" => ["required"]]);

        Route::post("/Admin/about", [new AdminController(), "about"], []);
        Route::post("/Admin/info", [new AdminController(), "info"], ["name" => ["required"]]);

        Route::post("/Admin/variables", [new AdminController(), "allControllers"], ["token" => ["required"]]);

        // route administrations

        Route::post("/Admin/create_route", [new AdminController(), "createRoute"], ["token" => ["required"], "routue_name" => ["required"], "parameters" => ["required"]]);
        Route::post("/Admin/delete_route", [new AdminController(), "deleteRoute"], ["token" => ["required"], "route_name" => ["required"]]);
        Route::post("/Admin/update_route", [new AdminController(), "editRoute"], ["token" => ["required"], "route_name" => ["required"], "changed" => ["required"]]);
        Route::post("/Admin/get_routes", [new AdminController(), "getRoutes"], ["token" => ["required"], "controller" => ["required"]]);

    }

    /**
     * @return array
     */
    public static function variables(){
        return (array) json_decode(file_get_contents(DirConfiguration::$_main_folder."/apps/Runtime/administration.json"));
    }

    /**
     * @param $name
     * @return bool
     */
    public static function deleteVariable($name){

        $vars = self::variables();

        if(isset($vars[$name])){

            unset($vars[$name]);
            file_put_contents(DirConfiguration::$_main_folder."/apps/Runtime/administration.json", json_encode($vars));

        }

        return true;

    }

    /**
     * @param $name
     * @param $attribute
     * @param $value
     * @return bool
     */
    public static function changeVariable($name, $attribute, $value){

        $vars = self::variables();
        $object = isset($vars[$name]) ? (array) $vars[$name] : [];

        $object[$attribute] = $value;
        $vars[$name] = $object;
        file_put_contents(DirConfiguration::$_main_folder."/apps/Runtime/administration.json", json_encode($vars));
        return true;

    }

    /**
     * @param $name
     * @param $exported
     * @param $initiated
     * @param $model
     * @param $builder
     * @param $controller
     * @param $initializer
     * @return bool
     */
    public static function createVariable($name, $exported, $initiated, $model, $builder, $controller, $initializer){

        $vars = self::variables();

        if(isset($vars[$name])){
            return false;
        }

        $object = [];

        $object["exported"] = $exported;
        $object["initiated"] = $initiated;
        $object["model"] = $model;
        $object["builder"] = $builder;
        $object["controller"] = $controller;
        $object["initializer"] = $initializer;

        $vars[$name] = $object;

        file_put_contents(DirConfiguration::$_main_folder."/apps/Runtime/administration.json", json_encode($vars));

        return true;

    }

    /**
     * @param $name
     * @return array
     s*/
    public static function see($name){

//        $mdb = new Builders();
//        $mdc = new Controllers();
//        $mdi = new Initializers();
//        $mdm = new Models();
//
//        $builders = $mdb->all();
//        $controllers = $mdc->all();
//        $initializers = $mdi->all();
//        $models = $mdm->all();

        $vars = self::variables();

        return $name == "all" ? $vars : (isset($vars[$name]) ? (array) $vars[$name] : []);

    }

//    public static function


}
