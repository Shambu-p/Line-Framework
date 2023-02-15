<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 4/25/2021
 * Time: 3:25 PM
 */

namespace Absoft\Line\Core\Engines\CLI\Receivers;


use Absoft\Line\App\Administration\Administration;
use Absoft\Line\App\Administration\Builders;
use Absoft\Line\App\Administration\Controllers;
use Absoft\Line\App\Administration\Initializers;
use Absoft\Line\App\Administration\Models;
use Absoft\Line\Core\FaultHandling\Errors\BuildersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\ControllersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Errors\ModelsFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;

class FileGenerator {


    public function generate($argument){

        $type = $argument["type"];
        $name = $argument["name"];

        if($type == "-c" || $type == "-controller" || $type == "controller"){
            $this->createController($name);
            Administration::changeVariable($name, "controller", true);
        }
        else if($type == "-b" || $type == "-builder" || $type == "builder"){
            $this->createBuilder($name);
            Administration::changeVariable($name, "builder", true);
        }
        else if($type == "-m" || $type == "-model" || $type == "model"){
            $this->createModel($name);
            Administration::changeVariable($name, "model", true);
        }
        else if($type == "-i" || $type == "-initializer" || $type == "initializer"){
            $this->createInitializer($name);
            Administration::changeVariable($name, "initializer", true);
        }
        else if($type == "-mbc" || $type == "-mcb" || $type == "-bmc" || $type == "-bcm" || $type == "-cmb" || $type == "-cbm"){

            $this->createModel($name);
            $this->createBuilder($name);
            $this->createController($name);
            Administration::createVariable($name, false, false, true, true, true, false);

        }
        else{
            print "incorrect or unknown type of generation encountered";
        }

    }

    private function createController($name){

        $controller = new Controllers();

        try{

            if($controller->create($name)){
                print "Controller generated Successfully. \n";
            }else{
                print "Run into problem";
            }

        } catch (ControllersFolderNotFound $e) {
            print $e->getMessage();
        } catch (OperationFailed $e) {
            print $e->getMessage();
        }

    }

    private function createInitializer($name){

        $initial = new Initializers();

        try{

            $initial->create($name);
            print "Initializer generated successfully \n";

        } catch (OperationFailed $e) {
            print $e->getMessage();
        }

    }

    private function createModel($name){

        $model = new Models();

        try {
            if ($model->create($name)) {
                print "Model generated Successfully. \n";
            } else {
                print "Run into problem";
            }
        } catch (ModelsFolderNotFound $e) {
            print $e->getMessage();
        } catch (OperationFailed $e) {
            print $e->getMessage();
        }

    }

    private function createBuilder($name){

        $builder = new Builders();

        try{

            if($builder->create($name)){
                print "Builder generated Successfully. \n";
            }else{
                print "System run into problem";
            }

        } catch (BuildersFolderNotFound $e) {
            print $e->getMessage();
        } catch (OperationFailed $e) {
            print $e->getMessage();
        }

    }

}
