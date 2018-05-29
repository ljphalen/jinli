<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Channel_BaseController {
	
	public $actions =array(
				'index' => '/channel/games/index',
				'detailUrl' => '/channel/index/detail/',
			    'subDetailUrl' => '/channel/subject/detail/',
			    'tjUrl'=>'/channel/index/tj'
			);
	public $perpage = 10;
	public $cacheKey = 'Channel_Subject_index';
	/**
	 * subject list
	 */
	public function indexAction() {
		//首页bannel
// 		list(, $bannel) = Game_Service_Ad::getCanUseNormalAds(1, 1, array('ad_ltype'=>11));
// 		$this->assign('bannel', $bannel[0]);
// 		$page = intval($this->getInput('page'));
// 		if ($page < 1) $page = 1;
		
// 		$params = array('status' => 1);
//     	$params['start_time'] = array('<=', Common::getTime());
//     	$params['end_time'] = array('>=', Common::getTime());
// 		list($total, $subjects) = Client_Service_Subject::getList(1, 8,  $params);
// 		$hasnext = (ceil((int) $total / 8) - 1) > 0 ? true : false;
// 		$this->assign('hasnext', $hasnext);
// 		$this->assign('subjects', $subjects);
// 		$this->assign('page', $page);
		
		
		$this->assign('source', $this->getSource());
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		$this->assign('configs', $configs);
		$this->assign('cache', Game_Service_Config::getValue('game_client_cache'));
	}
	
	public function moreAction() {
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	    
		$params = array('status' => 1);
		$startTime = strtotime(date('Y-m-d H:00:00'));
		$endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
		$params['start_time'] = array('<=', $startTime);
		$params['end_time'] = array('>=', $endTime);
		list($total, $subjects) = Client_Service_Subject::getList($page, 8,  $params);
		
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($subjects as $key=>$value) {
			$t_id = 'SUBJECT'.$value['id'];
			$href = urldecode($webroot.$this->actions['subDetailUrl']. '?id=' . $value['id'].'&intersrc='.(!$intersrc ? $t_id : $intersrc).'&t_bi='.$this->getSource());
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $t_id, $webroot.'/subject/detail/?id='.$value['id'].'&intersrc='.$t_id);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['icon']);
			$temp[$key]['data-infpage'] = $value['title'].','.$href;
			$temp[$key]['start_time'] = date("Y-m-d",$value['start_time']);
// 			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['resume'] = strip_tags(html_entity_decode($value['resume']));
		}
		
		$hasnext = (ceil((int) $total / 8) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		
		$info = Client_Service_Subject::getSubject($id);
		$this->assign('info', $info);
		$page = intval($this->getInput('page'));
		
		if ($page < 1) $page = 1;
		//get game_ids 
	    list($total, $subject_game_ids) = Client_Service_SubjectGames::getPageDistinctGameList(1, $this->perpage, array('subject_id'=>$id, 'game_status'=>1));
	    $resource_ids = Common::resetKey($subject_game_ids, 'game_id');
	    $resource_ids = array_keys($resource_ids);
		
		if ($resource_ids) {
    		foreach($resource_ids as $key=>$value) {
    			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
	    		if ($info) $games[] = $info; 
    		}
    	}

		$this->assign('games', $games);
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
		$this->assign('total', $total);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * subject json list
	 */
	public function moreSjAction(){
	    $id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$intersrc = 'SUBJECT'.$id;
		$page = intval($this->getInput('page'));
		$webroot = Common::getWebRoot();
		
		if ($page < 1) $page = 1;
		//get game_ids 
	    list($total,$subject_game_ids) = Client_Service_SubjectGames::getPageDistinctGameList($page, $this->perpage, array('subject_id'=>$id, 'game_status'=>1));
		$subject_game_ids = Common::resetKey($subject_game_ids, 'game_id');
		$resource_ids = array_keys($subject_game_ids);

		if ($resource_ids) {
			foreach($resource_ids as $key=>$value) {
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
				if ($info) $games[] = $info;
			}
		}
		
		foreach($games as $key=>$value) {
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[] = array(
					'id'=>$value['id'],
					'name'=>$value['name'],
					'resume'=>$value['resume'],
					'size'=>$value['size'].'M',
					'link'=>Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link']),
					'alink'=>urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
					'img'=>urldecode($value['img']),
			        'profile'=>htmlentities(html_entity_decode($value['name'], ENT_QUOTES)).','.$href.','.$value['infpage'],
			);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
}
