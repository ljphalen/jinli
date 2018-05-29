<?php
if (! defined ( 'BASE_PATH' ))
	exit ( 'Access Denied!' );
/**
 *
 * 游戏大厅 1.4.8 之前版本使用
 * Enter description here ...
 * 
 * @author lichanghua
 *        
 */
class Client_Service_IndexAd {
	/**
	 *
	 * @param unknown_type $adid        	
	 * @param unknown_type $adtj        	
	 * @param unknown_type $pos        	
	 */
	public function cookAd($ad, $adtype, $pos, $withCache = true) {
		// 1.内容，2.分类，3.专题，4.外链
		$webroot = Common::getWebRoot ();
		//判断是否为外发包
		$model = "client";
		if(stristr($webroot,'ala'))  $model = "channel";
		if(stristr($webroot,'kingstone'))  $model = "kingstone";
		
		switch ($ad ['ad_ptype']) {
			case 1 :
				$url = $webroot . '/'.$model.'/index/detail/?id=' . $ad ['link'] . '&intersrc=' . $adtype . $pos . '_GID' . $ad ['link'] . '&t_bi=' . self::getSource ();
			
				$content = self::_cookAdContent ( $ad, $url, $withCache);
				break;
			case 2 :
				$url = $webroot . "/".$model."/category/detail/?id=" . $ad ['link'] . '&intersrc=' . $adtype . $pos . '_CID' . $ad ['link'] . '&t_bi=' . self::getSource ();
				$content = self::_cookCategory ( $ad, $url );
				break;
			case 3 :
				$url = $webroot . "/".$model."/subject/detail/?id=" . $ad ['link'] . '&intersrc=' . $adtype . $pos . '_SID' . $ad ['link'] . '&t_bi=' . self::getSource ();
				$content = self::_cookSubject ( $ad, $url );
				break;
			case 4 :
				$content = self::_cookLink ( $ad, $adtype, $pos );
				break;
		}
		return $content;
	}
	
	private static function _cookAdContent($ad, $url, $withCache = true) {
		$gameid = $ad ['link'];
		$cacheKey = "index-ad-" . $ad ['link'];
		
		$cache = Common::getCache ();
		if ($withCache && $data = $cache->get($cacheKey )) {
			$data ['data-Info'] [1] = $url;
			$data ['data-Info'] = implode ( ",", $data ['data-Info'] );
			return $data;
		}
		$game = Resource_Service_Games::getGameAllInfo(array("id"=>$gameid));
		$tmp = array ();
		$tmp ['data-Info'] = array (
				$game ['name'],
				$url,
				$gameid,
				$game ['link'],
				$game ['package'],
				$game ['size'],
				'Android' . $game['min_sys_version_title'],
				$game['min_resolution_title'] . "-" . $game['max_resolution_title'] 
		);
		$tmp ['resume'] = $game ['resume'];
		$tmp ['name'] = $game ['name'];
		$tmp ['img'] = $game ['img'];
		$tmp ['size'] = $game ['size'];
		$tmp ['category'] = $game ['category_title'];
		$tmp ['hot'] = $game ['hot_title'];
		
		$cache->set ( $cacheKey, $tmp, 3600 );
		
		$tmp ['data-Info'] [1] = $url;
		$tmp ['data-Info'] = implode ( ",", $tmp ['data-Info'] );
		
		return $tmp;
	}
	private static function _cookCategory($ad, $url) {
		// 分类
		$category = Resource_Service_Attribute::getResourceAttribute ( $ad ['link'] );
		$tmp = array ();
		if ($category ['status']) {
			$tmp ['data-Info'] = sprintf ( "%s,%s", $category ["title"], $url );
		}
		return $tmp;
	}
	private static function _cookSubject($ad, $url) {
		$subject = Client_Service_Subject::getSubject ( $ad ['link'] );
		
		$tmp = array ();
		if ($subject ['status']) {
			$tmp ['data-Info'] = sprintf ( "%s,%s", $subject ["title"], $url );
		}
		return $tmp;
	}
	
	private static function _cookLink($ad, $adtype, $pos) {
		$tmp = array();
		$webroot = Common::getWebRoot();
		//判断是否为外发包
		$model = "client";
		if(stristr($webroot,'ala'))  $model = "channel";
		if(stristr($webroot,'kingstone'))  $model = "kingstone";
		
		//小辣椒处理
		if($model == "channel"){
			$tmp = parse_url(html_entity_decode($ad["link"]));
			if($tmp['query']){
				$ad ['link'] = $webroot.ereg_replace("/client/", "/channel/", $tmp['path']).'?'.$tmp['query'];
			} else {
				$ad ['link'] = $webroot.ereg_replace("/client/", "/channel/", $tmp['path']);
			}
		
		}
		
		//kingstone处理
		if($model == "kingstone"){
			$tmp = parse_url(html_entity_decode($ad["link"]));
			if($tmp['query']){
				$ad ['link'] = $webroot.ereg_replace("/client/", "/kingstone/", $tmp['path']).'?'.$tmp['query'];
			} else {
				$ad ['link'] = $webroot.ereg_replace("/client/", "/kingstone/", $tmp['path']);
			}
		
		}
		
		if (stristr($ad ['link'], 'installe')) {
			$tj = '_ness';
		} else if (stristr($ad ['link'], 'rank')) {
			$tj = '_newon';
		} else if (stristr($ad ['link'], 'single')) {
			$tj = '_pcg';
		} else if (stristr($ad ['link'], 'web')) {
			$tj = '_olg';
		}
		
		if (strpos(html_entity_decode($ad ['link']), "?")) {
			$anchor = $ad ['link'] . '&intersrc=' . $adtype . $pos . $tj . '&t_bi=' . self::getSource ();
		} else {
			$anchor = $ad ['link'] . '?intersrc=' . $adtype . $pos . $tj . '&t_bi=' . self::getSource ();
		}
		$tmp ['data-Info'] = sprintf("%s,%s", $ad["title"], html_entity_decode($anchor));
		return $tmp;
	}

	
	public static function getSource() {
		return Util_Cookie::get('GAME-SOURCE', false);
	}
}