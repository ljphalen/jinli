<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 首页推荐列表插入广告
 * @author lichanghua
 *
 */
class  Client_Service_InsertAdData extends Common_Service_Base{
	const PER_PAGE = 10;
	
	/**
	 * 获取所有的插图广告数据
	 */
	public function getInsertPicAd(){
		$ckey = '_index_InsertPicAd';
		$recAds= self::_getCache()->get($ckey);
		if($recAds) return  $recAds;
		$params  = array(
				'status' =>Client_Service_Ad::AD_STATUS_OPEN,
				'start_time'=>array('<=',time()),
				'end_time'=>array('>=',time()),
				'ad_type' =>Client_Service_Ad::AD_TYPE_RECPIC,//推荐图片类型
		);
		
		$recAds = Client_Service_Ad::getsBy($params, array('sort'=>'DESC', 'id'=>'DESC'));
		
		foreach($recAds as $k=>$v){
			$recAds[$k][Util_JsonKey::ITEM_TYPE]= 'SimpleBanner';
			$recAds[$k][Util_JsonKey::BANNER_IMG]  = Common::getAttachPath().$v['img'];
			$localData = Local_Service_IndexAd::cookClientAd($v, '');
			$recAds[$k][Util_JsonKey::VIEW_TYPE]	=	$localData['viewType'];
			$recAds[$k][Util_JsonKey::TITLE]	=	html_entity_decode($localData['title']);
			$recAds[$k][Util_JsonKey::CONTENT]=html_entity_decode($localData['title']);
			$recAds[$k]['ad_id']  = $localData['ad_id']?$localData['ad_id']:'0';
			$recAds[$k][Util_JsonKey::PARAM]= $localData['param'];
			if(in_array($v['ad_ptype'], array('1','5'))){
				if($v['ad_ptype'] == '1'){
					$gameId = $v['link'];
				}else{
					$gameId = $localData['param']['gameId'];
				}
				$game = Resource_Service_GameData::getGameAllInfo($gameId);
				$recAds[$k][Util_JsonKey::GAME_ID] = $gameId;
				$recAds[$k][Util_JsonKey::LINK] = $game['link'];
				$recAds[$k][Util_JsonKey::PACKAGE] = $game['package'];
				$recAds[$k][Util_JsonKey::SIZE] = $game['size']."M";
				$recAds[$k][Util_JsonKey::IMAGE] = $game['img'];
				$recAds[$k][Util_JsonKey::RESUME]=html_entity_decode($game ['resume']);
				$recAds[$k][Util_JsonKey::NAME] = $game['name'];
			}
		}
			self::_getCache()->set($ckey,$recAds, 60);
		return $recAds;
	}
	
	
	
	/**
	 *
	 * 计算每页要插入广告图片的位置
	 * @param int $page 当前页码
	 * @param int $interval
	 * @return array 本页之前已用广告数和上一页最后一个广告之后的游戏数
	 */
	public function getAdPos($page, $interval){
		$adUsed = 0;
		$gameCount = self::PER_PAGE;
		$previousPageGamesAfterLastAd = 0;
		for($i = 1; $i < $page; $i++){
			$adUsed += floor($gameCount / $interval);
			$previousPageGamesAfterLastAd =  (self::PER_PAGE + $previousPageGamesAfterLastAd) % $interval;
			$offset =  ($gameCount) % $interval;
			if ($offset != 0) {
				$gameCount = self::PER_PAGE + $offset;
			} else {
				$gameCount = self::PER_PAGE;
			}
		}
		return array($adUsed, $previousPageGamesAfterLastAd);
	}

	/**
	 * 根据位置计算当前页面需要的广告
	 * @param array $currPageAdPosArr
	 * @param array $adArr
	 * @param int $interval
	 * @return array 之前分页没有使用过的广告
	 */
	public function getCurrpageAd($currPageAdPos, $adArr, $interval){
		$adUnused = array();
		$length = count($adArr);
		for($i = $currPageAdPos; $i < $length; $i++) {
			$adUnused[] = $adArr[$i];
		}
		return $adUnused;
	}

	/**
	 * 插入广告到游戏列表
	 * @param array $gameArr
	 * @param array $adArr
	 * @param int $interval
	 */
	public function insertAdData($currPage, $insertData){
		$data = array();
		$length = count($insertData['subjectGames']);
		$insertPos = $insertData['interval'] - $insertData['previousPageGamesAfterLastAd'];
		$adIndex = 0;
		for($i = 0; $i < $length; $i++) {
			if ($insertData['adUnusedArr'][$adIndex] && $i == $insertPos) {
				$data[] = $insertData['adUnusedArr'][$adIndex];
				$adIndex++;
				$data[] = $insertData['subjectGames'][$i];
				$insertPos += $insertData['interval'];
			} else {
				$data[] = $insertData['subjectGames'][$i];
			}
		}
		if ((($length*$currPage) % $insertData['interval']) == 0 && $adIndex < count($insertData['adUnusedArr'])) {
			$data[] = $insertData['adUnusedArr'][$adIndex];
		}
		return $data;
	}
	
	/**
	 * 获取cache实例
	 */
	private static function _getCache() {
		return Common::getCache();
	}
}
