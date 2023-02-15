<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;
use Throwable;

class ErrorUnknown extends LineError {

    public string $title = "FATAL ERROR!!!";

    /**
     * ErrorUnknown constructor.
     * @param $description
     * @param $error_file
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($description, $error_file, $code = 0, Throwable $previous = null){

        $this->file = $error_file;
        $this->description = $description;
        parent::__construct($description, $code, $previous);

    }

}