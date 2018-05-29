<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Local_ClientrankController extends Api_BaseController{

	public $perpage = 10;	
	/**
	 * 本地化周榜（原来老版的下载最多）
	 */
	public function clientWeekIndexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$data = $this->_IndexList($page, $intersrc, 'week', 2);
		$this->localOutput('','',$data[0]);
	
	}
	
	/**
	 * 本地化月榜
	 */
	public function clientMonthIndexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');	
		$data = $this->_IndexList($page, $intersrc, 'month', 2);
		$this->localOutput('','',$data[0]);
	
	}
	
	/**
	 * 客户端的新游
	 */
	public function newRankIndexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$data = $this->_IndexList($page, $intersrc, 'newRank', 2);
		$this->localOutput('','',$data[0]);
		
	
	}
	
	/**
	 * 客户端的上升最快
	 */
	public function upRankIndexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');		
		$data = $this->_IndexList($page, $intersrc, 'upRank', 2);
		$this->localOutput('','',$data[0]);
	
	}
	

	/**
	 * 客户端的网游排行
	 */
	public function onlineRankIndexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');	
		$data = $this->_IndexList($page, $intersrc, 'onlineRank', 2);
		$this->localOutput('','',$data[0]);
	
	}
	
	/**
	 * 客户端的单机排行
	 */
	public function pcRankIndexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$data = $this->_IndexList($page, $intersrc, 'pcRank', 2);
		$this->localOutput('','',$data[0]);
	
	
	}
	
		
	/**
	 * 
	 * @param $page         当前页
	 * @param $intersrc     统计参数
	 * @param $rankType     排行榜类型（周榜[week]/月榜[month]）
	 * @param $type         客户端/服务端(1服务端，2客户端)
	 * @return array        数组
	 */
	private  function _IndexList($page,$intersrc,$rankType, $type) {

		if ($page < 1) $page = 1;
		$games = array();
		$resource_games = array();
		$resource_ids = array();
		$bi_games = array();
		
		//默认的周榜
		if($rankType == 'week')  {
			$limit = Game_Service_Config::getValue('game_rank_weeknum');
			$limit =  $limit ? $limit : 50;
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_WeekNewRank::getList(1,$limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id =  Client_Service_WeekNewRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(, $bi_games) = Client_Service_WeekNewRank::getList(1,$limit,$params);
				
			}
		}
		
		//默认的月榜
		if($rankType == 'month')  {
			//取BI前一天数据
			$limit = Game_Service_Config::getValue('game_rank_monthnum');
			$limit =  $limit ? $limit : 50;
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_MonthRank::getList(1,$limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id = Client_Service_MonthRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(, $bi_games) = Client_Service_MonthRank::getList(1,$limit,$params);
			}
		}
		

		//新游排行
		if($rankType == 'newRank')  {
			$limit = Game_Service_Config::getValue('game_rank_cnewnum');
			$limit =  $limit ? $limit : 50;
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_NewRank::getList(1,$limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id = Client_Service_NewRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(, $bi_games) = Client_Service_NewRank::getList(1,$limit,$params);
			}
		}
		
		//上升最快
		if($rankType == 'upRank')  {
			$limit = Game_Service_Config::getValue('game_rank_fastestnum');
			$limit =  $limit ? $limit : 50;
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_FastestRank::getList(1,$limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id = Client_Service_FastestRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(, $bi_games) = Client_Service_FastestRank::getList(1,$limit,$params);
			}
		}
		
		//网游排行
		if($rankType == 'onlineRank')  {
			$limit = Game_Service_Config::getValue('game_rank_onlineknum');
			$limit =  $limit ? $limit : 50;
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_OlgRank::getList(1,$limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id = Client_Service_OlgRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(, $bi_games) = Client_Service_OlgRank::getList(1,$limit,$params);
			}
		}
		
		//单击排行
		if($rankType == 'pcRank')  {
			$limit = Game_Service_Config::getValue('game_rank_pcnum');
			$limit =  $limit ? $limit : 50;
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_SingleRank::getList(1,$limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id = Client_Service_SingleRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(, $bi_games) = Client_Service_SingleRank::getList(1,$limit,$params);
			}
		}
		
		//去掉重复的游戏ID
		$resource_ids = Common::resetKey($bi_games, 'game_id');
		$resource_ids = array_unique(array_keys($resource_ids));
		
		//条件的拼装
		if( $resource_ids ){
			$games_params = array('id'=>array('IN', $resource_ids),'status'=>1);
		}else{
			$games_params = array('status'=>1);
		}
		
		//取得分页数据
		list($total, $resource_games) = Resource_Service_Games::search($page, $this->perpage,$games_params );			 
		foreach($resource_games as $key=>$value) {
			$info = Resource_Service_GameData::getGameAllInfo($value['id']);
			if ($info) $games[] = $info;
		}
		
		$webroot = Common::getWebRoot();
		//判断游戏大厅版本
		$temp = array();
		$checkVer = $this->checkAppVersion();
		
		//组装数据
		$i = 0;
		foreach($games as $key=>$value) {
			$data = array();
			$num = $i + (($page - 1) * $this->perpage);
			if ($num >= $total) break;
			//附加属性处理,1：礼包
			$attach = 0;
			if ($value['gift']) $attach = 1;
			
			$data = array(
					'img'=>urldecode($value['img']),
					'name'=>html_entity_decode($value['name']),
					'resume'=>html_entity_decode($value['resume']),
					'package'=>$value['package'],
					'link'=>$value['link'],
					'gameid'=>$value['id'],
					'size'=>$value['size'].'M',
					'category'=>$value['category_title'],
					'attach' => intval($attach),
					'device' => $value['device'],
					'hot' => Resource_Service_Games::getSubscript($value['hot']),
					'viewType' => 'GameDetailView',
					'date'=>date("Y-m-d",$value['online_time']),
					'score' => $value['client_star'],
					'freedl' => $value['freedl'],
					'reward' => $value['reward']
			);
			
			$date = date("Y-m-d",$value['online_time']);
			$tmp[$date][] = $data;
			$temp[] = $data;
			$i++;
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$data = array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>intval($total));
		$tmp = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page);
		return array($data,$tmp);
	}
}
