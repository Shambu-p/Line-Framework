<?php


namespace Absoft\Line\Core\Engines\CLI\Commands;


class versionCommand implements Command
{

    private $name = "Line PHP Framework";
    private $cli_version = "2.0";
    private $framework_version = "2.2";
    private $company = "Absoft Private Owned Company";
    private $licence = "All right reserved under Ethiopian law";
    private $description = "this command will show the version and related information about the framework";

    function execute($arguments)
    {

        print "
$this->name version $this->framework_version from $this->company
CLI version $this->cli_version
licence $this->licence";

    }

    /**
     * this method will provide the description of the command
     * what it will do and what is expected after execution
     * @return string
     */
    function description(){
        return $this->description;
    }
}
