<?php
error_reporting(E_ALL && E_NOTICE );
ini_set('display_errors', '1');
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../') . '/');
$prev = '';
if (stristr(BASE_PATH, 'overseas3g')) {
	$prev = 'overseas_';
} else if (stristr(BASE_PATH, 'sige3g')) {
	$prev = 'sige_';
}
define('ENV', $prev . get_cfg_var('GIONEE_ENV'));
define('APP_PATH', BASE_PATH . "application/");
define('DEFAULT_MODULE', 'Admin');
$app      = new Yaf_Application(BASE_PATH . "configs/application.ini", ENV);
$response = $app->bootstrap()->run();

?>