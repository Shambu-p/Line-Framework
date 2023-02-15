<?php


namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\TestReceiver;

class startServerCommand implements Command
{

    private $the_command;
    private $description = "this command will start server according to user interest";

    public function __construct(){

        $this->the_command = new TestReceiver();

    }

    function execute($arguments)
    {

        $this->the_command->startServer($arguments);

    }

    /**
     * this method will provide the description of the command
     * what it will do and what is expected after execution
     * @return string
     */
    function description()
    {
        return $this->description;
    }
}
