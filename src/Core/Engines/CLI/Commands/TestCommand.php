<?php
namespace Absoft\Line\Core\Engines\CLI\Commands;


use Absoft\Line\Core\Engines\CLI\Receivers\TestReceiver;

class TestCommand implements Command
{

    private $the_command;

    public function __construct(){

        $this->the_command = new TestReceiver();

    }

    function execute($arguments)
    {

        $this->the_command->fileCreate($arguments);

    }
}