<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/28/2021
 * Time: 12:11 PM
 */

namespace Absoft\Line\Core\Engines\HTTP;

class EngineConfiguration {

    /**
     * @var array
     * this will hold all the configuration for view request
     */
    private $view_conf = [];

    /**
     * @var array
     * this will hold all the configuration for control request
     */
    private $control_conf = [];

    /**
     * this method provide the framework default configurations
     */
    private static function defaultConfig(){

    }

    /**
     * this method gives the ability to developers
     * for setting their preferred configuration
     */
    static function devConfig(){

    }

}
