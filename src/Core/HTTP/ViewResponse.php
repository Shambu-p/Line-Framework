<?php


namespace Absoft\Line\Core\HTTP;


use Application\conf\DirConfiguration;
use Absoft\Line\App\Pager\Alert;
use Absoft\Line\Core\Engines\HTTP\Engine;
use Absoft\Line\Core\FaultHandling\Errors\LineError;
use Application\conf\ErrorConfiguration;

class ViewResponse extends Response
{

    /**
     * @var string
     */
    public $type;
    private $content = "";

    public function __construct(){
        parent::__construct("view");
    }

    public function prepare($location, $request_data = null){

        http_response_code(200);

        $loadTemplate = function ($location, $request = null) {
            ViewResponse::addLayout($location, $request);
        };

        $request = $request_data ?? Engine::$request->request;
        $main_path = Request::hostAddress();

        ob_start();
        include_once $location;
        $this->content = ob_get_clean();

    }

    /**
     * @param LineError|\stdClass|null $exception
     */
    public function prepareError($exception = null) {

        $location = ErrorConfiguration::$conf["error_page"] == "" ? dirname(dirname(__DIR__))."/App/Templates/error/index.php" : $_SERVER["DOCUMENT_ROOT"]."/apps/Templates".ErrorConfiguration::$conf["error_page"];

        http_response_code(501);

        $request = $exception ? [
            "title" => $exception->title,
            "description" => $exception->description,
            "file" => $exception->file
        ] : [
            "title" => "Unknown",
            "description" => "Unknown",
            "file" => "Unknown"
        ];

        $main_path = Request::hostAddress();

        ob_start();
        include_once $location;
        $this->content = ob_get_clean();

    }

    static function addLayout($location, $request_data = null){

        $request = $request_data ?? Engine::$request->request;
        $main_path = Request::hostAddress();

        include_once DirConfiguration::$_main_folder."/apps/Templates".$location.".php";
    }

    public function respond(){
        echo $this->content;
    }

    public function show_alert() {
        Alert::displayAlert();
    }

    public function set_alert_classname($success="", $info="", $error="") {

        Alert::setErrorClassName($error);
        Alert::setSuccessClassName($success);
        Alert::setInfoClassName($info);

    }

}