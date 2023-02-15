<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/27/2021
 * Time: 5:59 PM
 */

namespace Absoft\Line\Core\Engines\HTTP;

use Application\conf\DirConfiguration;
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Errors\LineError;
use Absoft\Line\Core\FaultHandling\FaultHandler;
use Absoft\Line\Core\HTTP\Decode\RequestAnalyzer;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Response;
use Absoft\Line\Core\HTTP\ViewResponse;

class Engine {

    /**
     * @var Request
     * this field will hold the request sent to the server.
     */
    public static $request;
    private $main_folder;

    /**
     * Engine constructor.
     * @param $location string
     */
    function __construct(string $location){

        FaultHandler::fromBrowser();
        $this->main_folder = $location;
        DirConfiguration::$_main_folder = $location;

        $decoder = new RequestAnalyzer();
        self::$request = $decoder->getRequest();

        strtolower(self::$request->case) == "api" ? FaultHandler::API() : FaultHandler::fromBrowser();

    }

    /**
     * @return void
     */
    function start(){

        if(self::$request->isView()){
            self::startView();
        }
        else if(self::$request->isControl()){
            self::startControl();
        }

    }

    /**
     * @throws FileNotFound
     */
    function startView(){

        $address = $this->main_folder."/apps/Templates/".self::$request->template.".php";

        if(!file_exists($address)){
            throw new FileNotFound("View ".$address." not found!", __FILE__, __LINE__);
        }

        $response = new ViewResponse();
        $response->prepare($address);
        $response->respond();

    }

    private static function startControl() {

        $route = self::$request->route;

        $callback = $route["callback"];

        /** @var Response */
        $response = call_user_func($callback, (array) self::$request->request);

        $response->respond();

    }

}
