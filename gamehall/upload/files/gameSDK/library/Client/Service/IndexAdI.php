<?php
if (! defined ( 'BASE_PATH' ))
	exit ( 'Access Denied!' );
/**
 *
 * 游戏大厅 1.4.8 之后使用
 * Enter description here ...
 * 
 * @author lichanghua
 *        
 */
class Client_Service_IndexAdI {
	/**
	 *
	 * @param unknown_type $adid
	 * @param unknown_type $adtj
	 * @param unknown_type $pos
	 */
	public function cookAd($ad, $adtype, $pos, $withCache = true) {
		// 1.内容，2.分类，3.专题，4.外链, 5.活动
		$webroot = Common::getWebRoot ();
		//判断是否为外发包
		$model = "client";
		if(stristr($webroot,'ala'))  $model = "channel";
		if(stristr($webroot,'kingstone'))  $model = "kingstone";

		switch ($ad ['ad_ptype']) {
			case 1 :
				$url = $webroot . '/'.$model.'/index/detail/?id=' . $ad ['link'] . '&pc=1&intersrc=' . $adtype . $pos . '_GID' . $ad ['link'] . '&t_bi=' . self::getSource ();
				//加入评测链接
				$evaluationId = self::getEvaluationByGame($ad['link']);
				$evaluationUrl = '';
				if ($evaluationId) {
					$evaluationUrl = '评测,' . $webroot . '/'.$model.'/evaluation/detail/?id=' . $evaluationId.'&pc=3&intersrc=' . $adtype . $pos . '_infor' . $evaluationId . '&t_bi=' . self::getSource();
				}
				//附加属性处理
				$attach = array();
				if ($evaluationId)	array_push($attach, '评');
				if (self::haveGiftByGame($ad['link'])) array_push($attach, '礼');

				$content = self::_cookAdContent ( $ad, $url, $evaluationUrl, $withCache);
				$content['attach'] = implode(',', $attach);
				$content['data-type'] = 1;
				break;
			case 2 :
				$url = $webroot . "/".$model."/category/detail/?id=" . $ad ['link'] . '&intersrc=' . $adtype . $pos . '_CID' . $ad ['link'] . '&t_bi=' . self::getSource ();
				$content = self::_cookCategory ( $ad, $url );
				$content['data-type'] = 0;
				break;
			case 3 :
				$url = $webroot . "/".$model."/subject/detail/?id=" . $ad ['link'] . '&intersrc=' . $adtype . $pos . '_SID' . $ad ['link'] . '&t_bi=' . self::getSource ();
				$content = self::_cookSubject ( $ad, $url );
				$content['data-type'] = 0;
				break;
			case 4 :
				$content = self::_cookLink ( $ad, $adtype, $pos );
				break;
			case 5 :
				$url = $webroot . "/".$model."/activity/detail/?id=" . $ad ['link'] . '&intersrc=' . $adtype . $pos . $ad ['link'] . '&t_bi=' . self::getSource ();
				$content = self::_cookActivity ( $ad, $adtype, $url );
				break;
		}
		return $content;
	}

