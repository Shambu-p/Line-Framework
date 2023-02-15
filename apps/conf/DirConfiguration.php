<?php

/**
 * @author Abnet Kebede
 * @meta time: 9:30 am date: sunday april 4 2013 E.C.
 */

namespace Application\conf;


class DirConfiguration {

    public static $_main_folder = "";
    public static $dir = [
        "controllers" => "/apps/Controllers",
        "builders" => "/apps/Builders",
        "models" => "/apps/Models",
        "templates" => "/apps/Templates",
        "initializers" => "/apps/Initializers",
        "css" => "/apps/src/css",
        "js" => "/apps/src/js",
        "resources" => "/apps/resources",
        "configurations" => "/apps/conf"
    ];

}
