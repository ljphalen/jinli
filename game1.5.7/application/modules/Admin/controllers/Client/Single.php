<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_SingleController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Single/index',
		'addCtUrl' => '/Admin/Client_Single/addCt',
		'deleteUrl' => '/Admin/Client_Single/delete',
		'batchUpdateUrl'=>'/Admin/Client_Single/batchUpdate'
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1, 'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		//开始搜索
		$search = $this->getInput(array('name', 'category_id'));
		$params = $gameParams = $categoryParams = $info =  array();
		$params = array('ctype'=>1, 'game_status'=>1);
		$orderBy = array('sort'=>'DESC', 'game_id'=>'DESC');
		if($search['name']) {
			//游戏名称检索
			$gameParams['status'] = 1;
			$gameParams['name'] = array('LIKE', $search['name']);
			$gameData = Resource_Service_Games::getsBy($gameParams);
			if($gameData){
				$gameData = Common::resetKey($gameData, 'id');
				$gameDataIds = array_keys($gameData);
			}else{
				$gameDataIds = array(0);
			}
			$params['game_id'] = array('IN', $gameDataIds);
		}
		
		if($search['category_id']){
			//游戏一级分类检索
			$categoryParams = array('parent_id' => $search['category_id'], 'status' => 1, 'game_status' => 1);
			if($gameDataIds){
				$categoryParams['game_id'] = array('IN', $gameDataIds);
			}
			$categoryGame = Resource_Service_GameCategory::getsBy($categoryParams);
			if($categoryGame){
				$categoryGame = Common::resetKey($categoryGame, 'game_id');
				$categoryGameIds = array_keys($categoryGame);
			}else{
				$categoryGameIds =array(0);
			}
			$params['game_id'] = array('IN', $categoryGameIds);
		}

		list($total, $games) = Client_Service_Channel::getList($page, $this->perpage, $params, $orderBy);
		if($games){
			foreach($games as $value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
				if($game){
				$value['gameName'] = $game['name'];
				$value['gameCategory'] = $game['category_title'];
				$value['gameIcon'] = $game['img'];
				$value['gameSize'] = $game['size'];
				$value['gameVersion'] = $game['version'];
				$value['updateTime'] = $game['update_time'];
				$info[] = $value;
				}
			}
		}

		$this->assign('total', $total);
		$this->assign('games', $info);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	//批量操作
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Channel::batchAddByChannel($info['ids'],1);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Channel::batchDeleteByChannel($info['ids'],1);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Channel::batchSortByChannel($info['sort'],1);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1,'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		//单机游戏数据
		$singleGame = Client_Service_Channel::getChannelByGtype(1);
		$singleGame = Common::resetKey($singleGame, 'game_id');
		$singleGameIds = array_keys($singleGame);
		$this->assign('singleGameIds', $singleGameIds);
		
		//开始搜索
		$search = $this->getInput(array('name', 'category_id'));
		$params = $gameParams = $categoryParams = $info =  array();
		$params = array('status'=>1);
		if($search['name']) {
			//游戏名称检索
			$gameParams['status'] = 1;
			$gameParams['name'] = array('LIKE', $search['name']);
			$gameData = Resource_Service_Games::getsBy($gameParams);
			if($gameData){
				$gameData = Common::resetKey($gameData, 'id');
				$gameDataIds = array_keys($gameData);
			}else{
				$gameDataIds = array(0);
			}
			$params['id'] = array('IN', $gameDataIds);
		}
		
		if($search['category_id']){
			//游戏一级分类检索
			$categoryParams = array('parent_id' => $search['category_id'], 'status' => 1, 'game_status' => 1);
			if($gameDataIds){
				$categoryParams['game_id'] = array('IN', $gameDataIds);
			}
			$categoryGame = Resource_Service_GameCategory::getsBy($categoryParams);
			if($categoryGame){
				$categoryGame = Common::resetKey($categoryGame, 'game_id');
				$categoryGameIds = array_keys($categoryGame);
			}else{
				$categoryGameIds =array(0);
			}
			$params['id'] = array('IN', $categoryGameIds);
		}
		
		list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params);
		foreach($games as $value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
				if($game){
					$info[] = $game;
				}
		}
		$this->assign('total', $total);
		$this->assign('games', $info);
		$this->assign('search', $search);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_Channel::getChannel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Channel::deleteChannel($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
