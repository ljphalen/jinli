<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_SingleController extends Api_BaseController {
	public $perpage = 10;
	private $cacheExpire = 120;
	
	public function singleInfoAction() {
		$apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
		$cacheKey = Util_CacheKey::SINGLE_LIST . "info" . "_" . $this->perpage;
		$data = $apcu->get($cacheKey);
		if ($data){
			$this->localOutput('','',$data);
		}
		
		//广告位 intersrc
		$ad = Game_Api_SingleRecommendBanner::getOldVersionBannerData();
		$topad = array();
		foreach($ad as $key=>$value){
			$topad = Client_Service_Ad::getDataAd($value['ad_ptype'],$value['title'],$value['link'],$value['img']);
		    $tmp[] = array(
					'viewType'=>$topad['viewType'],
					'title'=>html_entity_decode($topad['title']),
					'imageUrl'=>$topad['imageUrl'],
					'param'=>$topad['param'],
		    		'ad_id' => $value ['id'],
			);
		}
		$temp['slideItems'] = $tmp;
		
		$webroot = Common::getWebRoot();
		$href = urldecode($webroot.'/Api/Local_Single/singleList');
		$temp['listGameUrl'] = $href;
		
		$data = $this->_getSingleListCache(1);
		$signleList = json_encode(array(
				'success' => $data  ? true : false ,
				'msg' => '',
				'sign' => 'GioneeGameHall',
				'data' => $data,
		));
		$temp['listData'] = $signleList;
		$temp['totalCount'] = $data['totalCount'];
		
		$apcu->set($cacheKey, $temp, $this->cacheExpire);
		$this->localOutput('','',$temp);
	}
    
	/**
	 * 单机列表
	 */
    public function singleListAction() {
    	$page = intval($this->getInput('page'));
    	
    	// 检查参数的有效性，避免apcu被攻击
    	$apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
    	$total = $apcu->get(Util_CacheKey::SINGLE_LIST . "total");
    	if ($total) {
    		$maxPage = ceil($total / $this->perpage);
    		if ($page > $maxPage) {
    			$page = $maxPage;
    		}
    	}
    	if ($page < 1) $page = 1;
    	
    	$data = $this->_getSingleListCache($page);
    	$this->localOutput('','',$data);
    }
    
    private function _getSingleListCache($page) {
    	$apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
    	$data = $apcu->get(Util_CacheKey::SINGLE_LIST . $page . "_" . $this->perpage);
    	if ($data === false){
    		$data = $this->_getSingleList($page);
    		$apcu->set(Util_CacheKey::SINGLE_LIST . $page . "_" . $this->perpage, $data, $this->cacheExpire);
    		$apcu->set(Util_CacheKey::SINGLE_LIST . "total", $data['totalCount'], $this->cacheExpire);
    	}
    	return $data;
    }
    
    private  function _getSingleList($page) {
		if ($page < 1) $page = 1;
		$webroot = Common::getWebRoot();
		
		list($total, $games) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>1, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
		$gameIds = Resource_Service_Games::getGameIds($games); 
		$tmp = Resource_Service_Games::getGameData($gameIds);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		
		//第一页
		if($page == 1){
		    $ad = Game_Api_SingleRecommendBanner::getOldVersionBannerData();
			foreach($ad as $key=>$value){
				$topad = array();
				$topad = Client_Service_Ad::getDataAd($value['ad_ptype'],$value['title'],$value['link'],$value['img'],'','');
				if($topad['viewType']){
					$temp_data[] = array(
							'viewType'=>$topad['viewType'],
							'title'=>html_entity_decode($topad['title']),
							'imageUrl'=>$topad['imageUrl'],
							'param'=>$topad['param'],
							'ad_id' => $value ['id'],
					);
				}
			}
			$slide_data['slideItems'] = $temp_data;
			$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total,'slideData'=>json_encode($slide_data));
		}else{
			$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		}

		return $data;
    }
}