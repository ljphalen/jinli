<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Local_RankController extends Api_BaseController{

	public $perpage = 10;
	
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
    		
    		//处理客户端最新
    		if($apiType == 'client'){
    			$attach = 0;
    			if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) $attach =1;
    			$info = Resource_Service_GameData::getGameAllInfo($value['id']);
    		   
    			$data = array(
    					'img'=>urldecode($info['img']),
    					'name'=>html_entity_decode($info['name']),
    					'resume'=>html_entity_decode($info['resume']),
    					'package'=>$info['package'],
    					'link'=>$info['link'],
    					'gameid'=>$info['id'],
    					'size'=>$info['size'].'M',
    					'category'=>$info['category_title'],
    					'attach' =>  intval($attach),
    					'device' => $info['device'],
    					'hot' => Resource_Service_Games::getSubscript($info['hot']),
    					'viewType' => 'GameDetailView',
    					'date'=> ($flag == 1 ? date("m月d日",$taste['start_time']) : date("m月d日",$value['online_time']) ) ,
    					'score' => $info['client_star'],
    					'freedl' => $info['freedl'],
    					'reward' => $info['reward']
    			);    		
    		
    			$client_games[] = $data;
    		}
    		
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
	 */
	public function webIndexAction() {
		$flag = intval($this->getInput('flag'));
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		
		$sp = $this->getInput('sp');
		$sp = explode('_',$sp);
		$version = $sp[1];
				
		$data = $this->_IndexList($page, $intersrc, $flag, $version, 'web');
		$this->localOutput('','',$data);
	}
    
	/**
	 * 本地化客户端 获取排行 最新/下载最多数据
	 */
	public function clientIndexAction() {
		$flag = 1;
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		
		$sp = $this->getInput('sp');
		$sp = explode('_',$sp);
		$version = $sp[1];
	
		$data = $this->_IndexList($page, $intersrc, $flag, $version, 'client');
		$this->localOutput('','',$data);
	
	}
}
