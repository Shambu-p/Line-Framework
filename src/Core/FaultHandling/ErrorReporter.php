<?php

use Absoft\Line\Core\FaultHandling\FaultHandler;

ini_set('display_errors', 0);

error_reporting(-1);
//error_reporting( E_ALL) ;
//register_shutdown_function('shutDown');
register_shutdown_function('fatalErrorHandler');
//set_error_handler('ErrorHandler', E_ALL);

function fatalErrorHandler() {

    $error = error_get_last();

    if(!$error) {
        return;
    }

    $type = $error[ "type" ] ;
    $file = $error[ "file" ] ;
    $line = $error[ "line" ] ;
    $message = $error[ "message" ];

    ErrorHandler($type, $message, $file, $line);

}

function ErrorHandler($error_type, $error_message, $error_file, $error_line) {
    FaultHandler::reportError($error_type, $error_message, ($error_file . " on line " . $error_line), "immediate");
}
