<?php
/**
 * @author Abnet Kebede
 */
namespace Absoft\Line\Core\Engines\CLI\Receivers;


use Absoft\Line\Core\Engines\CLI\CLIConfiguration;
use Absoft\Line\Core\Engines\CLI\Commands\Command;
use Absoft\Line\Core\Engines\CLI\Commands\CommandFactory;

class TestReceiver
{

    public function fileCreate($arguments){

        $location = $arguments["main_folder"]."/apps/".$arguments["file_name"];

        $file = fopen($location, "w");

        fwrite($file, "hello");
        fclose($file);

        print "file has been generated \n";

    }

    public function startServer($arguments){

        if(isset($arguments["host"])){
            shell_exec("php -S ".$arguments["host"].":1111 -t ".$arguments["main_folder"]."/public");
            print "line development server started on ". $arguments["host"].":1111";
        }
        else{
            shell_exec("php -S localhost:1111 -t ".$arguments["main_folder"] . "/public");
            print "line development server started on localhost:1111";
        }

    }

    public function help($arguments){

        if(isset($arguments["command"])){
            $this->specificHelp($arguments["command"]);
        }else{
            $this->generalHelp();
        }

    }

    private function specificHelp($command){

        $cmd_description = "";
        $param_description = "";

        if(isset(CLIConfiguration::$map[$command])){

            $cmd_arr = CLIConfiguration::$map[$command];
            /** @var Command $cmd */
            $cmd = CommandFactory::get($command);

            if($cmd){

                $cmd_description = $cmd->description()."\n";

                foreach($cmd_arr as $param){

                    if($param["importance"]){
                        $param_description .= "\n [".$param["name"]."]\t "."cannot be empty"."\t ".$param["description"]."\n";
                    }else{
                        $param_description .= "\n [".$param["name"]."]\t "."can be empty"."\t ".$param["description"]."\n";
                    }

                }

                print $cmd_description."\n";
                print $param_description."\n";

            }

        }else{
            die("\n command not found \n");
        }


    }

    private function generalHelp(){

        $arr = array_keys(CLIConfiguration::$map);
        $show = "\n";

        foreach($arr as $param){

            /** @var Command $temp */
            $temp = CommandFactory::get($param);

            if($temp){
                $show .= $param."\t\t".$temp->description()."\n";
            }else{
                die();
            }

        }

        print $show;

    }

}
