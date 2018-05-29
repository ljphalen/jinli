<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Api_BaseController extends Common_BaseController {

	private $sTime = 0;

	public function init() {
		$this->sTime = microtime(true);
		Yaf_Dispatcher::getInstance()->disableView();
		Common::getCache()->increment('fanfan_pv');
	}

	public function output($code, $msg = '', $data = array()) {
		$callback = $this->getInput('callback');
		$out      = array(
			'success' => $code == 0 ? true : false,
			'msg'     => $msg,
			'data'    => $data
		);
		if ($callback) {
			header("Content-type:text/javascript");
			exit($callback . '(' . json_encode($out) . ')');
		} else {
			header("Content-type:text/json");
			exit(json_encode($out));
		}
	}

	public function __destruct() {
		$date       = date('Ymd');
		$eTime      = microtime(true);
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();
		$name       = sprintf("%s_%s_%s", $module, $controller, $action);
		$t          = sprintf("%.2f", $eTime - $this->sTime);
		//echo $name . '_' . $t;
		Common::getCache()->hIncrBy('MON_KEY_NUM:' . $date, $name);
		Common::getCache()->hIncrBy('MON_KEY_TIME:' . $date . ':' . $name, $t);
	}
}
