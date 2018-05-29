<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Bbs_ManageController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Bbs_Manage/index',
		'addGameUrl' => '/Admin/Bbs_Manage/addGame',
		'addGamePostUrl' => '/Admin/Bbs_Manage/addGamePost',
		'addSetUrl' => '/Admin/Bbs_Manage/addSet',
		'addSetPostUrl' => '/Admin/Bbs_Manage/addSetPost',
		'editSetUrl' => '/Admin/Bbs_Manage/editSet',
		'editSetPostUrl' => '/Admin/Bbs_Manage/editSetPost',
		'deleteUrl' => '/Admin/Bbs_Manage/delete',
		'batchUpdateUrl'=>'/Admin/Bbs_Manage/batchUpdate'
	);
	
	public $perpage = 20;

	public $versionName = 'Sdk_Ad_Version';
	    
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		$start_time = $this->getInput('start_time');
		$end_time = $this->getInput('end_time');
		$game_id = intval($this->getInput('game_id'));
		$name     = trim($this->getInput('name'));
		if ($page < 1) $page = 1;
		
		$params = array();
		$search = array();

		
		if($start_time && $end_time){
			$search['start_time'] = $start_time;
			$search['end_time'] = $end_time;
			$params['start_time'] = array('>=',strtotime($start_time));
			$params['end_time'] = array('<=',strtotime($end_time));
		}
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
	
	   if($game_id){
		   	$search['game_id'] = $game_id;
		   	$params['game_id'] = $game_id;
	   }
	   if($name){
	    	$search['name']  = $name;
			$game_params['name']  = array('LIKE',$name);
			$games = Resource_Service_Games::getGamesByGameNames($game_params);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			}else{
				$params['game_id'] = 0;
			}
		}
		
	    list($total, $bbs) = Bbs_Service_Bbs::getList($page, $this->perpage, $params);
	     $game_info = array(); 
	    foreach ($bbs as $key=>$val){
	    	$info = Resource_Service_Games::getGameAllInfo(array('id'=>$val['game_id']),true);
	    	//if(!$info) continue;
	    	$game_info[$info['id']]=array('id'=>$info['id'],
	    			                      'appid'=>$info['appid'],
	    								  'name'=>$info['name'],
	    			                      'img'=>$info['img'],
	    			                         );
	    }
		$this->assign('search', $search);
		$this->assign('game_info', $game_info);
		$this->assign('bbs', $bbs);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign("total", $total);
		$this->assign("show_type_name", $this->show_type_name);
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addGameAction() {
		
		$page = $this->getInput('page');
		if($page < 1) $page = 1;
		$info = $this->getInput(array('game_id','name','start_time','end_time'));
	    $params =  $search = array();
	
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
	
		$params['status'] = 1;
		if ($info['name']) {
			$search['name'] = $info['name'];
			$params['name'] = array('LIKE',$info['name']);
		}
		
		if ($info['game_id']) {
			$search['game_id'] = $info['game_id'];
			$params['id'] = $info['game_id'];
		}
		
		if ($info['start_time']  && $info['end_time'] ) {
			$search['start_time'] = $info['start_time'];
			$search['end_time']   = $info['end_time'];
			$params['online_time'] = array(array('>=',strtotime($info['start_time'])),array('<=',strtotime($info['end_time'])));
		}

		list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params, array('id'=>'DESC'));
		if($games){
			foreach($games as $k=>$v){
				$gameInfo[] = Resource_Service_Games::getGameAllInfo(array('id'=>$v['id']));
			}
			
		}
		//查询已经添加的游戏
		$gameIds = array();
		list(,$bbsGames) = Bbs_Service_Bbs::getAll();
		if($bbsGames){
			$bbsGames = Common::resetKey($bbsGames, 'game_id');
			$gameIds = array_keys($bbsGames);
		}

		$url = $this->actions['addGameUrl'].'/?' . http_build_query( $search ) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('games', $gameInfo);
		$this->assign('gameIds', $gameIds);
		$this->assign('search', $search);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addGamePostAction(){
		
		$game_id = $this->getInput('game_id');
		if(!$game_id)$this->output(-1, '操作非法.');
		$data['game_id'] = $game_id;
		$ret = Bbs_Service_Bbs::getBy($data);
		if($ret) $this->output(-1, '此游戏已经添加.');
		$this->output(0, '添加成功.',array('game_id'=>$game_id));
		
	}
	
	public function addSetAction(){
		$game_id = $this->getInput('game_id');
		if(!$game_id)$this->output(-1, '操作非法.');
	    //游戏信息
		$game_info = Resource_Service_Games::getGameAllInfo(array('id'=>$game_id),true);
		
		$this->assign('game_id', $game_id);
		$this->assign('game_info', $game_info);
	}
	
	public function addSetPostAction(){
		$info = $this->getInput(array('url','status','start_time','end_time','game_id'));
		$effect = $this->getInput('effect');
		$invalid = $this->getInput('invalid');
		if($effect){
			$info['start_time'] = date('Y-m-d H:i:s');
		}
		if($invalid){
			$info['end_time'] = '2030-12-31 23:59:59';
		}
		$data = $this->_cookData($info);
		$ret = Bbs_Service_Bbs::add($data);
		if(!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
		
	}
	
	
	private function _cookData($info) {
		if(strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if(!$info['start_time']) $this->output(-1, '生效时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '失效时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		//if($info['start_time'] <= Common::getTime()) $this->output(-1, '不能小于当前时间.');
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		return $info;
	}
    
	/**
	 *
	 * Enter description here ...
	 */
	public function editSetAction(){
		$id = $this->getInput('id');
		if(!$id)$this->output(-1, '操作非法.');
		$info = Bbs_Service_Bbs::getByID($id);
		$game_info = Resource_Service_Games::getGameAllInfo(array('id'=>$info['game_id']),true);
		$this->assign('info', $info);
		$this->assign('game_info', $game_info);
	}
	
	public function editSetPostAction(){
		$id = $this->getInput('id');
		if(!$id)$this->output(-1, '操作非法.');
		$info = $this->getInput(array('url','status','start_time','end_time'));
		$effect = $this->getInput('effect');
		$invalid = $this->getInput('invalid');
		
		if($effect){
			$info['start_time'] = date('Y-m-d H:i:s');
		}
		if($invalid){
			$info['end_time'] = '2030-12-31 23:59:59';
		}
		$data = $this->_cookData($info);
	
		$ret = Bbs_Service_Bbs::updateByID($data, $id);
		if(!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	
	}
	
	/**
	 *
	 * 批量操作
	 */
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$data['status'] = 1 ;
			foreach ($info['ids'] as $key=>$val){
				$ret = Bbs_Service_Bbs::updateByID($data, $val);
			}
		}elseif($info['action'] =='close'){
			$data['status'] = 0;
			foreach ($info['ids'] as $key=>$val){
				$ret = Bbs_Service_Bbs::updateByID($data, $val);
			}
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}


	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Bbs_Service_Bbs::getByID($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Bbs_Service_Bbs::deleteByID($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


  
}
