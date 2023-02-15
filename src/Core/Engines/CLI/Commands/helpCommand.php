<?php
    
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\TestReceiver;

class helpCommand implements Command
{

    private $the_command;
    private $description = "this command will show all or specified command description";

    public function __construct(){

        $this->the_command = new TestReceiver();

    }

    function execute($arguments)
    {

        $this->the_command->help($arguments);

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
