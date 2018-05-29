<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class UserPlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		$this->_cookBiParams($request, $response);
// 		$this->_updateCacheFile($request, $response);
	}
	
	public function preResponse(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}
	
	private function _cookBiParams($request, $response) {
		$body = $response->getBody();
		$webroot = Common::getWebRoot();
		$pattern = '/<a(.[^<]*)href=\"?(.[^<\"]*)\"?(.[^<]*)\/?>/is';
		$source = Util_Cookie::get('GOU-SOURCE', true);
		
		if(preg_match_all($pattern, $body, $p)){
			foreach($p[2] as $path){
				if (strpos(html_entity_decode($path), $webroot) !== false) {
					if (strpos(html_entity_decode($path), "?") === false && strpos(html_entity_decode($path), "t_bi") === false) {
						$newPath =$path.'?t_bi='.$source;
					} else {
						$newPath =$path.'&t_bi='.$source;
					}
					$path = json_encode($path);
					$path = str_replace(array('"', '?'), array('\"', '\?'), $path);
					$partten = '/<a(.*)href='.$path.'(.*)>/i';
// 					echo $path,$newPath."\n";
					$body = preg_replace($partten, '<a\\1href="'.$newPath.'"\\2>', $body);
						
				}
			}
			$response->setBody($body);
		}
	}
	
	private function _updateCacheFile($request, $response) {
		if ($request->module == 'Front') {
			$body = $response->getBody();
				
			$pattern = "/<img(.[^<]*)src=\"?(.[^<\"]*)\"?(.[^<]*)\/?>/is";
			if(preg_match_all($pattern, $body, $p)){
				foreach($p[2] as $path){
					if (strpos($path, 'channel') !== false) {
						$imgs[] = $path;
					}
				}
			}
			$key = sprintf("%s_%s_%s", $request->module, $request->controller, $request->action);
			if (in_array($key, array('Front_Index_index'))) {
				$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $key);
				Util_File::savePhpData($file, $imgs);
			}
		}
	}

}
