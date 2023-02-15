<?php


namespace Absoft\Line\Core\HTTP;


use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;

class DataTransfer {

    public static function get($route_name){

        if(file_exists(__DIR__ . "/transfer.tr")){
            $data = json_decode(file_get_contents(__DIR__ . "/transfer.tr"));
            return $data->$route_name;
        }
        else{
            file_put_contents(__DIR__ . "/transfer.tr", "{}");
            return new \stdClass();
        }

    }

    public static function set($route_name, $request){

        if(file_exists(__DIR__ . "/transfer.tr")){
            $data = json_decode(file_get_contents(__DIR__ . "/transfer.tr"));
        }else{
            $data = new \stdClass();
        }

        $data->$route_name = (object) $request;

        file_put_contents(__DIR__ . "/transfer.tr", json_encode($data));

    }

}