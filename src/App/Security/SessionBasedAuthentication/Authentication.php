<?php


namespace Absoft\Line\App\Security\SessionBasedAuthentication;


use Absoft\Line\App\Security\Interfaces\AuthInterface;
use Absoft\Line\App\Security\Interfaces\AuthStoreInterface;
use Absoft\Line\Core\Modeling\Models\Model;
use Application\conf\AuthConfiguration;

class Authentication implements AuthInterface {

    private AuthStoreInterface $store;

    public function __construct(){
        $this->store = new AuthStore();
    }

    /**
     * @param array $data
     * @param string $name
     * @return array
     */
    public function grant(array $data, string $name) {

        $model_name = AuthConfiguration::$conf[$name]["table"];
        $user = [];

        /** @var Model $user_model */
        $user_model = new $model_name;
        $columns = $user_model->MAINS ?? [];

        foreach ($columns as $main) {

            if(isset($data[$main])){
                $user[$main] = $data[$main];
            }

        }

        $this->store->save($user, $name);

        return $data;

    }

    /**
     * @param string $name
     */
    public function deni(string $name) {
        $this->store->delete($name);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function user(string $name) {
        return $this->store->read($name);
    }

    /**
     * @param $key
     * @param $value
     * @param $name
     * @return bool
     */
    public function check($key, $value, $name) {

        $user = $this->store->read($name);
        return ($user && is_array($user) && isset($user[$key]) && $user[$key] == $value);

    }

}