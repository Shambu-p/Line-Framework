<?php


namespace Absoft\Line\Core\Engines\CLI;


use Absoft\Line\Core\Engines\CLI\Commands\Command;

class Invoker
{

    private $the_command;

    /**
     * Invoker constructor.
     * @param Command $my_command
     */
    function __construct(Command $my_command){

        $this->the_command = $my_command;

    }

    function invoke($arguments){

        $this->the_command->execute($arguments);

    }

}