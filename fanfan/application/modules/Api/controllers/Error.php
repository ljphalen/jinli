<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ErrorController extends Yaf_Controller_Abstract {
	public function init() {
		Yaf_Dispatcher::getInstance()->disableView();

		// 3g.gionee.com/aorate/soft 特殊跳转
		$server = Util_Http::getServer();
		$url    = 'http://' . $server ['SERVER_NAME'] . $server ['REQUEST_URI'];
		if (strpos($url, 'aorate/soft')) {
			$redirect = Gionee_Service_Redirect::getRedirectByUrl(md5($url));
			if ($redirect) {
				$this->redirect($redirect ['redirect_url']);
			}
		}
	}

	public function errorAction($exception) {
		/* error occurs */
		switch ($exception->getCode()) {
			case YAF_ERR_NOTFOUND_MODULE :
			case YAF_ERR_NOTFOUND_CONTROLLER :
			case YAF_ERR_NOTFOUND_ACTION :
			case YAF_ERR_NOTFOUND_VIEW :
				if (ENV == 'product') {
					exit ('Access Denied!');
				} else {
					echo 404, ':', $exception->getMessage();
				}
				break;
			default :
				if (ENV == 'product') {
					exit ('Access Denied!');
				} else {
					echo 0, ':', $exception->getMessage();
				}
				break;
		}
	}
}