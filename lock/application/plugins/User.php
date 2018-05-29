<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class UserPlugin extends Yaf_Plugin_Abstract {
	
	public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		if ($request->module == 'Front') {
			$this->_updateCacheFile($request, $response);
		}
	}
	
	private function _updateCacheFile($request, $response) {
		$key = sprintf("%s_%s_%s", $request->module, $request->controller, $request->action);
		if (in_array($key, array_keys(Common::getConfig('cacheConfig')))) {
	
			$body = $response->getBody();
	
			$pattern = "/<img(.[^<]*)data-src=\"?(.[^<\"]*)\"?(.[^<]*)\/?>/is";
			if(preg_match_all($pattern, $body, $p)){
				foreach($p[2] as $path){
					if (strpos($path, 'http://') !== false) $imgs[] = $path;
				}
			}
			$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $key);
			Util_File::savePhpData($file, $imgs);
		}
	}

}
