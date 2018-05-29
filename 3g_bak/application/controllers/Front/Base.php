<?php
if (!defined('BASE_PATH')) exit ('Access Denied!');

/**
 * Front_BaseController
 *
 * @author rainkid
 */
class Front_BaseController extends Common_BaseController {
	public  $actions = array();
	private $sTime   = 0;

	public function init() {
		$this->sTime = microtime(true);
		parent::init();
		$this->checkToken();
		$webroot    = Common::getCurHost();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;

		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();

		$this->assign("webroot", $webroot);
		$this->assign("staticPath", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/3g');
		$this->defaultAction(true);

		$t_bi = $this->getSource();

		$modelName = Util_Http::ua('model');
		$this->assign('t_bi', $t_bi);
		$cn = sprintf('%s_%s_%s', $module, $controller, $action);
		$this->assign('cn', $cn);
		$this->assign('model_name', $modelName);

		$caches     = Common::getConfig('cacheConfig');
		$cache_keys = array_keys($caches);
		if (in_array($cn, $cache_keys)) {
			$this->assign('cache', Gionee_Service_Config::getValue('gionee_cache'));
		}

		Gionee_Service_Log::pvLog('3g_all');
		Gionee_Service_Log::uvLog('3g_all', $t_bi);

		$this->setSource();
	}

	/**
	 * 检查token
	 */
	protected function checkToken() {
		if (!$this->getRequest()->isPost()) {
			return true;
		}
		$post   = $this->getRequest()->getPost();
		$result = Common::checkToken($post ['token']);
		if (Common::isError($result)) {
			$this->output(-1, $result ['msg']);
		}
		return true;
	}

	/**
	 *
	 * @param int    $code
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
			exit ($callback . '(' . Common::jsonEncode($out) . ')');
		} else {
			header("Content-type:text/json;charset=utf-8");
			exit (Common::jsonEncode($out));
		}
	}

	public function getSource() {
		return Util_Cookie::get('3G-SOURCE', true);
	}

	public function setSource() {
		$source = $this->getInput('m');
		$sid    = $this->getSource();
		if ($sid) {
			$sid_arr    = explode('_', $sid);
			$sid_arr[0] = $source;
			$string     = implode('_', $sid_arr);
		} else {
			$uaArr  = Util_Http::ua();
			$uid    = !empty($uaArr['uuid']) ? $uaArr['uuid'] : uniqid();
			$string = sprintf("%s_%s", $source, crc32($uid));
		}
		return Util_Cookie::set('3G-SOURCE', $string, true, strtotime("+360 day"), '/', $this->getDomain());
	}

	/**
	 *
	 * @return string
	 */
	public function getDomain() {
		$domain = str_replace('http://', '', Common::getCurHost());
		if ($number = strrpos($domain, ':')) {
			$domain = Util_String::substr($domain, 0, $number);
		}
		return $domain;
	}

	/**
	 * 是否跳转
	 */
	public function isRedirect($f) {
		if ($f == 'pc') {
			return false;
		}

		$model = array(
			"GN700W",
			"GN800",
			"GN810 ",
			"GN181",
			"GN360",
			"GNc610",
			"GNc700",
			"GN858",
			"GN868H",
			"GN135",
			"GN700T",
			"GN878",
			"GN787",
			"E5",
			"C620",
			"E3",
			"E6",
			"E6mini",
			"GN810",
			"E6T",
			"GN708W",
			"GN206T",
			"X805",
			"GN139",
			"E7",
			"GN208",
			"GN709",
			"GN808",
			"GN705W",
			"GN136",
			"GN180",
			"GN206",
			"GN305"
		);

		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		// ua中有机型
		foreach ($model as $key => $value) {
			if (strpos($ua, $value) !== false) {
				return 2;
			}
		}
		// ua中没有GiONEE标识
		if ((strpos($ua, 'GiONEE') === false) && (strpos($ua, 'GIONEE') === false)) {
			return 1;
		}

		return false;
	}

	public function __destruct() {
		$date       = date('Ymd');
		$eTime      = microtime(true);
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();
		$name       = sprintf("%s_%s_%s", $module, $controller, $action);
		$name       = strtolower($name);
		$t          = sprintf("%.2f", $eTime - $this->sTime);
		Common::getCache()->hIncrBy('MON_KEY_NUM:' . $date, $name);
		Common::getCache()->hIncrBy('MON_KEY_TIME:' . $date . ':' . $name, $t);
	}
}
