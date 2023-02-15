<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 10/26/2019
 * Time: 8:52 AM
 */

namespace Absoft\Line\Core\Modeling;

use Application\conf\DirConfiguration;
use Absoft\Line\Core\Engines\HTTP\Engine;
use Absoft\Line\Core\HTTP\Decode\LineValidator;
use Absoft\Line\Core\HTTP\FileResponse;
use Absoft\Line\Core\HTTP\JSONResponse;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Response;
use Absoft\Line\Core\HTTP\ViewResponse;

abstract class Controller {

    public $_main_address;

    /** @var Request */
    public $request;
    public static $J = 1;
    public static $H = 2;
    public static $validation_message = "";

    public function __construct(){

        $this->request = Engine::$request;
        $this->_main_address = DirConfiguration::$_main_folder;

    }

    /**
     * @param $response
     * @param int $flag
     * @return Response
     */
    public function respond($response, $flag = 2) {

        $resp = new JSONResponse();
        $resp->prepareData($flag == 1 ? $response : (array) json_decode($response));
        return $resp;

    }

    /**
     * @param $file
     * file can be file absolute location of a file to be responded
     * or it can be file array containing file content and other file information
     * organized in the array as follows
     * [
     *      "name" => "file name",
     *      "extension" => "file extension",
     *      "content" => "file content",
     *      "size" => "file size in bites"
     * ]
     * @param bool $download
     * set TRUE to force download
     * @return Response
     */
    public function respondFile($file, $download = false){

        $response = new FileResponse();
        $response->prepare($file, $download);
        return $response;

    }

    public function json(array $array){
        $response = new JSONResponse();
        $response->prepareData($array);
        return $response;
    }

    public function display($location, $request_data = []){
        $response = new ViewResponse();
//        if(!file_exists(DirConfiguration::$_main_folder."/apps/Templates".$location.".php")){
//
//        }
        $response->prepare(DirConfiguration::$_main_folder."/apps/Templates".$location.".php", $request_data);
        return $response;
    }

    /**
     * it validate parameters passed to this specific route
     * @return boolean
     */
    public function validate(){

        $validator = new LineValidator(Engine::$request->route["parameters"], Engine::$request->request);
        $result = $validator->validate();

        if(!$result["status"]){
            self::$validation_message = $result["message"];
        }

        return $result["status"];

    }

    public function validationMessage(){
        return self::$validation_message;
    }

}