	private static function _cookAdContent($ad, $url, $evaluationUrl, $withCache = true) {
		$gameid = $ad ['link'];
		$cacheKey = "index-ad-v2-" . $ad ['link'];

		$cache = Cache_Factory::getCache ();
		if ($withCache && $data = $cache->get($cacheKey )) {
			$data ['data-Info'] [1] = $url;
			//处理评测地址
			if(!empty($evaluationUrl)) { 
				$data['data-Info'][3] = $evaluationUrl;
			} else {
				unset($data['data-Info'][3]);
			}
			$data ['data-Info'] = implode ( ",", $data ['data-Info'] );
			return $data;
		}
		$game = Resource_Service_Games::getGameSimpleInfo(array("id"=>$gameid));
		$tmp = array ();
		$tmp ['data-Info'] = array (
				//$game ['name'],
				'游戏详情',
				$url,
				$gameid,
				$evaluationUrl
		);
		$tmp ['resume'] = $game ['resume'];
		$tmp ['name'] = $game ['name'];
		$tmp ['img'] = $game ['img'];
		$tmp ['size'] = $game ['size'];
		$tmp ['category'] = $game ['category_title'];
		$tmp ['hot'] = $game ['hot_title'];
		$tmp ['device'] = $game ['device'];

		$cache->set ( $cacheKey, $tmp, 3600 );
		//处理评测地址
		if (empty($evaluationUrl)) unset($tmp['data-Info'][3]);		
		$tmp ['data-Info'] = implode ( ",", $tmp['data-Info'] );

		return $tmp;
	}
	private static function _cookCategory($ad, $url) {
		// 分类
		$category = Resource_Service_Attribute::getBy(array('id'=>$ad['link']));
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
			$tmp ['data-type'] = 0;
		} else if (stristr($ad ['link'], 'rank')) {
			$tj = '_newon';
			$tmp ['data-type'] = 0;
		} else if (stristr($ad ['link'], 'single')) {
			$tj = '_pcg';
			$tmp ['data-type'] = 0;
		} else if (stristr($ad ['link'], 'web')) {
			//网游频道标识
			$ckey = 'ONLINE_GAME';
			$columns = Client_Service_ChannelColumn::getCanUseColumn($ckey);
			$data = array();
			if((!empty($columns))){
				//单条热门栏目url过滤
				if ((count($columns) == 1)) {
					if (strpos($columns[0]['link'],'web') !== false) $columns = array();
				}

				if (!empty($columns)) {
					//栏目子频道
					foreach ($columns as $value) {
						//kingstone处理
						if($model == "kingstone"){
							$tmp = parse_url(html_entity_decode($value['link']));
							if($tmp['query']){
								$value['link'] = $webroot.ereg_replace("/client/", "/kingstone/", $tmp['path']).'?'.$tmp['query'];
							} else {
							$value['link'] = $webroot.ereg_replace("/client/", "/kingstone/", $tmp['path']);
							}
						}
						//统计参数
						if (stristr($value['link'], 'web')) {
							$tj = '_olg';
						}else if (stristr($value['link'], 'gift')) {
							$tj = '_olg_libao';
						}
						if (strpos($value['link'], "?") ) {
							$anchor = $value['link'] . '&intersrc=' . $adtype . $pos . $tj . '&t_bi=' . self::getSource ();
						} else {
							$anchor = $value['link'] . '?intersrc=' . $adtype . $pos . $tj . '&t_bi=' . self::getSource ();
						}
						
						$data[]= html_entity_decode($value['name']) . ',' . html_entity_decode($anchor);
					}
				}
				
			}
			$tj = '_olg';
			$tmp ['data-type'] = 0;
		} else {
			//不确定内容的外链
			$tmp['data-type'] = 3;
		}

		if (strpos(html_entity_decode($ad ['link']), "?")) {
			$anchor = $ad['link'] . '&intersrc=' . $adtype . $pos . $tj . '&t_bi=' . self::getSource ();
		} else {
			$anchor = $ad['link'] . '?intersrc=' . $adtype . $pos . $tj . '&t_bi=' . self::getSource ();
		}
		$tmp['data-Info'] = empty($data) ? sprintf("%s,%s", $ad["title"], html_entity_decode($anchor)) : sprintf("%s,%s,%s", $ad["title"], html_entity_decode($anchor), implode(',', $data));
		return $tmp;
	}
	
	private static function _cookActivity($ad, $adtype, $url ) {
	    // 活动
		$hd = Client_Service_Hd::getHd($ad['link']);
		$tmp = array ();
		$tmp ['data-type'] = 4;
		$tmp ['data-Info'] = sprintf ( "%s,%s", $ad ["title"], $url );
		return $tmp;
	}


	/**
	 * 通过游戏ID 获取最新一条游戏评测
	 * @param int $game_id
	 * @return boolean
	 */
	public static function getEvaluationByGame($gameId){
		if (!intval($gameId)) return false;

		$params = array(
				'ntype' =>2,
				'status' => 1,
				'game_id' => intval($gameId),
				'create_time'=> array('<=', Common::getTime()),
		);
		list(,$ret) = Client_Service_News::getList(1, 1, $params);
		return (empty($ret)) ? false : $ret[0]['id'];
	}

	/**
	 * 通过游戏ID 判断是否存在可用礼包
	 * @param int $gameId
	 * @return boolean
	 */
	public static function haveGiftByGame($gameId) {
		if (!intval($gameId)) return false;
		$gift = Client_Service_Gift::getGiftNumByGameId($gameId);
		return ($gift) ? true : false ;
	}
	
	
	/**
	 * 通过游戏ID 获取最新一条游戏攻略
	 * @param int $game_id
	 * @return boolean
	 */
	public static function getStrategyByGame($gameId){
		if (!intval($gameId)) return false;
	
		$params = array(
				'ntype' =>4,
				'status' => 1,
				'game_id' => intval($gameId),
				'create_time'=> array('<=', Common::getTime()),
		);
		list(,$ret) = Client_Service_News::getList(1, 1, $params);
		return (empty($ret)) ? false : $ret[0]['id'];
	}
	

	public static function getSource() {
		return Util_Cookie::get('GAME-SOURCE', false);
	}
}
