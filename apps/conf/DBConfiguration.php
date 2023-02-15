<?php
/**
 * Created by PhpStorm.
 * User: Abnet Kebede
 * Date: 3/1/2021
 * Time: 11:46 AM
 */
namespace Application\conf;

class DBConfiguration {

    public static $conf = [
        "MySql" => [
            "second" => [
                "DB_NAME" => "test",
                "DB_USERNAME" => "phpmyadmin",
                "DB_PASSWORD" => "password",
                "HOST_ADDRESS" => "localhost:3306"
            ],
            "third" => [
                "DB_NAME" => "ab_keno",
                "DB_USERNAME" => "root",
                "DB_PASSWORD" => "password",
                "HOST_ADDRESS" => "localhost:3306"
            ],
            "first" => [
                "DB_NAME" => "clinic_db",
                "DB_USERNAME" => "root",
                "DB_PASSWORD" => "password",
                "HOST_ADDRESS" => "localhost:3306"
            ]
        ],
        "MsSql" => [
            "first" => [
                "DB_NAME" => "my_school",
                "DB_USERNAME" => "",
                "DB_PASSWORD" => "",
                "HOST_ADDRESS" => "localhost"
            ],
            "second" => [
                "DB_NAME" => "my_school",
                "DB_USERNAME" => "",
                "DB_PASSWORD" => "",
                "HOST_ADDRESS" => "localhost"
            ]
        ],
        "SQLite" => [
            "first" => [
                "DB_NAME" => "system",
                "DB_USERNAME" => "",
                "DB_PASSWORD" => "",
                "HOST_ADDRESS" => "{{main_folder}}/apps/Runtime/runtime.db"
            ]
        ]
    ];

}
