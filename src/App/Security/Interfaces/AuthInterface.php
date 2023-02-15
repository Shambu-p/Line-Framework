<?php


namespace Absoft\Line\App\Security\Interfaces;


Interface AuthInterface {
    public function grant(array $data, string $name);
    public function deni(string $name);
    public function user(string $name);
    public function check($key, $value, $name);
}