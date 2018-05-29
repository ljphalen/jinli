<?php
ignore_user_abort(true);
set_time_limit(0);
error_reporting(E_ALL && ~E_NOTICE);
define("BASE_PATH", dirname(__FILE__) . "/../../");
define ("APP_PATH", BASE_PATH . "application/");
define ('ENV', get_cfg_var('GIONEE_ENV'));
define("CRON_SUCCESS", date('Y-m-d H:i:s') . ' __CRON_SUCCESS__');
$app = new Yaf_Application(BASE_PATH. "configs/application.ini", ENV);
$response = $app->bootstrap();