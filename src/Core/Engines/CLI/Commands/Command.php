<?php

namespace Absoft\Line\Core\Engines\CLI\Commands;


interface Command
{

    function execute($arguments);

    /**
     * this method will provide the description of the command
     * what it will do and what is expected after execution
     * @return string
     */
    function description();

}
