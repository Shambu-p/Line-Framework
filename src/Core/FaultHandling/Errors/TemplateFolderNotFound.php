<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/26/2021
 * Time: 9:58 AM
 */

namespace Absoft\Line\Core\FaultHandling\Errors;

use Absoft\Line\Core\FaultHandling\Errors\LineError;

class TemplateFolderNotFound extends LineError {

    public string $title = "TemplatesFolderNotFound Exception";
    public string $description;
    private string $urgency = "immediate";


    function __construct($file_address, $file, $line, $code = 0, $previous = null){

        $this->description = "
        There is no directory with address ' $file_address '. does not exist \n it is because the template address is changed in DirConfiguration file.";

        $this->file = $file." on line ".$line;

        parent::__construct($this->description, $code, $previous);

    }

}
