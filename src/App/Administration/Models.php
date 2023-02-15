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
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Errors\ModelsFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;

class Models
{

    private $content = '<?php
namespace Application\Models;

use Absoft\Line\Core\Modeling\Models\Model;

class @_nameModel extends Model{

    /*    public $MAINS = ["id", "username", "f_name"];    */
    
    //As the name indicate this is the Table name of the Model
    
    public $TABLE_NAME = "@_name";

    /**********************************************************************
        In this property you are expected to put all the columns you want
        other than the fields you want to be hashed.
    ***********************************************************************/

    public $MAINS = ["id"];
    
    /**********************************************************************
        In this field you are expected to put all columns you want to be
        encrypted or hashed.
    ***********************************************************************/
    
    public $HIDDEN = ["id"];

}
?>';

    /**
     * @param $name
     * @return bool
     * @throws ModelsFolderNotFound
     * @throws OperationFailed
     * this method will generate model file with default attributes
     */
    public function create($name){

        $content = str_replace("@_name", $name, $this->content);

        if(!Resource::checkFile(DirConfiguration::$dir["models"]."/".$name."Model.php")){

            if(file_put_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["models"]."/".$name."Model.php", $content)){
                return true;
            }else{
                return false;
            }

        }else{
            throw new OperationFailed("same file exist ".DirConfiguration::$_main_folder.DirConfiguration::$dir["models"]."/".$name."Model.php");
        }

    }

    /**
     * @param $name
     * @return bool
     * @throws FileNotFound
     * this method will delete/remove user/developer defined model file.
     */
    public function delete($name){

        if(Resource::checkFile(DirConfiguration::$dir["models"]."/".$name."Model.php")){

            if(unlink(DirConfiguration::$_main_folder.DirConfiguration::$dir["models"]."/".$name."Model.php")){
                return true;
            }

        }else{
            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["models"]."/".$name."Model.php", __File__, __Line__);
        }

        return false;

    }

    /**
     * @return array
     * @throws ModelsFolderNotFound
     * this method will provide the list of user/developer defined models.
     */
    public function all(){

        $return = [];

        if(Resource::checkFile(DirConfiguration::$dir["models"])){

            if(is_dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["models"])){

                $list = dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["models"]);

                while(($file = $list->read()) != false) {

                    if ($file == "." || $file == "..") {

                        continue;

                    } else {

                        if(strpos($file, "Model.php") > 0){

                            $return[] = substr($file, 0, strpos($file, ".php"));

                        }

                    }

                }

            }
            else{

                throw new ModelsFolderNotFound(DirConfiguration::$dir["models"], __FILE__, __LINE__);

            }

        }
        else{

            throw new ModelsFolderNotFound(DirConfiguration::$dir["models"], __FILE__, __LINE__);

        }

        return $return;

    }

    public function update(){

    }

    public function generateCRUDOperations(){

    }

}
