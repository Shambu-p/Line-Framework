<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/27/2021
 * Time: 12:08 AM
 */

namespace Application\conf;

class Configuration {

    public static $conf = [
        "title" => "Travel Assistant",
        "type" => "UI"
    ];

    public static $admin_conf = [
        "cli" => false,
        "webAPI" => false,
        "DB_SERVER" => "MySql",
        "DATABASE_NAME" => "first"
    ];

    public static $alert_setup = [
        "success_class_name" => "alert alert-success alert-dismissible",
        "error_class_name" => "alert alert-danger alert-dismissible",
        "info_class_name" => "alert alert-info alert-dismissible"
    ];

}
