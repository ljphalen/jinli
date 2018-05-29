<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * APPCACHE 接口
 */
class CacheController extends Common_BaseController {

	public function indexAction() {
		$cn = $this->getInput('cn');
		Yaf_Dispatcher::getInstance()->disableView();
		header('Content-Type: text/cache-manifest');
		echo "CACHE MANIFEST\n";
		$v = Gionee_Service_Config::getValue('APPC_' . $cn);
		echo "\n#version:" . $v . "\n\n";
		$caches = Common::getConfig('cacheConfig', $cn);
		foreach ($caches as $key => $value) {
			echo sprintf("\n\n%s:\n", $key);
			if ($key == 'CACHE') {
				$files = Common::getAppc($cn);
				if (is_array($files)) {
					$value = array_merge($files, $value);
				}
			}
			echo implode("\n", $value);
		}
	}
}