<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ErrorController extends Yaf_Controller_Abstract {

	public function init() {
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function errorAction($exception) {
		/* error occurs */
		$code = $exception->getCode();
		$msg  = $exception->getMessage();
		$module = $this->getRequest()->getModuleName();
		$name   = $module . '-' . $_SERVER["REQUEST_URI"];
		if (stristr(ENV, 'product')) {
            $path = '/data/3g_log/err/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
			$txt = sprintf('%s %s %s (%s) %s', date('YmdHis'), $name, json_encode(Util_Http::ua()), $code, $msg);
			error_log($txt . "\n", 3, $path . date('Ymd'));
			exit('Access Denied!');
		} else {
			echo 404, ':', $msg;
		}
		exit;
	}
}
