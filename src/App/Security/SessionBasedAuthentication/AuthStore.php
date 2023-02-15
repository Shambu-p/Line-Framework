<?php


namespace Absoft\Line\App\Security\SessionBasedAuthentication;


use Absoft\Line\App\Security\Interfaces\AuthStoreInterface;
use Application\conf\AuthConfiguration;

class AuthStore implements AuthStoreInterface {

    public function save(array $data, string $name) {
        $data["auth_date_time"] = strtotime("now");
        $_SESSION[AuthConfiguration::$parameter][$name] = $data;
    }

    public function read(string $name, string $token = null) {

        if(isset($_SESSION[AuthConfiguration::$parameter][$name])){
            return $_SESSION[AuthConfiguration::$parameter][$name];
        }

        return null;

    }

    public function delete(string $name, string $token = null) {
        if(isset($_SESSION[AuthConfiguration::$parameter][$name])) {
            unset($_SESSION[AuthConfiguration::$parameter][$name]);
        }
    }

}