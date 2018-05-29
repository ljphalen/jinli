<?php
error_reporting(E_ALL && ~E_NOTICE);
define("BASE_PATH", dirname(__FILE__) . "/../../");
define ("APP_PATH", BASE_PATH . "application/");
define("ENV", 'develop');
define("DEFAULT_MODULE", 'Front');
$app = new Yaf_Application(BASE_PATH. "configs/application.ini", ENV);
$response = $app->bootstrap()->run();
?>
