<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/26/2021
 * Time: 8:46 AM
 */

namespace Absoft\Line\App\Administration;


use Application\conf\DirConfiguration;
use Absoft\Line\App\Files\Resource;
use Absoft\Line\Core\FaultHandling\Errors\ControllersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;

class Controllers{

    private $content = '<?php
namespace Application\Controllers;

use Absoft\Line\Core\Modeling\Controller;

class @_nameController extends Controller{

    private function show(){
        //TODO: here write showing codes to be Executed
        return "";
    }
    
    private function view($request){
        //TODO: here write viewing codes to be Executed
        return "";
    }

    private function save($request){
        //TODO: Here write save codes to be Executed
        return "";
    }
    
    public function update($request){
        //TODO: here write updating codes to be Executed
        return "";
    }
    
    private function delete($request){
        //TODO: here write deleting codes to be Executed
        return "";
    }

}
?>';

    /**
     * @param $name
     * @return bool
     * @throws ControllersFolderNotFound
     * @throws OperationFailed
     *
     * this method will generate controller file with default codes.
     */
    public function create($name){

        $content = str_replace("@_name", $name, $this->content);

        if(!Resource::checkFile(DirConfiguration::$dir["controllers"]."/".$name."Controller.php")){

            if(file_put_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["controllers"]."/".$name."Controller.php", $content)){
                return true;
            }else{
                return false;
            }

        }else{
            throw new OperationFailed("file with the same name exist".DirConfiguration::$dir["controllers"]."/".$name."Controller.php");
        }

    }

    public function generateCRUDOperations(){

    }

    /**
     * @param $name
     * @return bool
     * @throws FileNotFound
     * @throws ControllersFolderNotFound
     * this method will delete/remove user/developer defined controller
     */
    public function delete($name){

        return Resource::deleteFile($name."Controller.php", "controllers");

//        if(Resource::checkFile(DirConfiguration::$dir["controllers"]."/".$name."Controller.php")){
//
//            if(unlink(DirConfiguration::$_main_folder.DirConfiguration::$dir["controllers"]."/".$name."Controller.php")){
//                return true;
//            }
//
//        }else{
//            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["controllers"]."/".$name."Controller.php", __File__, __Line__);
//        }
//
//        return false;

    }

    /**
     * @return array
     * @throws ControllersFolderNotFound
     * this method will provide list of user/developer defined controllers.
     */
    public function all() {

        $return = [];

        $addresses = Resource::getFiles("controllers");

        foreach ($addresses as $address) {
            $name = basename($address);
            $return[] = substr($name, 0, strpos($name, ".php"));
        }

//        if(Resource::checkFile(DirConfiguration::$dir["controllers"])){
//
//            if(is_dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["controllers"])){
//
//                $list = dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["controllers"]);
//
//                while(($file = $list->read()) != false) {
//
//                    if ($file == "." || $file == "..") {
//                        continue;
//                    } else {
//
//                        if(strpos($file, "Controller.php") > 0){
//                            $return[] = substr($file, 0, strpos($file, ".php"));
//                        }
//
//                    }
//
//                }
//
//            }
//            else{
//                throw new ControllersFolderNotFound(DirConfiguration::$dir["controllers"], __FILE__, __LINE__);
//            }
//
//        }
//        else{
//            throw new ControllersFolderNotFound(DirConfiguration::$dir["controllers"], __FILE__, __LINE__);
//        }

        return $return;

    }

}
