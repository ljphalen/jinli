<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Local_RankController extends Api_BaseController{

	public $mPageLimit = 10;
	
	const REQUEST_API_TYPE_FOR_CLIENT = 'client';
	const REQUEST_API_TYPE_FOR_H5     = 'web';
	const DATA_TYPE_FOR_NEWEST_GAME = 1;
	const DATA_TYPE_FOR_DOWNLOAD_MORE_GAME = 0;
	const DEAFAULT_NEWEST_GAME_INTERSRC = 'Newrelease';
	const DEAFAULT_MOSTDOWNLOAD_GAME_INTERSRC = 'Mostdownload';
	
	
	private $mPage = null;
	private $mIntersrc = null;
	private $mClientVersion = null;
	private $mSp = null;
	private $mDataType = null;
	
	/**
	 * 最新/下载最多通用处理方法
	 * $page       当前页码
	 * $intersrc   统计参数
	 * $flag       1为最新，0为下载最多
	 * $version    当前版本
	 * $apiType    接口类型，client为客户端，web为服务端
	 */
	private  function _IndexList($page, $intersrc, $flag, $version, $apiType) {

    	if ($page < 1) $page = 1;
    	
    	if($flag == 1){
    		//最新游戏
    		$limit = Game_Service_Config::getValue('game_rank_newnum');
    		$this->perpage = min($this->perpage, $limit);
    		$params = array('status'=> 1,'game_status'=>1,'start_time'=>array('<=',Common::getTime()));
    		list($total, $newgames) = Client_Service_Taste::getList($page, $this->perpage, $params, array('start_time' => 'DESC','game_id' => 'DESC'));
    		$new_game_list = array();
    		foreach($newgames as $key=>$value) {
    			$info = Resource_Service_GameData::getGameAllInfo($value['game_id']);
    			if ($info) $resource_games[] = $info;
    		}
    	} else {
    		//下载最多
    		$limit = Game_Service_Config::getValue('game_rank_mostnum');
    		$this->perpage = min($this->perpage, $limit);
    		if (is_array($this->filter) && count($this->filter)) {
    			$params['GAME_ID'] = array('NOT IN', $this->filter);
    		}
    		$params = array();
    		list(, $bi_games) = Client_Service_RankResult::getList(1, $limit, $params);
    		
    		$resource_ids = Common::resetKey($bi_games, 'GAME_ID');
    		$resource_ids = array_unique(array_keys($resource_ids));
    			
    		if($resource_ids){
    			list($total, $resource_games) = Resource_Service_Games::search($page, $this->perpage, array('id'=>array('IN', $resource_ids),'status'=>1));
    		}
    		
    	}
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	
    	$i = 0;
    	foreach($resource_games as $key=>$value) {
    		
    		$num = $i + (($page - 1) * $this->perpage);    		
    		if ($num >= $total) break;
    		
    		if ($flag == 1) {
    			$taste = Client_Service_Taste::getTasteGame(array('game_id'=>$value['id']));
    			$date = date("Y-m-d",$taste['start_time']);
    		} else {
    			$date = date("Y-m-d",$value['online_time']);
    		}
    		
    		//构造广告（当成广告处理）
    		$value['ad_ptype'] = 1;
    		$value['link'] = $value['id'];
    		
    		$tj = $intersrc ? $intersrc : ($flag ? 'Newrelease' : 'Mostdownload');
    		if(strnatcmp($version, '1.4.8') < 0){
    			$rank_games[$key] = Client_Service_IndexAd::cookAd($value, $tj, (($page - 1) * $this->perpage)+ $i + 1);
    		} else {
    			$rank_games[$key] = Client_Service_IndexAdI::cookAd($value, $tj, (($page - 1) * $this->perpage)+ $i + 1);
    		}
    		
    		$rank_games[$key]['title'] = $value['name'];
    		$rank_games[$key]['date'] = ($flag == 1 ? date("m月d日",$taste['start_time']) : $date ) ;
    		$tmp[$date][] = $rank_games[$key];
    		
    		
    		
    		$i++;
    	}
    	
    	if($apiType =='web' && $flag == 1 ){         //处理服务端最新
    		foreach($tmp as $k=>$v){
    			$temp[$k] = $v;
    		}
    		
    		foreach($temp as $k=>$v){
    			$temp1[] = array(
    					'date'=>$k,
    					'list'=>$v,
    			);
    		}
    		
    	} else if($apiType == 'web' && $flag != 1) {  //处理服务端下载最多
    		$temp1 = $rank_games;
    	}
    	
        if( $apiType == 'web'){
        	$datas = array('list'=>$temp1, 'hasnext'=>$hasnext, 'curpage'=>$page);
        } 
        if($apiType == 'client'){
        	$datas = array('list'=>$client_games, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>intval($total));
        }
        
        return $datas;
	
	}
	/**
	 * 服务端获取排行接口 获取排行最新/下载最多数据
	 * $flag 1为最新，0为下载最多
	 */
	/**
	 * 最新/下载最多通用处理方法
	 * $page       当前页码
	 * $intersrc   统计参数
	 * $flag       1为最新，0为下载最多
	 * $version    当前版本
	 * $apiType    接口类型，client为客户端，web为服务端
	 */
	public function webIndexAction() {
		$this->getReuestInputParam ();
        $this->checkInputParams();
        if($this->mDataType == 1){
        	list($total, $newestGameList) = $this->getNewestGameList();
        }else{
        	list($total, $gameList) = $this->getDownloadMoreGameList();
        }
        
        $gameList = $this->makeGameListForWeb($gameList );
        $hasnext = $this->mPageLimit * $this->mPage  <  $total ? true : false;
        $data = array('list'      =>$gameList,
		        	  'hasnext'   =>$hasnext,
		        	  'curpage'   =>$this->mPage,
		        	  'totalCount'=>intval($total)
        );
        $this->localOutput('','',$data);
	}
	
    
	/**
	 * 本地化客户端 获取排行 最新/下载最多数据
	 */
	public function clientIndexAction() {
		
		$this->getReuestInputParam ();
        $this->checkInputParams();
        list($total, $newestGameList) = $this->getNewestGameList();
        $newestGameList = $this->makeNewestGameList( $newestGameList );
		$hasnext = $this->mPageLimit * $this->mPage  <  $total ? true : false;
		$data = array('list'      =>$newestGameList, 
				      'hasnext'   =>$hasnext,
				      'curpage'   =>$this->mPage,
				      'totalCount'=>intval($total)
				      );
		$this->localOutput('','',$data);
		
	}
	
	private function makeGameListForWeb($newestGameList) {
		
		$gameList = array();
		foreach($newestGameList as $key=>$value) {
			
			if ($this->mDataType == self::DATA_TYPE_FOR_NEWEST_GAME) {
				$date = date("Y-m-d",$value['start_time']);
				$gameId = $value['game_id'];
			} else {
				$date = date("Y-m-d",$value['online_time']);
				$gameId = $value['id'];
			}
			
			$adInfo['ad_ptype'] = 1;
			$adInfo['link'] = $gameId;
			$statisticsParam = $this->mIntersrc?$this->mIntersrc:($this->mDataType?self::DEAFAULT_NEWEST_GAME_INTERSRC:self::DEAFAULT_NEWEST_GAME_INTERSRC);
			$position        = (($this->mPage - 1) * $this->perpage)+ $key + 1;
			
			if(strnatcmp($this->mClientVersion, '1.4.8') < 0){
				$gameInfo[$key] = Client_Service_IndexAd::cookAd($adInfo,  $statisticsParam, $position);
			} else {
				$gameInfo[$key] = Client_Service_IndexAdI::cookAd($adInfo, $statisticsParam, $position);
			}
			$gameInfo[$key]['title'] = $gameInfo['name'];
			$gameInfo[$key]['date']  = $this->mDataType == self::DATA_TYPE_FOR_NEWEST_GAME?date("m月d日", $date):$date ;
			
			//1 最新 
			if($this->mDataType == self::DATA_TYPE_FOR_NEWEST_GAME){
				$gameList[] = array('data'=>date("Y-m-d",$date),
						            'list'=>$gameInfo
				);
			}else{
				$gameList[] = $gameInfo;
			}
		}
		return $gameList;
	}
	
	
	private function makeNewestGameList($newestGameList) {
		$gameList = array();
		foreach($newestGameList as $key=>$value) {
			$attach = 0;
			if (Client_Service_Gift::getGiftNumByGameId($value['game_id'])){
				$attach = 1;
			} 	
			$gameInfo = Resource_Service_GameData::getGameAllInfo($value['game_id']);
			$data = array(
					'img'=>urldecode($gameInfo['img']),
					'name'=>html_entity_decode($gameInfo['name']),
					'resume'=>html_entity_decode($gameInfo['resume']),
					'package'=>$gameInfo['package'],
					'link'=>$gameInfo['link'],
					'gameid'=>$gameInfo['id'],
					'size'=>$gameInfo['size'].'M',
					'category'=>$gameInfo['category_title'],
					'attach' =>  $attach,
					'device' => $gameInfo['device'],
					'hot' => Resource_Service_Games::getSubscript($gameInfo['hot']),
					'viewType' => 'GameDetailView',
					'date'=>  date("m月d日",$value['start_time']) ,
					'score' => $gameInfo['client_star'],
					'freedl' => $gameInfo['freedl'],
					'reward' => $gameInfo['reward']
			);
			$gameList[] = $data;
		}
		return $gameList;
	}

	
	private function getNewestGameList(){
		//最新游戏
		$limit = Game_Service_Config::getValue('game_rank_newnum');
		$this->mPageLimit = min($this->mPageLimit, $limit);
		$params = array('status'=> 1,
				        'game_status'=>1,
				        'start_time'=>array('<=' ,Common::getTime()));
		list($total, $newestGameList) = Client_Service_Taste::getList($this->mPage, 
				                                                $this->mPageLimit,
				                                                $params, 
				                                                array('start_time' => 'DESC', 
				                                                	  'game_id' => 'DESC'));
		
		return array($total, $newestGameList);
	}
	
	private function  getDownloadMoreGameList(){
		//下载最多
		$limit = Game_Service_Config::getValue('game_rank_mostnum');
		$this->perpage = min($this->perpage, $limit);
		if (is_array($this->filter) && count($this->filter)) {
			$params['GAME_ID'] = array('NOT IN', $this->filter);
		}
		$params = array();
		list(, $rankGameList) = Client_Service_RankResult::getList(1, $limit, $params);
		
		$gameIds = Common::resetKey($rankGameList, 'GAME_ID');
		$gameIds = array_unique(array_keys($gameIds));
		$gameList = array();
		if($gameIds){
			list($total, $gameList) = Resource_Service_Games::search($this->mPage, 
					                                                 $this->perpage, 
					                                                 array('id'=>array('IN', $gameIds), 'status'=>1));
		}
		return $gameList;
	}
	
	private function getReuestInputParam() {
		$this->mPage = intval($this->getInput('page'));
		$this->mIntersrc = $this->getInput('intersrc');
		$this->mSp = $this->getInput('sp');
		$this->mDataType = $this->getInput('flag')?$this->getInput('flag'):self::DATA_TYPE_FOR_NEWEST_GAME;
	}
	

	
	private function checkInputParams() {
		if ($this->mPage < 1){
			$this->mPage = 1;
		} 
		if(!$this->mSp){
			return ;
		}	
		$this->mClientVersion = Common::parseSp($this->mSp, 'game_ver');
	}

}
