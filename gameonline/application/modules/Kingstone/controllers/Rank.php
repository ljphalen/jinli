<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class RankController extends Kingstone_BaseController{
	
	public $actions = array(
		'listUrl' => '/kingstone/rank/index',
		'detailUrl' => '/kingstone/index/detail/',
		'tjUrl' => '/kingstone/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$flag = intval($this->getInput('flag'));
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		$this->assign('configs', $configs);
		
		if ($page < 1) $page = 1;
		
		$games = array();
		$resource_games = array();
		
		if($flag == 1){
			//最新游戏
			$limit = Game_Service_Config::getValue('game_rank_newnum');
			$offset = min($limit, $this->perpage);
		 	list($total, $resource_games) = Resource_Service_Games::getList(1, $offset, array('status'=>1), array('online_time'=>'DESC'));
		} else {
			$limit = Game_Service_Config::getValue('game_rank_mostnum');
			list(, $bi_games) = Client_Service_Rank::getMostGames($limit,'');
			$resource_ids = Common::resetKey($bi_games, 'GAME_ID');
			$resource_ids = array_unique(array_keys($resource_ids));
			
			$this->perpage = min($this->perpage, $limit);
			
			if($resource_ids){
				list($total, $resource_games) = Resource_Service_Games::search(1, $this->perpage, array('id'=>array('IN', $resource_ids),'status'=>1));
			}
		}
		
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		
		foreach($resource_games as $key=>$value) {
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			if ($info){
				if ($checkVer >= 2) {
					//增加评测信息
					$info['pc_info'] = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
					//增加礼包信息
					$info['gift_info'] = Client_Service_IndexAdI::haveGiftByGame($info['id']);
				}
				$games[] = $info; 
			}
		}
		$total = $limit;
		$this->assign('games', $games);
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('checkver', $checkVer);
		$this->assign('sp', $sp);
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('flag', $flag);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction(){
		$flag = intval($this->getInput('flag'));
	    $page = intval($this->getInput('page'));
	    $intersrc = $this->getInput('intersrc');
	    if($intersrc){
	    	$t_id = $intersrc;
	    } else {
	    	$t_id = 'Mostdownload';
	    	if ($flag == 1) $t_id = 'Newrelease';
	    }
	    $this->assign('configs', Game_Service_Config::getValue('game_rank_mostnum'));
	    
		if ($page < 1) $page = 1;
		
		$games = array();
		$resource_games = array();
		$resource_ids = array();
		$bi_games = array();
		
		if($flag == 1){
			//最新游戏
		 	list($total, $resource_games) = Resource_Service_Games::getList($page, $this->perpage, array('status'=>1), array('online_time'=>'DESC'));
		 	$total = Game_Service_Config::getValue('game_rank_newnum');
		} else {
			$limit = Game_Service_Config::getValue('game_rank_mostnum');
			list(, $bi_games) = Client_Service_Rank::getMostGames($limit,'');
			$resource_ids = Common::resetKey($bi_games, 'GAME_ID');
			$resource_ids = array_unique(array_keys($resource_ids));
			
			$this->perpage = min($this->perpage, $limit);
			
			if($resource_ids){
				list($total, $resource_games) = Resource_Service_Games::search($page, $this->perpage, array('id'=>array('IN', $resource_ids),'status'=>1));
			}
			$total = $limit;
		}
				 
		foreach($resource_games as $key=>$value) {
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			if ($info) $games[] = $info;
		}
		
		$webroot = Common::getWebRoot();

		//判断游戏大厅版本
		$temp = array();
		$checkVer = $this->checkAppVersion();
		
		$i = 0;
		foreach($games as $key=>$value) {
			$data = array();
			$num = $i + (($page - 1) * $this->perpage);
			if ($num >= $total) break;
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&flag='.$flag.'&pc=1&intersrc='.$t_id.'&t_bi='.$this->getSource());
			if ($checkVer >= 2) {
				//加入评测链接
				$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($value['id']);
				$evaluationUrl = '';
				if ($evaluationId) {
					$evaluationUrl = ',评测,' . $webroot . '/kingstone/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $t_id . '&t_bi=' . $this->getSource();
				}
				//附加属性处理
				$attach = array();
				if ($evaluationId)	array_push($attach, '评');
				if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) array_push($attach, '礼');
			}
			
			$data = array(
						'id'=>$value['id'],
						'name'=>$value['name'],
						'resume'=>$value['resume'],
						'size'=>$value['size'].'M',
						'link'=>Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link']),
						'alink'=>urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&pc=1&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
						'img'=>urldecode($value['img']),
						'profile'=>$value['name'].','.$href.','.$value['id'].','.$value['link'].','.$value['package'].','.$value['size'].','.$value['min_sys_version_title'].','.$value['min_resolution_title'].'-'.$value['max_resolution_title'],
			);
			
			if ($checkVer >= 2) {
				//js a 标签 data-infpage 参数数据
				$data_info = '游戏详情,'.$href.','.$value['id'];
				$data['profile'] = $evaluationId ? $data_info . $evaluationUrl : $data_info;
				$data['attach'] = ($attach) ? implode(',', $attach) : '';
				$data['device'] = $value['device'];
				$data['data-type'] = 1;
			}
			$temp[] = $data;
			
			$i++;
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
