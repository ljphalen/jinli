<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Common_BaseController
 * @author rainkid
 */
abstract class Common_BaseController extends Yaf_Controller_Abstract {
	public $actions = array();
	public $userInfo = array();

	public function init() {

		$webroot    = Common::getCurHost();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;

		$this->assign("webroot", $webroot);
		$this->assign("staticPath", $staticroot . '/apps/');
		$this->assign("attachPath", Common::getImgPath());
		$this->assign('token', Common::getToken());
		//$this->assign('version', Common::getConfig('siteConfig', 'version'));
		$this->assign('version', Gionee_Service_Config::getValue('styles_version'));

		$t_bi = $this->getSource();
		//init actions
		foreach ($this->actions as $key => $value) {
			$this->assign($key, $value . "?t_bi=" . $t_bi);
		}
		
		$ajaxSkip = $this->getInput('_skip');
		if ($this->isAjax() && empty($ajaxSkip)) {
			Yaf_Dispatcher::getInstance()->disableView();
		}
	}

	public function defaultAction($withBi = false) {
		foreach ($this->actions as $key => $value) {
			if ($withBi) $value = $value . "?t_bi=" . $this->getSource();
			$this->assign($key, $value);
		}
	}

	public function getSource() {
		return Util_Cookie::get('3G-SOURCE', true);
	}

	
	public function output($code, $msg = '', $data = array()) {
		$callback = $this->getInput('callback');
		$out      = array(
				'success' => $code == 0 ? true : false,
				'msg'     => $msg,
				'data'    => $data
		);
		if ($callback) {
			header("Content-type:text/javascript;charset=utf-8");
			exit ($callback . '(' . Common::jsonEncode($out) . ')');
		} else {
			header("Content-type:text/json;charset=utf-8");
			exit (Common::jsonEncode($out));
		}
	}
	
	/**
	 *
	 *
	 * @param string $var
	 * @param string $value
	 */
	public function assign($var, $value) {
		$this->getView()->assign($var, $value);
	}

	/**
	 *
	 * 获取post参数
	 * @param string /array $var
	 */
	public function getPost($var) {
		if (is_string($var)) return Util_Filter::post($var);
		$return = array();
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				if (is_array($value)) {
					$return[$value[0]] = Util_Filter::post($value[0], $value[1]);
				} else {
					$return[$value] = Util_Filter::post($value);;
				}
			}
			return $return;
		}
		return null;
	}

	/**
	 *
	 * 获取get参数
	 * @param string $var
	 */
	public function getInput($var) {
		if (is_string($var)) {
			return self::getVal($var);
		}
		if (is_array($var)) {
			$return = array();
			foreach ($var as $key => $value) {
				$return[$value] = self::getVal($value);
			}
			return $return;
		}
		return null;
	}

	/**
	 *
	 * @param string $var
	 * @return void
	 */
	private static function getVal($var) {
		$value = Util_Filter::post($var);
		if (!is_null($value)) {
			return $value;
		}
		$value = Util_Filter::get($var);
		if (!is_null($value)) {
			return $value;
		}
		return null;
	}

	/**
	 *
	 * 请求是否是ajax
	 */
	public function isAjax() {
		return $this->getRequest()->isXmlHttpRequest() || $this->getInput("callback");
	}

	/**
	 * @param $code
	 * @param string $msg
	 * @throws Yaf_Exception
	 */
	public function showMsg($code, $msg = '') {
		throw new Yaf_Exception($msg, $code);
	}
}