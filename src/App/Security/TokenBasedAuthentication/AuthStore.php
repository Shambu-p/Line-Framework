<?php


namespace Absoft\Line\App\Security\TokenBasedAuthentication;


use Absoft\Line\App\Security\AuthorizationManagement;
use Absoft\Line\App\Security\Interfaces\AuthStoreInterface;
use Application\conf\AuthConfiguration;
use Application\conf\DirConfiguration;

class AuthStore implements AuthStoreInterface {

    private static $file_address = "/apps/Runtime/authorization.json";

    public function save(array $data, string $name) {

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.self::$file_address));

        $token = $this->getToken(10);
        $tobe_saved = (array) $auths->$name;
        $tobe_saved[$token] = (object) $data;
        $auths->$name = (object) $tobe_saved;

        $data["token"] = $token;
        file_put_contents(DirConfiguration::$_main_folder.self::$file_address, json_encode($auths));

        return $data;

    }

    public function read(string $name, string $token = null) {

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.self::$file_address));
        return isset($auths->$name->$token) ? (array) $auths->$name->$token : null;

    }

    public function delete(string $name, string $token = null) {

        $auths = json_decode(file_get_contents(DirConfiguration::$_main_folder.self::$file_address));

        if(isset($auths->$name->$token)) {
            unset($auths->$name->$token);
            file_put_contents(DirConfiguration::$_main_folder.self::$file_address, json_encode($auths));
        }

    }

    public function getToken($length){

        $char_array = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];

        $i = 0;
        $token = "";

        while($i < $length){

            $token .= $char_array[rand(0, 61)];
            $i += 1;

        }

        return $token;

    }

}