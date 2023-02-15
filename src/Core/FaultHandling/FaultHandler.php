<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 6/19/2020
 * Time: 11:00 PM
 */
namespace Absoft\Line\Core\FaultHandling;

use Application\conf\DirConfiguration;
use Absoft\Line\Core\HTTP\JSONResponse;
use Absoft\Line\Core\HTTP\ViewResponse;

class FaultHandler {

    public static string $request_type = "browser";

    /**
     * @param int|string $title
     * @param string $description
     * @param $error_file
     * @param string $urgency
     */
    public static function reportError($title, string $description, $error_file, string $urgency = "not_immediate"){

        $new_error = new \stdClass();

        $new_error->title = $title;
        $new_error->description = $description;
        $new_error->file = $error_file;
        $new_error->date = strtotime("now");

        self::logError($new_error);

        if(self::$request_type == "api") {
            $response = new JSONResponse();
            $response->prepareError($description);
            $response->respond();
        }else if(self::$request_type == "UI" || self::$request_type == "browser") {
            ob_get_clean();
            $response = new ViewResponse();
            $response->prepareError($new_error);
            $response->respond();
        }else{
            print_r($new_error);
        }

        die();

    }

    static function logError(\stdClass $new_error){

        $log_file = DirConfiguration::$_main_folder."/apps/Runtime/error_log.json";

        $errors = json_decode(file_exists($log_file) ? file_get_contents($log_file) : "{}");

        $log = (array) $errors;
        $log[] = $new_error;
        unset($errors);

        file_put_contents($log_file, json_encode($log));

    }

    public static function fromCLI(){
        self::$request_type = "cli";
    }

    public static function fromBrowser(){
        self::$request_type = "browser";
    }

    public static function API(){
        self::$request_type = "api";
    }

}
