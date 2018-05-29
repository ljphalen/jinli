<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ErrorController extends Yaf_Controller_Abstract {

	public function init() {
		Yaf_Dispatcher::getInstance()->disableView();
	}
	
	public function errorAction($exception) {
		/* error occurs */
		if (ENV == 'product') {
			exit('Access Denied!');
		}
		switch ($exception->getCode()) {
		case YAF_ERR_NOTFOUND_MODULE:
		case YAF_ERR_NOTFOUND_CONTROLLER:
		case YAF_ERR_NOTFOUND_ACTION:
		case YAF_ERR_NOTFOUND_VIEW:
		default :
			echo 0,':',$exception->getMessage();
			break;
		}
	}
}
