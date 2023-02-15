<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/25/2021
 * Time: 2:58 PM
 */

namespace Absoft\Line\Core\Engines\CLI\Receivers;


use Application\conf\DirConfiguration;

class CommandCreator {

    private $content = '<?php
    
namespace Absoft\Line\Engines\CLI\Commands;


use Absoft\Line\Engines\CLI\Receivers\@_receiver;

class @_commandCommand implements Command
{

    private $the_command;
    private $description = "";

    public function __construct(){

        $this->the_command = new @_receiver();

    }

    function execute($arguments)
    {

        $this->the_command->@_method($arguments);

    }

    function description(){
        return $this->description;
    }
    
}
    ';

    public function createCommand($arguments){

        $name = $arguments["name"];
        $receiver = $arguments["receiver"];
        $method = $arguments["method"];

        //$file = fopen($arguments["main_folder"]."/System/Absoft/Line/Engines/CLI/commands/$name", "w+");

        $content = str_replace("@_command", $name, $this->content);
        $content = str_replace("@_receiver", $receiver, $content);
        $content = str_replace("@_method", $method, $content);

        if(file_put_contents($arguments["main_folder"]."/System/Absoft/Line/Engines/CLI/commands/".$name."Command.php", $content)){
            print "File generated Successfully. \n";
        }else{
            print "Run into problem";
        }

    }

}
