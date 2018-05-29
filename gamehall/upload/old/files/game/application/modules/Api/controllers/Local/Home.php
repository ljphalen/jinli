<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 游戏大厅 1.5.6 版本 开始使用
 * @author lichanghua
 *
 */
class Local_HomeController extends Api_BaseController {
	public $perpage = 10;   
	 
	static $adType = array(
		Client_Service_Ad::AD_TYPE_TURN  =>  array('key'=>Util_JsonKey::SLIDE_ITEMS,'txt'=>'轮播图广告'),
		Client_Service_Ad::AD_TYPE_PICTURE  =>  array('key'=>Util_JsonKey::IMAGE_AD_ITEMS,'txt'=>'广告位'),
		Client_Service_Ad::AD_TYPE_ACTIVITY  =>  array('key'=>Util_JsonKey::ACTIVITY_ITEM   ,'txt'=>'文字公告'),
	);
	
    /**
     * 首页轮播图
     */
    public function slideAdAction() {
    	$sp = $this->getInput('sp');
    	if(!$sp) {
    		return false;
    	}
    	$spArr = Common::parseSp($sp);
		$this->saveFirstPageBehaviour($spArr['imei']);
    	$clientVersion = Common::getClientVersion($spArr['game_ver']);
    	$dataList = Game_Api_RecommendBanner::getClientBanner($clientVersion);

    	$data = array();
    	$data['sign'] = 'GioneeGameHall';
    	$data['msg'] = '';
    	$data['success'] = true;
    	$data['data'] = $dataList;
    	$this->clientOutput($data);
    }
    
    /**
     * 文字公告
     */
    public function textActivityAdAction() {
    	$sp = $this->getInput('sp');
    	$spArr = Common::parseSp($sp);
    	$clientVersion = Common::getClientVersion($spArr['game_ver']);
        $dataList = Game_Api_RecommendText::getClientText();

        $data = array();
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $dataList;
    	$this->clientOutput($data);
    }
    
    /**
     * 1.5.7免流量专区
     * 
     */
    public function  freeFlowAction(){
    	
    	$freedls = Freedl_Service_Hd::getFreedList();
    	
    }
    
    /**
     * 每日一荐
     */
    public function dailyRecommendAction(){
        $sp = $this->getInput('sp');
        $spArr = Common::parseSp($sp);
        $systemVersion = Common::getSystemVersion($spArr['android_ver']);
    	$clientVersion = Common::getClientVersion($spArr['game_ver']);
        
        $dataList = Game_Api_RecommendDay::getClientDay($systemVersion, $clientVersion);
        
    	if(Common::isAfterVersion($clientVersion, '1.5.8')) {
    	    $data['sign'] = 'GioneeGameHall';
    	    $data['success'] = true;
    	    $data['msg'] = '';
    	    $data['data']['list'] = $dataList;
    	}else{
    	    $data['sign'] = 'GioneeGameHall';
    	    $data['success'] = true;
    	    $data['msg'] = '';
    	    $data['data']['listData']['sign'] = 'GioneeGameHall';
    	    $data['data']['listData']['success'] = true;
    	    $data['data']['listData']['msg'] = '';
    	    $data['data']['listData']['data']['list'] = $dataList;
    	}
    	$this->clientOutput($data);
    }

    /**
     * 首页推荐列表 1.5.7
     */
    public function newRecomendListAction() {
    	$page = intval($this->getInput('page'));
    	$sp = $this->getInput('sp');
    	if(!$sp || !$page) {
    		return false;
    	}
    	$data = $this->getRecommendCacheData($page, $sp);
    	$this->clientOutput($data);
    }
    
    /**
     * 首页推荐列表 1.5.7
     */
    public function newRecomendFirstPageListAction() {
    	$sp = $this->getInput('sp');
    	$page = $this->getInput('page');
    	if(!$sp) {
    		return false;
    	}
    	if(!page) {
    	    $page = 1;
    	}else{
    	    $page = intval($page);
    	}
    	$data = $this->getRecommendCacheData($page, $sp);
    	$spArr = Common::parseSp($sp);
		$this->saveFirstPageBehaviour($spArr['imei']);
    	$this->clientOutput($data);
    }
    
