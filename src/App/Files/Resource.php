<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 10/31/2020
 * Time: 1:12 PM
 */

namespace Absoft\Line\App\Files;

use Absoft\Line\Core\FaultHandling\Errors\BuildersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\ControllersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Exceptions\OperationFailed;
use Application\conf\DirConfiguration;

class Resource {

    /**
     * @param $image_name
     * @return string
     * @throws FileNotFound
     */
    public static function imageAddress($image_name){

        $type = pathinfo($image_name)["extension"];
        if(self::checkFile(DirConfiguration::$dir["resources"]."/images/$image_name")){

            $content = file_get_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/images/$image_name");
            $content = base64_encode($content);

            return "data:image/$type;base64,".''.$content.'';

        }else{
            throw new FileNotFound(DirConfiguration::$dir["resources"]."/images/$image_name", __FILE__, __LINE__);
        }

    }

    /**
     * @param $address
     * @return string
     * @throws FileNotFound
     */
    public static function loadAudio($address){

        $type = pathinfo($address)["extension"];
        if(self::checkFile(DirConfiguration::$dir["resources"]."/audio/$address")){

            $content = file_get_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/audio/$address");
            $content = base64_encode($content);

            return "data:audio/$type;base64,".''.$content.'';

        }else{
            throw new FileNotFound(DirConfiguration::$dir["resources"]."/audio/$address", __FILE__, __LINE__);
        }

    }

    /**
     * @param $address
     * @return string
     * @throws FileNotFound
     */
    public static function loadVideo($address){

        $type = pathinfo($address)["extension"];
        if(self::checkFile(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/videos/$address")){

            $content = file_get_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/videos/$address");
            $content = base64_encode($content);

            return "data:video/$type;base64,".''.$content.'';

        }else{
            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/videos/$address", __FILE__, __LINE__);
        }

    }

    /**
     * @param $address
     * @return string
     * @throws FileNotFound
     */
    public static function loadDocuments($address){

        $type = pathinfo($address)["extension"];
        if(self::checkFile(DirConfiguration::$dir["resources"]."/documents/$address")){

            $content = file_get_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/documents/$address");
            $content = base64_encode($content);
            return "data:application/$type;base64,".''.$content.'';

        }else{
            throw new FileNotFound(DirConfiguration::$dir["resources"]."/documents/$address", __FILE__, __LINE__);
        }

    }

    /**
     * @param $address
     * @return string
     * @throws FileNotFound
     */
    public static function loadResource($address){

        if(self::checkFile(DirConfiguration::$dir["resources"]."/$address")){
            $type = pathinfo($address)["extension"];
            $content = file_get_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["resources"]."/$address");
            $content = base64_encode($content);
            return "data:application/$type;base64,".''.$content.'';
        }else{
            throw new FileNotFound(DirConfiguration::$dir["resources"]."/$address", __FILE__, __LINE__);
        }

    }

    public static function checkFile($address){
        return file_exists(DirConfiguration::$_main_folder.$address);
    }

    /**
     * get all the files defined by the file type provided in single folder
     * @param $address
     *        address of the folder to be searched in
     * @param $file_type
     *        the type of files being searched builders, models, controllers, initializers or view templates
     * @return array
     */
    private static function getDirFiles($address, $file_type){

        switch(strtolower($file_type)){
            case "controllers":
                $suffix = "Controller.php";
                break;
            case "models":
                $suffix = "Model.php";
                break;
            case "initializers":
                $suffix = "Initializer.php";
                break;
            default:
                $suffix = ".php";
                break;
        }

        return self::searchFileInFolder($address, $suffix, 'sub_string');

    }

    /**
     * search for files based on parameters passed to it and
     * returns all the files found in array
     * @param string $address
     *          folder address
     * @param string $needle
     *          any string in the file name wanted to be found.
     *          this parameter should not be empty string
     * @param string $search_type (sub_string | file_name)
     *          set to 'sub_string' to search for number of character in the file name
     *          set to 'file_name' to search by file name with extension
     * @return array
     */
    static function searchFileInFolder(string $address, string $needle, string $search_type){

        $address_list = [];
        $list = dir($address);

        if(empty($needle)){
            return $address_list;
        }

        while(($file = $list->read()) != false) {

            if($file == "." || $file == "..") {
                continue;
            }

            if($search_type == "sub_string" && strpos($file, $needle) > 0){
                $address_list[] = $address."/".$file;
            }

            if($search_type == "file_name" && $file == $needle){
                $address_list[] = $address."/".$file;
            }

        }

        return $address_list;

    }

