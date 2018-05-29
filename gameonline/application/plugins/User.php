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
		/* if ($request->module == 'Client' && $request->controller != 'Index') {
			$this->_updateCacheFile($request, $response);
		 } */

	}

	private function _updateCacheFile($request, $response) {
		$key = sprintf("%s_%s_%s", $request->module, $request->controller, $request->action);
		if (in_array($key, array_keys(Common::getConfig('cacheConfig')))) {
		
			$body = $response->getBody();
			$img = array();	
			$pattern = "/<img(.[^<]*)src=\"?(.[^<\"]*)\"?(.[^<]*)\/?>/is";
			if(preg_match_all($pattern, $body, $p)){
				foreach($p[2] as $path){
					$imgs[] = $path;
				}
			}
			
			$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $key);
			if (!file_exists($file)) {
				Util_File::savePhpData($file, $imgs);
			}
			$new_version = crc32(json_encode($imgs));
			$files = include $file;
			
			if (crc32(json_encode($files)) !== $new_version) {
				Util_File::savePhpData($file, $imgs);
			}
		}
	}

}
