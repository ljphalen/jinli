<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GamesController extends Front_BaseController {

	public $actions = array(
			'indexUrl' => '/games/index',
		);
	public $perpage = 5;
	
	public function indexAction() {
		$ptype = intval($this->getInput('ptype'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		$search = array();
		if ($ptype) $search['ptype'] = $ptype;
		list($total, $games) = Games_Service_Game::getList($page, $this->perpage, $search);
		$o_games = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach ($games as $key => $value) {
			array_push($o_games, array(
					'id' => $value['id'],
					'name' => $value['name'],
					'resume' => $value['resume'],
					'package' => $value['package'],
					'min_resolution' =>$value['min_resolution'],
					'max_resolution' =>$value['max_resolution'],
					'sys_version' =>$value['sys_version'],
					'img'=>$webroot . '/attachs' . $value['img'], 
					'link' => $value['link'],
					'size' => $value['size']
				));
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$o_games, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}

	public function typesAction() {
		list(, $types) = Games_Service_Type::getList(0, 100, array('status'=>1));
		$tmp = array(array('id'=>0, 'title'=>'全部'));
		foreach($types as $key=>$value) {
			array_push($tmp, array('id'=>$value['id'], 'title'=>$value['title']));
		}
		$this->output(0, '', $tmp);
	}
	
	public function detailAction() {
		$id = intval($this->getInput('id'));
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$info = Games_Service_Game::getGame($id);
		$info['update_time'] = date('Y-m-d', $info['update_time']);
		$info['img'] = $webroot . '/attachs' . $info['img'];
		$info['descrip'] = html_entity_decode($info['descrip']);

		list(, $gimgs) = Games_Service_GameImg::getList(0,10, array('game_id'=>$id));
		foreach ($gimgs as $key => $value) {
			$gimgs[$key]['img'] = $webroot . '/attachs' . $value['img'];
		}
		
		list(, $types) = Games_Service_Type::getAllType();
		$types = Common::resetKey($types, 'id');

		$this->output(0, '', array('info'=>$info, 'gimgs'=>$gimgs, 'types'=>$types));
	}
	
	public function detail2Action() {
		$id = intval($this->getInput('id'));
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$info = Games_Service_Game::getGame($id);
		$info['update_time'] = date('Y-m-d', $info['update_time']);
		$info['img'] = $webroot . '/attachs' . $info['img'];
		unset($info['descrip']);
		$this->output(0, '', $info);
	}
	
	public function detail_packageAction() {
		$package = $this->getInput('package');
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$info = Games_Service_Game::getByPackage($package);
		$info['update_time'] = date('Y-m-d', $info['update_time']);
		$info['img'] = $webroot . '/attachs' . $info['img'];
		
		list(, $gimgs) = Games_Service_GameImg::getList(0,10, array('game_id'=>$info['id']));
		foreach ($gimgs as $key => $value) {
			$gimgs[$key]['img'] = $webroot . '/attachs' . $value['img'];
		}
		
		list(, $types) = Games_Service_Type::getAllType();
		$types = Common::resetKey($types, 'id');
		
		$this->output(0, '', array('info'=>$info, 'gimgs'=>$gimgs, 'types'=>$types));
	}
	
	public function topicAction() {
		$id = intval($this->getInput('id'));
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$subject = Games_Service_Subject::getSubject($id);
		$o_subject = array( 'img'=>$webroot . '/attachs' . $subject['img'], 
							'title' => $subject['title'],
							'resume' => $subject['resume']);

		list($total, $games) = Games_Service_Game::getList(0, 100, array('subject'=>$id));
		$o_games = array();
		foreach ($games as $key => $value) {
			array_push($o_games, array(
					'id' => $value['id'],
					'name' => $value['name'],
					'resume' => $value['resume'],
					'package' => $value['package'],
					'min_resolution' =>$value['min_resolution'],
					'max_resolution' =>$value['max_resolution'],
					'sys_version' =>$value['sys_version'],
					'img'=>$webroot . '/attachs' . $value['img'], 
					'link' => $value['link'],
					'size' => $value['size'],
				));
		}
		$this->output(0, '', array('subject'=>$o_subject, 'games'=>$o_games, 'total'=>$total));
	}
	
	public function getsAction() {
		$ids = $this->getInput('ids');
		$ids = explode(',', html_entity_decode($ids));
		if (!$ids) $this->output(-1, '');
		$ret = Games_Service_Game::getGames($ids);
		if(!$ret) $this->output(-1, '获取下载资源失败.');
		$tmp = array();
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$tmp = array();
		foreach($ret as $key=>$value) {
			array_push($tmp, array(
				'id' =>  $value['id'],
				'name' => $value['name'],
				'size' => $value['size'],
				'package' => $value['package'],
				'resume' => $value['resume'],
				'link' => urlencode($value['link']),
				'img' => $webroot . '/attachs' . $value['img']
			));
		}
		$this->output(0, '', $tmp);
	}
	
	public function packageAction() {
		$time = intval($this->getInput("time"));
		list(, $packages) = Games_Service_Package::getPackagesByTime(0, 100, $time);
		
		$temp = array();
		foreach($packages as $key=>$value){
			array_push($temp, $value['status'] . ':' . $value['package']);
		}
		exit(implode(",", $temp));
	}
	
	public function allAction() {
		list($total, $result) = Games_Service_Game::getAllGame();
		
		$tmp = array();
		foreach($result as $key=>$value) {
			array_push($tmp, array(
				'package'=>$value['package'],
				'version'=>$value['version'],
				'link'=>$value['link'].'?gn_id='.$value['id'].'&sys_version='.$value['sys_version'].'&min_resolution='.$value['min_resolution'].'&max_resolution='.$value['max_resolution'].'&path=3'
			));
		}
		exit(json_encode($tmp));
	}
	
	public function clientTjAction() {
		exit('HTTP_TJ_OK');
	}
	
	public function downloadAction() {
		$url = $this->getInput('url');
		$this->redirect($url);
		exit;
	}
}
