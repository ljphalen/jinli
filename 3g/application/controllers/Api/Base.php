<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Api_BaseController
 * @author rainkid
 */
class Api_BaseController extends Common_BaseController {
	private $sTime = 0;

	public function init() {
		$this->sTime = microtime(true);
		$t_bi        = $this->getSource();
		Gionee_Service_Log::pvLog('3g_all');
		Gionee_Service_Log::uvLog('3g_all', $t_bi);
	}

	/**
	 *
	 * @param int $code
	 * @param string $msg
	 * @param string $data
	 */
	public function output($code, $msg = '', $data = array()) {
		$callback = $this->getInput('callback');
		$out      = array(
			'success' => $code == 0 ? true : false,
			'msg'     => $msg,
			'data'    => $data
		);
		if ($callback) {
			header("Content-type:text/javascript;charset=utf-8");
			exit($callback . '(' . json_encode($out, JSON_UNESCAPED_UNICODE) . ')');
		} else {
			header("Content-type:text/json;charset=utf-8");
			exit(json_encode($out, JSON_UNESCAPED_UNICODE));
		}
	}

	public function getSource() {
		return Util_Cookie::get('3G-SOURCE', true);
	}

	public function __destruct() {
		$date       = date('Ymd');
		$eTime      = microtime(true);
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();
		$name       = sprintf("%s_%s_%s", $module, $controller, $action);
		$t          = sprintf("%.2f", $eTime - $this->sTime);
		$name       = strtolower($name);
		Common::getCache()->hIncrBy('MON_KEY_NUM:' . $date, $name);
		Common::getCache()->hIncrBy('MON_KEY_TIME:' . $date . ':' . $name, $t);
	}
}
