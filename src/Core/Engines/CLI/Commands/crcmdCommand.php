<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/25/2021
 * Time: 3:16 PM
 */

namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\CommandCreator;

class crcmdCommand implements Command {

    private $the_command;
    private $description = "this command will help to create an other command";


    public function __construct(){
        $this->the_command = new CommandCreator();
    }

    function execute($arguments){
        $this->the_command->createCommand($arguments);
    }

    /**
     * this method will provide the description of the command
     * what it will do and what is expected after execution
     * @return string
     */
    function description(){
        return $this->description;
    }
}
