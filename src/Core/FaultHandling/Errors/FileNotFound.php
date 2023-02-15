<?php


namespace Absoft\Line\Core\FaultHandling\Errors;


use Absoft\Line\Core\FaultHandling\Errors\LineError;

class FileNotFound extends LineError {

    public string $title = "FileNotFound Exception";

    function __construct($file_address, $file, $line, $code = 0, \Throwable $previous = null){

        $this->description = "
        The file path ' $file_address '. does not exist";

        $this->file = $file." on line ".$line;

        parent::__construct($this->description, $code, $previous);

    }

}
