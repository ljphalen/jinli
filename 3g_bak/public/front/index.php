<?php
//error_reporting(E_ALL);//测试bug
error_reporting(E_ALL && E_NOTICE);//线上
//date_default_timezone_set('PRC');
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../') . '/');
$ver = '';
if (stristr(BASE_PATH, 'overseas3g')) {
	$ver = 'overseas';
} else if (stristr(BASE_PATH, 'sige3g')) {
	$ver = 'sige';
}
$prev = !empty($ver) ? $ver . '_' : '';
define('APP_VER', $ver);
define('ENV', $prev . get_cfg_var('GIONEE_ENV'));
define('APP_PATH', BASE_PATH . "application/");
define("DEFAULT_MODULE", 'Front');
$app      = new Yaf_Application (BASE_PATH . "configs/application.ini", ENV);
$response = $app->bootstrap()->run();
?>
