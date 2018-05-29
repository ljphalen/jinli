<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_TasteController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl'=>'/Admin/Client_Taste/index',
		'editPostUrl'=>'/Admin/Client_Taste/edit_post',
		'addUrl' => '/Admin/Client_Taste/add',
		'addPostUrl' => '/Admin/Client_Taste/add_post',
		'editCtUrl' => '/Admin/Client_Taste/editCt',
		'editUrl'=>'/Admin/Client_Taste/edit',
		'addCtUrl' => '/Admin/Client_Taste/addCt',
		'uploadUrl' => '/Admin/Client_Taste/upload',
		'deleteUrl' => '/Admin/Client_Taste/delete',
		'checkUrl' => '/Admin/Client_Taste/check',
		'uploadPostUrl' => '/Admin/Client_Taste/upload_post',
		'singleUrl'=>'/Admin/Client_Taste/single',
		'batchUpdateUrl'=>'/Admin/Client_Taste/batchUpdate',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('name','status', 'start_time','end_time','game_id'));
		$params = $search =  array();
		//恢复没有完成的
		$olds = Client_Service_Taste::getTasteGames(array('start_time'=>0,'recovery_time'=>array('!=',0)));
		foreach($olds as $key=>$value){
			Client_Service_Taste::updateByTastes(array('start_time'=>$value['recovery_time']),array('start_time'=>0,'recovery_time'=>array('!=',0)));
		}
		
		//删除掉没有添加成功的
		Client_Service_Taste::deleteTasteTime(array('start_time'=>0,'recovery_time'=>0));
		
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['start_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time']) $params['start_time'][1] = array('<=', strtotime($s['end_time']));
		
		if ($s['game_id']) {
			$params['game_id'] = $s['game_id'];
		}
		
	    $params['game_status'] = 1;
		$taste_games = Client_Service_Taste::getTasteGames($params);
		
		if($taste_games){
			$taste_games = Common::resetKey($taste_games, 'game_id');
			$this->assign('taste_games', $taste_games);
			$taste_ids = array_unique(array_keys($taste_games));
			
			if($taste_ids){
				//找出索引表中游戏id
				$search['id'] = array('IN',$taste_ids);
			} else {
				$search['id'] = 0;
			}
			if($s['name']){
				$search['name'] = array('LIKE',$s['name']);
			
			}
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage,$search);
		}
		
		
		$this->assign('total', $total);
		$this->assign('games', $games);
		$this->assign('s', $s);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	public function editAction() {
		$id = intval($this->getInput('id'));
		$t_info = Client_Service_Taste::getTaste($id);
		$info = Resource_Service_Games::getGameAllInfo(array('id'=>$t_info['game_id']));
		$this->assign('info', $info);
		$this->assign('t_info', $t_info);
		$this->assign('id', $id);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$info = $this->getInput(array('id','status','start_time'));
		if(!$info['start_time']) $this->output(-1, '生效时间不能为空.');
		$t_info = Client_Service_Taste::getTaste($info['id']);
		
		if(strtotime($info['start_time']) < Common::getTime() && $t_info['start_time'] != strtotime($info['start_time'])) $this->output(-1, '生效时间不能小于当前时间.');
		$ret = Client_Service_Taste::updateTaste(array('status'=>$info['status'],'start_time'=>strtotime($info['start_time'])), $info['id']);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	//批量操作
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Taste::batchAddByTaste($info['ids']);
		} else if($info['action'] =='open'){
			$ret = Client_Service_Taste::updateTasteStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Taste::updateTasteStatus($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	public function checkAction() {
		$tmp = $game_info = $game_ids = $temp = array();
		$str = '';
		//查找当前已经存在的
		$params['start_time'] = 0;
		$params['recovery_time'] = array('!=',0);
		$current_games = Client_Service_Taste::getTasteGames($params);
		$temp = array();
		if($current_games){
			foreach($current_games as $k=>$v){
				$current_name = array();
				$current_name = Resource_Service_Games::getGameAllInfo(array('id'=>$v['game_id']));
				$temp[] = $current_name['name'];
			}
		}
		unset($params);
		$params['start_time'] = 0;
		$num = Client_Service_Taste::getCountTasteGames($params);
		
		if($temp){
			$str = "你所选中".$num."个内容，其中 ".implode(',',$temp)." 共".count($current_games)." 个游戏已经发布过, 再次发布会取消之前的记录，确定要再次发布吗？";
		}
		$this->output('0', $str);
	}
	
	/*
	public function checkAction() {
		$tmp = $game_info = $game_ids = $temp = array();
		$flag = $exist = 0;
		$ids = $this->getInput('ids');
		$game_ids = explode(',',html_entity_decode($ids));
		foreach($game_ids as $k=>$v){
			$rets = Client_Service_Taste::getTasteGames(array('game_id'=>$v));
			if($rets && $v) {
				$game_info = Resource_Service_Games::getResourceGames($v);
				$tmp[] = $game_info['name'];
			} else if(!$rets && $v){
				   $temp[] = $v;
			}
		}
		if($tmp){
			$num = count($temp) + count($tmp);
			$str = "你所选中".$num."个内容，其中 ".implode(',',$tmp)." 等".count($tmp)." 个游戏已经发布过, 再次发布会取消之前的记录，确定要再次发布吗？";
		}
		if($temp){
			$exist = implode(',',$temp);
		}
		$this->_output('0', $str, $ids, $exist);
	}*/
	
	public function singleAction() {
		$tmp = $game_info = $game_ids = $exist_ids = array();
		$ids = $this->getInput('ids');
		$exist = $this->getInput('exist');
		
		$game_ids = explode(',',html_entity_decode($ids));
		$exist_ids = explode(',',html_entity_decode($exist));
		if($game_ids && ($exist_ids && $exist)) {
			$game_ids = array_merge($game_ids,$exist_ids);
		} else if($game_ids && (!$exist_ids && empty($exist))) {
			$game_ids = $game_ids;
		} else if(!$game_ids && ($exist_ids && $exist)) {
			$game_ids = $exist_ids;
		}
		$game_ids = array_unique($game_ids);
		$ret = Client_Service_Taste::batchAddByTaste($game_ids);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	private function _output($code, $msg = '', $data = array(), $exist = array()) {
		header("Content-type:text/json");
		exit(json_encode(array(
				'success' => $code == 0 ? true : false ,
				'msg' => $msg,
				'data' => $data,
				'exist' => $exist
		)));
	}
	
	/**
	 *
	 * edit games
	 */
	public function addCtAction() {
	}
	
	/**
	 *
	 * add games
	 */
	public function addAction() {
		
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		$s = $this->getInput(array('name','id'));
		$params = $search = $info = $taste_games = $current_games  = array();

		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);

		$search['status'] = 1;
  	    if ($s['name']) $search['name'] = array('LIKE',$s['name']);
	    if ($s['id']) $search['id'] = $s['id'];
		list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $search, array('id'=>'DESC'));
		unset($search);
		if($games){
			foreach($games as $k=>$v){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$v['id']));
			}
			$oline_versions = common::resetkey($info, 'id');
				
			$category_ids = array_keys($oline_versions);
			//游戏分类
			$tmp = $category_games = array();
			$category_games = Resource_Service_Games::getIdxResourceCategoryGames(array('game_id'=>array('IN', $category_ids)));
			foreach($category_games as $key=>$value){
				$tmp[$value['game_id']][] = $value['category_id'];
			}
			
			$category_title = array();
			foreach($tmp as $key=>$val){
				foreach($val as $key1=>$val1){
					if($categorys_id[$val1]['title']){
						$category_title[$key][] = $categorys_id[$val1]['title'];
					}
				}
			}        	
		}
		
		//当前添加的
		unset($params);
		$params['start_time'] = 0;
		$current_games = Client_Service_Taste::getTasteGames($params);
		if($current_games){
			$temp = array();
			$current_games = Common::resetKey($current_games, 'game_id');
			$this->assign('current_games', $current_games);
			foreach($current_games as $k=>$v){
				$current_name = array();
				$current_name = Resource_Service_Games::getGameAllInfo(array('id'=>$v['game_id']));
				$temp[$v['game_id']] = $current_name['name'];
			}
		}
		unset($params);
		 
		//之前添加的
		//$params['status'] = 1;
		$params['game_status'] = 1;
		$taste_games = Client_Service_Taste::getTasteGames($params);
		 
		if($taste_games){
			$taste_games = Common::resetKey($taste_games, 'game_id');
			$taste_ids = array_unique(array_keys($taste_games));
			$this->assign('taste_ids', $taste_ids);
		}
		
		$this->assign('total', $total);
		$this->assign('count_tastes', $count_tastes);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('category_title', $category_title);
		$this->assign('games', $games);
		$this->assign('s', $s);
		$this->assign('current_name', $temp);
		$url = $this->actions['addUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('effective_status', 'start_time'));
		if($info['effective_status']) {   //稍后生效
			$start_time = $this->_cookData($info['start_time']);
		} else {                          //立即生效  
			$start_time  = Common::getTime();
		}
		$ret = Client_Service_Taste::updateTasteTime($start_time, 0);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	private function _cookData($start_time) {
		$start_time = strtotime($start_time);
		if(!$start_time) $this->output(-1, '生效时间不能为空.');
		if($start_time < Common::getTime()) $this->output(-1, '生效时间不能小于当前时间.');
		return $start_time;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_Taste::getTaste($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Taste::deleteTaste($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
