<?php


namespace Absoft\Line\Core\HTTP;


class JSONResponse extends Response {

    private $response = [
        "header" => [
            "error" => false,
            "message" => ""
        ],
        "data" => []
    ];

    function __construct(){
        parent::__construct("json");
    }

    /**
     * @param array $data
     */
    function prepareData(array $data){
        http_response_code(200);
        $this->response["data"] = is_array($data) ? $data : [];
    }

    function additionalData($parameter, $data) {

        if($parameter == "data") {
            return;
        }

        $this->response[$parameter] = $data;

    }

    function prepareError($message) {
        http_response_code(201);
        $this->response["header"]["message"] = $message;
        $this->response["header"]["error"] = true;
    }

    function respond(){

        header("Provider: Absoft");
        header("Access-Control-Allow-Origin: *");
        header("Content-type: application/json");

        echo json_encode($this->response);

    }

}