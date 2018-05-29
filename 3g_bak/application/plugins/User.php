<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * UserPlugin
 * @author rainkid
 */
class UserPlugin extends Yaf_Plugin_Abstract {

	public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		if ($request->module == 'Front' && $request->controller != 'Nav') {
// 			$this->_cookBiParams($request, $response);
			$this->_updateCacheFile($request, $response);
		}
	}

	private function _cookBiParams($request, $response) {
		$body    = $response->getBody();
		$webroot = Common::getCurHost();
		$pattern = '/<a(.[^<]*)href=\"?(.[^<\"]*)\"?(.[^<]*)\/?>/is';
		$source  = Util_Cookie::get('3G-SOURCE', true);

		if (preg_match_all($pattern, $body, $p)) {
			foreach ($p[2] as $path) {
				if (strpos(html_entity_decode($path), $webroot) !== false) {
					if (strstr(html_entity_decode($path), "?") === false && strpos(html_entity_decode($path), "t_bi") === false) {
						$newPath = $path . '?t_bi=' . $source;
					} else {
						$newPath = $path . '&t_bi=' . $source;
					}
					$path    = json_encode($path);
					$path    = str_replace(array('"', '?'), array('\"', '\?'), $path);
					$partten = '/<a(.*)href=' . $path . '(.*)>/i';
					$body    = preg_replace($partten, '<a\\1href="' . $newPath . '"\\2>', $body);
				}
			}
			$response->setBody($body);
		}
	}

	/**
	 * 分析页面数据生存appcache缓存文件
	 * @param $request
	 * @param $response
	 */

	private function _updateCacheFile($request, $response) {
		$key = sprintf("%s_%s_%s", $request->module, $request->controller, $request->action);
		if (in_array($key, array_keys(Common::getConfig('cacheConfig')))) {

			$body    = $response->getBody();
			$pattern = "/<img(.[^<]*)src=\"?(.[^<\"]*)\"?(.[^<]*)\/?>/is";
			if (preg_match_all($pattern, $body, $p)) {
				foreach ($p[2] as $path) {
					//只匹配http开头的图片文件
					if (stristr($path, 'http')) {
						if (stristr($path, '.png') || stristr($path, '.jpg') || stristr($path, '.gif')) {
							$imgs[] = $path;
						}
					}
				}
			}

			Common::setAppc($key, $imgs);
		}
	}

}
