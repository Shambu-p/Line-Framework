<?php


namespace Absoft\Line\App\Security\Interfaces;


Interface AuthStoreInterface {
    public function save(array $data, string $name);
    public function read(string $name, string $token = null);
    public function delete(string $name, string $token = null);
}