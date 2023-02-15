<?php


namespace Absoft\Line\Core\HTTP\Decode;


class LineValidator {

    /**
     * @var array
     *   this field should be array like the following
     *          [
     *              "param1" => ['required', 'email', 'integer' ...]
     *              "param2" => [...]
     *          ]
     */
    private $validation_array;

    /**
     * @var array
     * this field should be associative array of
     * parameter name and their corresponding value
     */
    private $parameter_array;


    /**
     * LineValidator constructor.
     * @param array $validation_array
     *          this field should be array like the following
     *          [
     *              "param1" => ['required', 'email', 'integer' ...]
     *              "param2" => [...]
     *          ]
     * @param array $parameter_array
     *          this field should be associative array of
     *          parameter name and their corresponding value
     */
    function __construct(array $validation_array, array $parameter_array){
        $this->validation_array = $validation_array;
        $this->parameter_array = $parameter_array;
    }

    /**
     * this method validate the parameters passed
     * with validation array specification.
     * @returns array
     *      as result it will return associative array boolean value and string message as follow
     *      [
     *          "status" => false,
     *          "message" => "error message"
     *      ]
     */
    function validate(){

        $response = [
            "status" => true,
            "message" => ""
        ];

        if(empty($this->validation_array)){
            return $response;
        }

        foreach($this->validation_array as $parameter_name => $methods){

            $res = $this->singleValidation($parameter_name);

            if(!$res["status"]){
                $response["status"] = false;
                $response["message"] .= "<br/>" .  $res["message"];
            }

        }

        return $response;

    }

    private function singleValidation(string $parameter_name){

        $response = [
            "status" => true,
            "message" => ""
        ];

        foreach($this->validation_array[$parameter_name] as $name => $method) {

            $res = $this->singleCheck($parameter_name, $method);

            if(!$res["status"]){
                $response["status"] = false;
                $response["message"] .= "<br/>" . $res["message"];
            }

        }

        return $response;

    }

    private function singleCheck(string $parameter_name, string $method){

        $response = [
            "status" => true,
            "message" => ""
        ];

        switch ($method){
            case "required":
                if(!$this->required($parameter_name)){
                    return $this->falseReturn("parameter named '$parameter_name' is required");
                }
                break;
            case "number":
                if(is_numeric($this->parameter_array[$parameter_name])){
                    return $this->falseReturn("parameter name '$parameter_name' should be number");
                }
                break;
            case "integer":
                if(is_integer($this->parameter_array[$parameter_name])){
                    return $this->falseReturn("parameter name '$parameter_name' should be integer");
                }
                break;
            case "float":
                if(is_float($this->parameter_array[$parameter_name])){
                    return $this->falseReturn("parameter name '$parameter_name' should be float number");
                }
                break;
            case "double":
                if(is_double($this->parameter_array[$parameter_name])){
                    return $this->falseReturn("parameter name '$parameter_name' should be number");
                }
                break;
            case "email":
                if($this->validEmail($this->parameter_array[$parameter_name])){
                    return $this->falseReturn("parameter name '$parameter_name' should be valid email address");
                }
                break;
        };

        return $response;

    }

    private function falseReturn($message){
        return [
            "status" => false,
            "message" => $message
        ];
    }

    /**
     * check if the parameter is set.
     * if the parameter is set it will return TRUE else it will return FALSE
     * @param $parameter_name
     * @return bool
     */
    private function required($parameter_name) {
        return isset($this->parameter_array[$parameter_name]) && !empty($this->parameter_array[$parameter_name]);
    }

    /**
     * checks if the parameter is valid email address or not using regular expression
     * @param $parameter_name
     * @return false|int
     */
    private function validEmail($parameter_name){
        $pattern = '([a-z]|[0-9])^3\@[a-z]+\.[a-z]^3';
        return preg_match($pattern, $this->parameter_array[$parameter_name]);
    }



}