    private function getRecommendCacheData($page, $sp) {
    	if ($page < 1) $page = 1;
    	$spArr = Common::parseSp($sp);
    	$device  = $spArr['device'];
    	$clientVersion = Common::getClientVersion($spArr['game_ver']);
    	$systemVersion = Common::getSystemVersion($spArr['android_ver']);
    	$data = array();
    	$cacheListData = Game_Api_Recommend::getClientRecommend($page, $device, $systemVersion, $clientVersion);
    	if($page > 1 || Common::isAfterVersion($clientVersion, '1.5.8')) {
    	    $data['sign'] = 'GioneeGameHall';
    	    $data['msg'] = '';
    	    $data['success'] = true;
    	    $data['data'] = $cacheListData;
    	}else{
    	    $data['sign'] = 'GioneeGameHall';
    	    $data['msg'] = '';
    	    $data['success'] = true;
    	    $data['data']['listData']['sign'] = 'GioneeGameHall';
    	    $data['data']['listData']['success'] = true;
    	    $data['data']['listData']['msg'] = '';
    	    $data['data']['listData']['data'] = $cacheListData;
    	}
    	return $data;
    }

    /**
     * 推荐列表点击更多
     * @return boolean
     */
    public function recommendGameListAction() {
    	$recommendId = intval($this->getInput('recommendId'));
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	$sp = $this->getInput('sp');
    	if(!$recommendId){
    		$this->clientOutput(array());
    	}
        $pageData = array();
        if($recommendId) {
            $pageData = Game_Api_Recommend::getClientRecommendGamesList($recommendId, $page);
        }
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $pageData; 
        $this->clientOutput($data);
    }
    
    /**
     * 游戏附加属性
     */
    public function gameExtraInfoAction() {
    	header("Content-type:text/json");
    	exit("");
    }
    
    /**
     * 图片广告
     */
    public function imageAdAction() {
		$ads = $params = $announce = array();
		$params['ad_type'] = Client_Service_Ad::AD_TYPE_PICTURE;
		list(, $announce) = Client_Service_Ad::getCanUseNormalAds(1, 8, $params);
		$j = 1;
		foreach($announce as $k=>$v) {
			$info = Local_Service_IndexAd::cookClientAd($v, "ad3", $j++);
			$ads[$k] = $info;
			if(count($ads) == 2) break;
		}
		
		//专区免流量活动是否有
		$freeFlow[] = array(
				'viewType'=>'FreeFlowAreaView',
				'title'=>'免流量专区',
				'imageUrl'=> Common::getAttachPath() . Game_Service_Config::getValue('game_free_img'),
				'param'=> array(
					'url'=>'',
					'contentId'=>'',
					'gameId'=>'',
				),
				'ad_id'=>'0000',
				//'source'=>'freeevent',
		);
		
		//活动广告必须有２个，否则不显示
		if(count($ads) == 2){
			$ads = array_merge($ads, $freeFlow);
		}
		$data['imageAdItems']  = $this->getJsonData($params, $ads, '', 3);
		$this->localOutput('','',$data);
    }
    
    private  function getJsonData($params, $ads, $source ='', $num =0, $icon = false) {
    	$data = $item = array();
    	foreach ($ads as $key=>$value) {
    		if($value['viewType'] && $value['ad_id']){   //如果没有数据不显示
    			$item[] = array(
    				Util_JsonKey::VIEW_TYPE => $value['viewType'],
    				Util_JsonKey::TITLE =>html_entity_decode($value['title']),
    				Util_JsonKey::CONTENT =>html_entity_decode($value['title']),
    				Util_JsonKey::IMAGE_URL => $icon ? $value['icon'] : $value['img'],
    				Util_JsonKey::PARAM => $value['param'],
    				Util_JsonKey::SOURCE =>$source,
    				'ad_id'=>$value['ad_id'],
			    );
    		}
    		if($num) {
    			if(count($item) == $num) break;
    		}
    	}
    	
    	$data = $data[self::$adType[$params['ad_type']]['key']] = $item;
    	return $data;
    }
    
