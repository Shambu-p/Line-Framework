<?php
    
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\DBManagement;

class initCommand implements Command
{

    private $the_command;
    private $description = "";

    public function __construct(){

        $this->the_command = new DBManagement();

    }

    function execute($arguments)
    {

        $this->the_command->initialize($arguments);

    }

    function description()
    {
        return $this->description;
    }
}
    