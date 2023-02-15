<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 3/1/2021
 * Time: 7:48 PM
 */
namespace Absoft\Line\Core\Engines\CLI\Commands;

use Absoft\Line\Core\Engines\CLI\CLIConfiguration;

class CommandFactory
{

    /**
     * this method will find the receiver by taking receivers name as string
     * and then it will return the object of that class
     * @param string $command_name
     * @return object
     */
    public static function get(string $command_name){

        $command_name = 'Absoft\\Line\\Core\\Engines\\CLI\\Commands\\'.$command_name."Command";

        try {

            $class = new \ReflectionClass($command_name);

            return $class->newInstance();

        } catch (\ReflectionException $e) {

            print $e->getMessage();
            return null;

        }

    }


    /**
     * @param string $name
     * this parameter is for setting the receiver command name or short name
     * @param string $receiver_full_name
     * this parameter is for setting the receiver name in namespace.
     * for example this class full name is Absoft\Line\Engines\CLI\Receivers\ReceiverFactory
     * this parameter should be set as string.
     */
    public static function set($name, $receiver_full_name){

    }

}

?>
