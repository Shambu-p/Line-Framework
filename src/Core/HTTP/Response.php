<?php


namespace Absoft\Line\Core\HTTP;


abstract class Response{

    /**
     * @var string
     * this will show what type of response it is
     */
    public $type;

    function __construct($response_type){
        $this->type = $response_type;
    }

    abstract function respond();

    /**
     * @return $this
     * this method will return constructed response object
     */
    function getResponse(){
        return $this;
    }

}