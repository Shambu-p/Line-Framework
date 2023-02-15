<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/30/2021
 * Time: 9:52 AM
 */

namespace Absoft\Line\Core\Engines\CLI\Receivers;


use Absoft\Line\App\Administration\Administration;
use Absoft\Line\App\Administration\Builders;
use Absoft\Line\App\Administration\Initializers;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;

class DBManagement {

    public function export($arguments){

        if(isset($arguments["builder"])){

            $builders = explode("/", $arguments["builder"]);

            foreach ($builders as $tb_name){

                $this->singleExport($tb_name);

            }

        }
        else{
            print "Parameter missed no builder name provided \n";
        }

    }

    private function singleExport($name){

        try {

            $builder = new Builders();
            $builder->execute($name);
            Administration::changeVariable($name, "exported", true);
            print "Table named ".$name." has been created. \n";

        } catch (ClassNotFound $e) {
            print $e->getMessage();
        }

    }

    public function drop($arguments){

        try {

            if($arguments["builder"]){

                $builder = new Builders();
                $builder->drop($arguments["builder"]);
                Administration::changeVariable($arguments["builder"], "exported", false);
                print "a Table named ".$arguments["builder"]." has been dropped. \n";

            }else{
                print "Parameter missed! no Builder name provided \n";
            }

        } catch (ClassNotFound $e) {

            print $e->getMessage();

        }

    }

    public function initialize($arguments){

        try {

            if($arguments["builder"]){

                $initializer = new Initializers();
                if($initializer->init($arguments["builder"])){
                    print "Table populated!";
                    Administration::changeVariable($arguments["builder"], "initiated", true);
                }else{
                    print "Ran into problem!";
                }

            }else{
                print "Parameter missed! empty or no Builder name provided";
            }

        } catch (ClassNotFound $e) {
            print $e->getMessage();
        }

    }

}
