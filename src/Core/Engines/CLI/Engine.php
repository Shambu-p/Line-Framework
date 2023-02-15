<?php
namespace Absoft\Line\Core\Engines\CLI;

use Application\conf\DirConfiguration;
use Absoft\Line\Core\Engines\CLI\Commands\Command;
use Absoft\Line\Core\Engines\CLI\Commands\CommandFactory;
use Absoft\Line\Core\FaultHandling\FaultHandler;
use Application\conf\Configuration;

class Engine
{

    private $arguments = [];
    private $argument_count = 0;
    private $main_folder;

    function __construct($arguments, $main_folder){

        FaultHandler::fromCLI();
        $this->arguments = $arguments;
        $this->argument_count = sizeof($arguments);
        $this->main_folder = $main_folder;
        Configuration::$conf["type"] = "cmd";
        DirConfiguration::$_main_folder = $main_folder;

    }

    /**
     * @return void
     * every execution start here every command is going to be invoked from here.
     * this method uses getArguments method.
     */
    function start(){

        $requests = $this->getArguments();
        $requests["main_folder"] = $this->main_folder;

        /** @var Command $command */
        $command = CommandFactory::get($requests["command"]);

        if($command){

            $invoker = new Invoker($command);
            $invoker->invoke($requests["arguments"]);

        }
        else{
            print "Unknown absoft command. \n Command cannot be initiated \n";
        }

    }

    function getArguments(){

        $return = [];

        if($this->argument_count > 2){

            $return["command"] = $this->arguments[1];
            $return["arguments"]["main_folder"] = $this->main_folder;

            if(isset(CLIConfiguration::$map[$this->arguments[1]])){

                $conf = CLIConfiguration::$map[$this->arguments[1]];

                for($i = 0; $i < sizeof($conf); $i ++){

                    if(isset($this->arguments[$i+2])){

                        $return["arguments"][$conf[$i]["name"]] = $this->arguments[$i+2];

                    }else if(!$conf[$i]["importance"]){
                        continue;
                    }else{
                        print "\n important argument named ".$conf[$i]["name"]." missed\n";
                        die();
                    }

                }

            }
            else{
                print "\n unknown command \n command doesn't have arguments specification";
            }

        }
        else if($this->argument_count == 2){

            $return["command"] = $this->arguments[1];
            $return["arguments"]["main_folder"] = $this->main_folder;

            if(!isset(CLIConfiguration::$map[$this->arguments[1]])){
                print "unknown command \n command doesn't have arguments specification";
            }

        }
        else{
            die("incorrect argument");
        }

        return $return;

    }

}
