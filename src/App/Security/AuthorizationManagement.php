<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/26/2021
 * Time: 4:56 PM
 */

namespace Absoft\Line\App\Security;


use Application\conf\DirConfiguration;
use Application\conf\AuthConfiguration;
use stdClass;

class AuthorizationManagement {

    private static $file_address = "/apps/Runtime/authorization.json";

    public static function getToken($length = 10){

        $char_array = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];

        $i = 0;
        $token = "";

        while($i < $length){

            $token .= $char_array[rand(0, 61)];
            $i += 1;

        }

        return $token;

    }

    public static function set($record, $auth){

        $token = self::getToken(20);
        $user = ["date" => strtotime("now")];

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address));

        foreach (AuthConfiguration::$conf[$auth]["save"] as $save){
            $user[$save] = $record[$save];
        }

        $tobe_saved = (array) $auths->$auth;
        $tobe_saved[$token] = (object) $user;
        $auths->$auth = (object) $tobe_saved;

        file_put_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address, json_encode($auths));

        return $token;

    }

    /**
     * @param $token
     * @param $auth
     * @return array
     */
    public static function update($token, $auth){

        $record = self::viewAuth($token, $auth);

        if(sizeof($record) == 0){
            return [];
        }

        $user = ["date" => strtotime("now")];
        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.self::$file_address));

        foreach (AuthConfiguration::$conf[$auth]["save"] as $save){
            $user[$save] = $record[$save];
        }

        $auths->$auth->$token = (object) $user;
        file_put_contents(DirConfiguration::$_main_folder.self::$file_address, json_encode($auths));

        return $user;

    }

    public static function delete($token, $auth){

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address));

        if(isset($auths->$auth->$token)){
            unset($auths->$auth->$token);
            file_put_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address, json_encode($auths));
        }

        return true;

    }

    public static function checkAuthorization($token, $auth){

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address));

        if(isset($auths->$auth->$token) && (intval(strtotime("now")) < ((3600 * doubleval(AuthConfiguration::$TOKEN_EXPIRATION)) + intval($auths->$auth->$token->date)))){
            return true;
        }

        return false;

    }

    public static function getAuth($token, $auth){

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address));

        if(!isset($auths->$auth->$token)){
            return [];
        }

        $saved = $auths->$auth->$token;

        if(intval(strtotime("now")) > ((3600 * doubleval(AuthConfiguration::$TOKEN_EXPIRATION)) + intval($saved->date))){
            return [];
        }

        $auths->$auth->$token->date = strtotime("now");
        file_put_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address, json_encode($auths));
        return (array) $auths->$auth->$token;

    }

    public static function viewAuth($token, $auth){

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.AuthorizationManagement::$file_address));

        if(isset($auths->$auth->$token)){
            return (array) $auths->$auth->$token;
        }

        return [];

    }

}
