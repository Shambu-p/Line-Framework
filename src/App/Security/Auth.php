<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 3/1/2021
 * Time: 9:40 AM
 */
namespace Absoft\Line\App\Security;

use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Models\Model;
use Application\conf\AuthConfiguration;

class Auth {

    public static function grant(Array $user, $auth_name) {

        $model_name = AuthConfiguration::$conf[$auth_name]["table"];

        /** @var Model $user_model */
        $user_model = new $model_name;

        foreach ($user_model->MAINS as $main) {

            if(isset($user[$main])){
                $_SESSION["auth"][$auth_name][$main] = $user[$main];
            }

        }

        $_SESSION["auth"][$auth_name]["auth_date_time"] = strtotime("now");

        return $_SESSION["auth"][$auth_name];

    }

    public static function user($auth_name){

        if(isset($_SESSION["auth"][$auth_name]) && !empty($_SESSION["auth"][$auth_name])){
            return $_SESSION["auth"][$auth_name];
        }

        return null;

    }

    /**
     * @param $auth_name
     * @return bool
     * @throws OperationFailed
     */
    public static function deni($auth_name){
        try{
            unset($_SESSION["auth"][$auth_name]);
            return true;
        }catch(\Exception $exception){
            throw new OperationFailed($exception->getMessage());
        }
    }

    public static function checkLogin($auth_name) {
        return (isset($_SESSION["auth"][$auth_name]) && !empty($_SESSION["auth"][$auth_name]));
    }

    public static function checkUser($key, $value, $auth_name){

        $user = self::user($auth_name);
        return (self::checkLogin($auth_name) && $user && $user[$key] == $value);

    }

    /**
     * @param $auth_name
     * @param array $parameters
     * @return array|mixed
     * @throws DBConnectionError|ExecutionException
     */
    public static function Authenticate($auth_name, array $parameters){

        $p_size = sizeof($parameters);
        $w_size = sizeof(AuthConfiguration::$conf[$auth_name]["with"]);

        if($p_size != $w_size || $p_size == 0 || $w_size == 0 || $p_size < $w_size){
            return [];
        }

        $model_name = AuthConfiguration::$conf[$auth_name]["table"];

        /** @var Model $model */
        $model = new $model_name();

        if(!in_array(AuthConfiguration::$conf[$auth_name]["with"][0], $model->MAINS) && !in_array(AuthConfiguration::$conf[$auth_name]["with"][0], $model->HIDDEN)){
            return [];
        }

        if(isset(AuthConfiguration::$conf[$auth_name]["with"][1]) && !in_array(AuthConfiguration::$conf[$auth_name]["with"][1], $model->MAINS) && !in_array(AuthConfiguration::$conf[$auth_name]["with"][1], $model->HIDDEN)){
            return [];
        }

        if(sizeof(AuthConfiguration::$conf[$auth_name]["with"]) && AuthConfiguration::$conf[$auth_name]["order"] == "keep"){

            $qr = $model->searchRecord();
            $qr->where(AuthConfiguration::$conf[$auth_name]["with"][0], $parameters[0]);

            $query_result = $qr->fetch();

            $result = $query_result->fetchAll();


            if(isset(AuthConfiguration::$conf[$auth_name]["with"][1])){

                if(in_array(AuthConfiguration::$conf[$auth_name]["with"][1], $model->HIDDEN)) {

                    foreach($result as $res){

                        if(password_verify($parameters[1], $res[AuthConfiguration::$conf[$auth_name]["with"][1]])){
                            //$token = AuthorizationManagement::set($res, "user_auth");
                            return $res;
                        }

                    }

                }
                else if(in_array(AuthConfiguration::$conf[$auth_name]["with"][1], $model->MAINS)){
                    foreach($result as $res){

                        if(password_verify($parameters[1], $res[AuthConfiguration::$conf[$auth_name]["with"][1]])){
                            //$token = AuthorizationManagement::set($res, "user_auth");
                            return $res;
                        }

                    }
                }

            }

        }
        else if(sizeof(AuthConfiguration::$conf[$auth_name]["with"]) && isset(AuthConfiguration::$conf[$auth_name]["order"]) && AuthConfiguration::$conf[$auth_name]["order"] == "once"){

            $count = 0;
            $qr = $model->searchRecord();

            foreach(AuthConfiguration::$conf[$auth_name]["with"] as $with){

                $qr->where($with, $parameters[$count]);
                $count += 1;

            }

            //$token = AuthorizationManagement::set($res, "user_auth");
            $result = $qr->fetch();
            return $result->fetchAll();

        }

        return [];

    }

}
