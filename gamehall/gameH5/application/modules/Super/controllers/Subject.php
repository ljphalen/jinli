<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Super_BaseController {
	
	public $actions =array(
				'index' => '/games/index',
				'detailUrl' => '/super/game/detail/',
			    'tjUrl'=>'/super/game/tj'
			);
	public $perpage = 8;
	
	/**
	 * subject list
	 */
	public function indexAction() {
		$title = '游戏专题';
		$this->setSource();
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$params = array('status' => 1);
    	$params['start_time'] = array('<=', Common::getTime());
    	$params['end_time'] = array('>=', Common::getTime());
		list($total, $subjects) = Client_Service_Subject::getList(1, 8,  $params);
		
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		$hasnext = (ceil((int) $total / 8) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('subjects', $subjects);
		$this->assign('page', $page);
		$this->assign('title', $title);
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
	}
	
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		$params = array('status' => 1);
		$params['start_time'] = array('<=', Common::getTime());
		$params['end_time'] = array('>=', Common::getTime());
		list($total, $subjects) = Client_Service_Subject::getList($page, 8,  $params);
		
		$temp = array();
		foreach($subjects as $key=>$value) {
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], 'SUBJECT', $webroot.'/super/subject/detail/?id='.$value['id'],'SUBJECT');
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['icon']);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));

		$info = Game_Service_Subject::getSubject($id);
		$this->assign('info', $info);
		$title = $info['title'];
		$page = intval($this->getInput('page'));
		
		if ($page < 1) $page = 1;
		//get game_ids 
	    $subject_game_ids = Game_Service_Game::getIdxSubjectBySubjectId(array('subject_id'=>$id));
		$tmp = array();
		if($subject_game_ids){
			foreach($subject_game_ids as $key=>$value){
				array_push($tmp,$value['game_id']);
			}
		}
		if($tmp){
		  list($total, $games) = Game_Service_Game::getList(1, $this->perpage, array('id'=>array("IN", $tmp)));
		}
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('games', $games);
		$this->assign('page', $page);
		$this->assign('id', $id);
		$this->assign('title', $title);
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
	}
	
	/**
	 * subject json list
	 */
	public function moreSjAction(){
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		if(!$intersrc) {
			$intersrc = 'SUBJECT';
		}
		if ($page < 1) $page = 1;
		//get game_ids 
	    $subject_game_ids = Game_Service_Game::getIdxSubjectBySubjectId(array('subject_id'=>$id));
		$tmp = array();
		if($subject_game_ids){
			foreach($subject_game_ids as $key=>$value){
				array_push($tmp,$value['game_id']);
			}
		}
		if($tmp){
			list($total, $games) = Game_Service_Game::getList($page, $this->perpage, array('id'=>array($tmp)));
			
			$temp = array();
			foreach($games as $key=>$value) {
				$temp[$key]['id'] = $value['id'];
				$temp[$key]['name'] = $value['name'];
				$temp[$key]['resume'] = $value['resume'];
				$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], 'GAME', $value['link'],'SUBJECT');
				$temp[$key]['alink'] = urldecode($this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
				$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
			}
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
}
