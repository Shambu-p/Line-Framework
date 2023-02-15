<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 11:45 PM
 */

namespace Absoft\Line\Core\Engines\HTTP;

use Absoft\Line\App\Administration\AdminController;
use Application\conf\DirConfiguration;
use Absoft\Line\App\Files\Resource;
use Absoft\Line\Core\FaultHandling\Errors\ControllerNotFound;
use Absoft\Line\Core\FaultHandling\Errors\LineError;
use Absoft\Line\Core\HTTP\JSONResponse;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Response;
use Absoft\Line\Core\HTTP\Route;
use Absoft\Line\Core\HTTP\ViewResponse;
use Absoft\Line\Core\Modeling\Controller;

class ControlEngine
{

    private $request;
    private $_main_address;

    function __construct(Request $request, $_main_folder){

        $this->request = $request;
        $this->_main_address = $_main_folder;

    }

    /**
     * @return Controller
     * @throws ControllerNotFound
     */
    private function defaultControllerLoader(){

        $full_c_name = 'Application\\Controllers\\'.$this->request->header->controller;

        if(Resource::checkFile(DirConfiguration::$dir["controllers"]."/".$this->request->header->controller.".php")){
            return new $full_c_name($this->request, $this->_main_address);
        }else{
            throw new ControllerNotFound($this->request->header->controller, __FILE__, __LINE__);
        }

    }



    /**
     * @return Controller
     * @throws ControllerNotFound
     */
    private function adminController(){
        //$full_c_name = 'Absoft\\App\\Administration\\AdminController';

        if(Resource::checkFile("/System/Absoft/App/Administration/AdminController.php")){
            return new AdminController($this->request, $this->_main_address);
        }else{
            throw new ControllerNotFound($this->request->header->controller, __FILE__, __LINE__);
        }
    }

    /**
     *
     */
    public function start(){

        $routes = Route::allRoutes();
        $callback = $routes[strtolower($_SERVER["REQUEST_METHOD"])][$this->request->link];

        try {

            /** @var Response */
            $response = call_user_func($callback);
            $response->respond();

        }catch (LineError $exception){

            if($this->request->case == "API"){
                $response = new JSONResponse();
                $response->prepareError($exception);
                $response->respond();
            }else{
                $response = new ViewResponse();
                $response->prepareError($exception);
                $response->respond();
            }

        }

    }

}