    /**
     * magnet
     */
    public function getMagnetAction() {
    	$version = $this->getInput ( "version" );
    	$response = array ();
    	$curVersion = Game_Service_Config::getValue('Magneticon_Ad_Version');
    	$params  = array(
    			'status' =>Client_Service_Ad::AD_STATUS_OPEN,
    			'start_time'=>array('<=',time()),
    			'end_time'=>array('>=',time()),
    			'ad_type' =>Client_Service_Ad::AD_TYPE_MAGNETICON,
    	);
    	list(, $ads) = Client_Service_Ad::getCanUseNormalAds(1, 1, $params);
    	$item = Local_Service_IndexAd::cookClientAd($ads[0]);
    	if($item['viewType']){
    		$response [Util_JsonKey::VIEW_TYPE] = $item['viewType'];
    		$response [Util_JsonKey::IMAGE_URL] = $item['img'];
    		$response [Util_JsonKey::PARAM] = $item['param'];
    	}
    	return $this->versionOutput ( 0, '', $response, $curVersion );
    }
    
    
    /**
     * 首页推荐列表 首页数据
     */
    public function listFirstPageAction() {
    	$sp = $this->getInput('sp');
    	$imei = Common::parseSp($sp, 'imei');
		$this->saveFirstPageBehaviour($imei);

    	$list = $this->_IndexList(1);
    	$listData = json_encode(array(
    			'success' => $list ? true : false ,
    			'msg' => '',
    			'sign' => 'GioneeGameHall',
    			'data' => $list,
    	));
    	$data[Util_JsonKey::LIST_DATA] = $listData;
    	$this->localOutput('','',$data);
    }
    
	private function saveFirstPageBehaviour($imei) {
		$behaviour = new Client_Service_ClientBehaviour($imei, Client_Service_ClientBehaviour::CLIENT_HALL);
        $behaviour->saveBehaviours(Client_Service_ClientBehaviour::ACTION_GET_FIRST_PAGE);
	}
    
    /**
     * 首页推荐列表 分页数据
     */
    public function recomendListAction() {
    	$page = intval($this->getInput('page'));
		$data = $this->_IndexList($page);
		$this->localOutput('','',$data);
    }
    
    private  function _IndexList($page) {
    	//推荐专题
    	if ($page < 1) $page = 1;
    	$status = Game_Service_Config::getValue('client_picture_status');//添加推荐图片
    	$interval = Game_Service_Config::getValue('client_picture_space');
    	
    	//游戏列表信息
    	$params =  array();
    	$params['ad_type'] = Client_Service_Ad::AD_TYPE_SUBJECT;
    	$params['status'] = Client_Service_Ad::AD_STATUS_OPEN;
    	//当前页面游戏信息
    	list($total, $subjects) = Client_Service_Ad::getCanUseApiAds($page, $this->perpage, $params);
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	foreach($subjects as $key=>$value) {
    		$subject_games[$key] = Local_Service_IndexAd::cookClientAd($value, "ad4");
    	}
    	//插图广告开关
    	if($status){
    		//获取插图广告
			$recAds = Client_Service_InsertAdData::getInsertPicAd();
			//当前页面广告位置
			list($adUsedCount, $previousPageGamesAfterLastAd) = Client_Service_InsertAdData::getAdPos($page,  $interval);
			//当前页面的广告
			$adsUnused = Client_Service_InsertAdData::getCurrpageAd($adUsedCount, $recAds, $interval);
			//插入当前数据
			$insertData['subjectGames'] = $subject_games;
			$insertData['adUnusedArr'] = $adsUnused;
			$insertData['interval'] = $interval;
			$insertData['previousPageGamesAfterLastAd'] = $previousPageGamesAfterLastAd;
			$pageData = Client_Service_InsertAdData::insertAdData($page, $insertData);
    	}
    	$data  = $status ? $pageData : $subject_games;
    	$data =  $this->_jsonData($data, 'ad4' ,$page, $hasnext);
    	return $data;
    }
    
    /**
     * 组装数据
     * @param unknown_type $ads
     * @param unknown_type $name
     * @param unknown_type $page
     * @param unknown_type $hasnext
     * @return array
     */
    private  function _jsonData($ads, $name, $page, $hasnext) {
    	$data = array('list'=>$ads, 'hasnext'=>$hasnext, 'curpage'=>$page);
    	return $data;
    }
}
