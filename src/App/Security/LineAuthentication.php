<?php


namespace Absoft\Line\App\Security;


use Absoft\Line\App\Security\Interfaces\AuthInterface;
use Absoft\Line\App\Security\TokenBasedAuthentication\Authentication;
use Absoft\Line\App\Security\SessionBasedAuthentication\Authentication as TokenAuthentication;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\Modeling\Models\Model;
use Application\conf\AuthConfiguration;

class LineAuthentication {

    /**
     * @param array $data
     * @param string $name
     * @return array|void
     */
    public static function grant(array $data, string $name){
        return self::getAuthenticatorObject($name)->grant($data, $name);
    }

    /**
     * @param string $name
     */
    public static function deni(string $name) {
        self::getAuthenticatorObject($name)->deni($name);
    }

    /**
     * @param string $name
     * @return array|mixed|null
     */
    public static function user(string $name) {
        return self::getAuthenticatorObject($name)->user($name);
    }

    public static function checkLogin($name){
        $user = self::user($name);
        return !empty($user);
    }

    /**
     * @param $key
     * @param $value
     * @param $name
     * @return bool
     */
    public static function check($key, $value, $name) {
        return self::getAuthenticatorObject($name)->check($key, $value, $name);
    }

    /**
     * @param $name
     * @return AuthInterface
     */
    private static function getAuthenticatorObject($name){

        $auth_type = (isset(AuthConfiguration::$conf[$name]["is_token_based"]) && AuthConfiguration::$conf[$name]["is_token_based"]) ? "token_based" : "session_based";

        switch (strtolower($auth_type)){
            case "token_based":
                return new Authentication();
            default:
                return new TokenAuthentication();
        }

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