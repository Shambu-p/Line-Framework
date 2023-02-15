<?php
    
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\DBManagement;

class dropCommand implements Command
{

    private $the_command;
    private $description = "this command will delete table from database";

    public function __construct(){

        $this->the_command = new DBManagement();

    }

    function execute($arguments)
    {

        $this->the_command->drop($arguments);

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
