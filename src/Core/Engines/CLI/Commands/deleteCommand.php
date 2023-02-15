<?php
    
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\FileRemover;

class deleteCommand implements Command
{

    private $the_command;
    private $description = "";

    public function __construct(){

        $this->the_command = new FileRemover();

    }

    function execute($arguments)
    {

        $this->the_command->remove($arguments);

    }

    function description(){
        return $this->description;
    }
    
}
