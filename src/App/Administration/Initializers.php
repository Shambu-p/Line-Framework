<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/26/2021
 * Time: 11:54 AM
 */

namespace Absoft\Line\App\Administration;


use Application\conf\DirConfiguration;
use Absoft\Line\App\Files\Resource;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Initializer;

class Initializers
{

    private $content = '<?php
namespace Application\Initializers;

use Absoft\Line\Modeling\Initializer;

class @_nameInitializer extends Initializer{

    /*
    public $VALUES = [
        [
            "id" => "the_id",
            "name" => "the_name",
        ],
        [
            "id" => "the_id",
            "name" => "the_name"
        ]
    ];

    */
    
    public $BUILDER = "@_name";

    /*************************************************************************
        In this property you are expected to put all the values you want
        to insert into database. the you can initialize the operation from
        line cli.
    *************************************************************************/

    public $VALUES = [
        [
            "username" => "@admin"
        ]
    ];
    
}
?>';


    /**
     * @param $name
     * @return bool
     * @throws OperationFailed
     * this method will generate user defined initializer file with example data
     */
    public function create($name){

        $content = str_replace("@_name", $name, $this->content);

        if(!Resource::checkFile(DirConfiguration::$dir["initializers"]."/".$name."Initializer.php")){

            if(file_put_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"]."/".$name."Initializer.php", $content)){
                return true;
            }else{
                throw new OperationFailed("cannot generate file ".DirConfiguration::$dir["initializers"]."/".$name."Initializer.php");
            }

        }else{
            throw new OperationFailed("same file exists ".DirConfiguration::$dir["initializers"]."/".$name."Initializer.php");
        }

    }


    /**
     * @param $name
     * @return bool
     * @throws FileNotFound
     * this method will delete/remove User defined initializer file form initializers folder.
     */
    public function delete($name){

        if(Resource::checkFile(DirConfiguration::$dir["initializers"]."/".$name."Initializer.php")){

            if(unlink(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"]."/".$name."Initializer.php")){
                return true;
            }

        }else{
            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"]."/".$name."Initializer.php", __File__, __Line__);
        }

        return false;

    }

    /**
     * @param string $initializer
     * @return bool
     * @throws ClassNotFound
     */
    public function init($initializer){

        $full_name = "Application\\Initializers\\".$initializer."Initializer";

        /** @var Initializer $initial */
        if($initial = new $full_name){

            return $initial->initialize($initializer);

        }else{
            throw new ClassNotFound($full_name, "initializer initialization file", __LINE__);
        }

    }

    /**
     * @return array
     * @throws FileNotFound
     */
    public function all(){

        $return = [];

        if(Resource::checkFile(DirConfiguration::$dir["initializers"])){

            if(is_dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"])){

                $list = dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"]);

                while(($file = $list->read()) != false) {

                    if ($file == "." || $file == "..") {

                        continue;

                    } else {

                        $return[] = substr($file, 0, strpos($file, "Initializer.php"));

                    }

                }


            }
            else{

                throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"], __FILE__, __LINE__);
                //$return["error_message"] = " DatabaseBuilder folder has been misplaced or file type has been changed ";

            }

        }
        else{

            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["initializers"], __FILE__, __LINE__);
            //$return["error_message"] = " DatabaseBuilder folder has been deleted or misplaced. ";

        }

        return $return;

    }

}
