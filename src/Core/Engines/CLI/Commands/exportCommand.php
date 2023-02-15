<?php
    
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\DBManagement;

class exportCommand implements Command
{

    private $the_command;
    private $description = "this command will create table on the database";

    public function __construct(){

        $this->the_command = new DBManagement();

    }

    function execute($arguments)
    {

        $this->the_command->export($arguments);

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
