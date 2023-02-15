<?php
    
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\FileGenerator;

class createCommand implements Command
{

    private $the_command;
    private $description = "this command will generate file such as models, controllers and builders";

    public function __construct(){

        $this->the_command = new FileGenerator();

    }

    function execute($arguments)
    {

        $this->the_command->generate($arguments);

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
