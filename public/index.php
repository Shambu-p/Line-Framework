<?php

session_start();

use Absoft\Line\Core\Engines\HTTP\Engine;

$_main_location = str_replace("\\", "/", dirname(__DIR__));
//date_default_timezone_set("Africa/Asmara");

include_once $_main_location."/vendor/autoload.php";
include_once $_main_location."/src/Core/FaultHandling/ErrorReporter.php";
include_once $_main_location."/apps/conf/route.php";
//require_once $_main_location."/ExcelPackage/PHPExcel.php";

$start = new Engine($_main_location);
$start->start();

?>