    /**
     * checks if the file path provided is folder or file
     * if it is folder then it will return list of files based on the file type
     * if it is file it will return single file in array
     * nether of the two then empty array will be returned
     * @param $address
     *        file path
     * @param $file_type
     *        file type to be searched
     * @return array
     */
    private static function findFiles($address, $file_type) {

        if(file_exists($address)) {
            if(is_dir($address)){
                return self::getDirFiles($address, $file_type);
            }else if(is_file($address)) {
                return [$address];
            }
        }

        return [];

    }

    /**
     * @param $file_type
     * @return array
     * @throws ControllersFolderNotFound
     */
    public static function getFiles($file_type) {

        $address_list = [];

        if(isset(DirConfiguration::$dir[$file_type]) && !empty(DirConfiguration::$dir[$file_type])) {

            if(is_array(DirConfiguration::$dir[$file_type])) {

                foreach(DirConfiguration::$dir[$file_type] as $directory){
                    $address_list = array_merge($address_list, self::findFiles(DirConfiguration::$_main_folder.DirConfiguration::$dir[$file_type].$directory, $file_type));
                }

            }else if(is_string(DirConfiguration::$dir[$file_type])){
                $address_list = array_merge($address_list, self::findFiles(DirConfiguration::$_main_folder.DirConfiguration::$dir[$file_type], $file_type));
            }
        }
        else{
            new BuildersFolderNotFound("cannot find any builders folder specification", __FILE__, __LINE__);
            throw new ControllersFolderNotFound("cannot find any controller folder specification.", __FILE__, __LINE__);
        }

        return $address_list;

    }

    /**
     * @param $file_name
     *        file base name including the extension
     * @param $content
     *        file content to be saved
     * @param $file_type
     *        builders, controllers, models, initializers or templates
     * @return false|int
     * @throws OperationFailed
     */
    public static function saveFiles($file_name, $content, $file_type){
        if(isset(DirConfiguration::$dir[$file_type])) {

            if(is_array(DirConfiguration::$dir[$file_type])){

                foreach(DirConfiguration::$dir[$file_type] as $directory){
                    if(file_exists((DirConfiguration::$dir[$file_type].$directory)) && is_dir(DirConfiguration::$dir[$file_type].$directory)){
                        return file_put_contents(DirConfiguration::$dir[$file_type].$directory."/$file_name", $content);
                    }
                }

            } else if(is_string(DirConfiguration::$dir[$file_type])){
                if(is_dir(DirConfiguration::$dir[$file_type])){
                    return file_put_contents(DirConfiguration::$dir[$file_type].$file_name, $content);
                }else {
                    return self::forceSaving($file_name, $content, $file_type);
                }
            }
        }

        return self::forceSaving($file_name, $content, $file_type);

    }

    /**
     * @param $file_name
     * @param $content
     * @param $file_type
     * @return false|int
     * @throws OperationFailed
     */
    private static function forceSaving($file_name, $content, $file_type){

        switch(strtolower($file_type)){
            case "controllers":
                $folder_name = "Controllers";
                break;
            case "models":
                $folder_name = "Models";
                break;
            case "builders":
                $folder_name = "Builders";
                break;
            case "initializers":
                $folder_name = "Initializers";
                break;
            case "templates":
                $folder_name = "Templates";
                break;
            default:
                $folder_name = "";
                break;
        }

        $folder_path = DirConfiguration::$_main_folder . "/apps" . (!empty($folder_name) ? "/$folder_name" : "");
        if(mkdir($folder_path)) {
            return file_put_contents($folder_path."/$file_name", $content);
        } else {
            throw new OperationFailed("cannot create directory on '$folder_path'");
        }

    }

    static function getBuilder(){

    }

    /**
     * @param $file_name
     * @param $object_type
     * @return string|string[]
     * @throws ControllersFolderNotFound
     */
    static function searchObject($file_name, $object_type){

        $files_list = self::getFiles($object_type);

        foreach ($files_list as $file_address){
            if(basename($file_address) == $file_name){
                return self::getObjectName($file_address);
            }
        }

        return "";

    }

    /**
     * @param $file_name
     * @param $file_type
     * @return bool
     * @throws ControllersFolderNotFound
     * @throws FileNotFound
     */
    static function deleteFile($file_name, $file_type){
        $files_list = self::getFiles($file_type);

        foreach ($files_list as $file_address){
            if(basename($file_address) == $file_name){
                return unlink($file_address);
            }
        }

        throw new FileNotFound($file_name, __File__, __Line__);

    }

    static function getObjectName($file_address) {

        if(strpos($file_address, DirConfiguration::$_main_folder."/apps") == 0){
            $name = "Application".substr( str_replace(DirConfiguration::$_main_folder."/apps", "", $file_address), 0, -4);
            return str_replace("/", "\\", $name);
        }

        return "";

    }
}
