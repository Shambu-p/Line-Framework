<?php


namespace Absoft\Line\App\Security\TokenBasedAuthentication;


use Absoft\Line\App\Security\Interfaces\AuthStoreInterface;
use Application\conf\AuthConfiguration;

class Authentication implements \Absoft\Line\App\Security\Interfaces\AuthInterface
{

    private AuthStoreInterface $store;

    public function __construct(){
        $this->store = new AuthStore();
    }

    public function grant(array $data, string $name) {

        $user = [];
        foreach (AuthConfiguration::$conf[$name]["save"] as $save){
            $user[$save] = $data[$save];
        }
        return $this->store->save($user, $name);

    }

    public function deni(string $name) {
        $token = $this->getTokenFromHeader();
        $this->store->delete($token);
    }

    public function user(string $name) {
        $token = $this->getTokenFromHeader();
        return $this->store->read($name, $token);
    }

    public function check($key, $value, $name) {

        $user = $this->user($name);
        return ($user && isset($user[$key]) && $user[$key] == $value);

    }

    private function getTokenFromHeader() {
        return $_SERVER[AuthConfiguration::$parameter] ?? "";
    }

}