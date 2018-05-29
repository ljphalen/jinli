<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljphalen
 *
 */
class Web_PlaygameController extends Admin_BaseController {
	public $actions = array(
		'listUrl' => '/Admin/Web_Playgame/index',
		'editUrl' => '/Admin/Web_Playgame/edit',
		'addUrl' => '/Admin/Web_Playgame/add',
		'editPostUrl' => '/Admin/Web_Playgame/editPost',
		'deleteUrl' => '/Admin/Web_Playgame/delete',
		'batchUpdateUrl'=>'/Admin/Web_Playgame/batchUpdate'
	);
	
	public $perpage = 20;
	public $ad_type = 2;
	
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
		$params = array('ad_type' => $this->ad_type);
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
			$params['link'] = array('IN', $gameDataIds);
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
				$categoryGameIds = array(0);
			}
			$params['link'] = array('IN', $categoryGameIds);
		}
		
		list($total, $games) =  Web_Service_Ad::getList($page, $this->perpage, $params);
		if($games){
			foreach($games as $value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['link']));
				if($game){
					$value['gameName'] = $game['name'];
					$value['gameCategory'] = $game['category_title'];
					$value['gameIcon'] = $game['img'];
					$value['gameSize'] = $game['size'];
					$value['gameVersion'] = $game['version'];
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
	
	/**
	 * 编辑游戏页面
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Web_Service_Ad::getAd(intval($id));
		$game_info = Resource_Service_Games::getResourceGames($info['link']);
		$this->assign('game_info', $game_info);
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * 	批量操作
	 */
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Web_Service_Ad::addGameAd($info['ids'], $this->ad_type);
		} else if($info['action'] =='delete'){
			$ret = Web_Service_Ad::deleteGameAd($info['ids']);
		} else if($info['action'] =='sort'){
			$ret = Web_Service_Ad::sortGameAd($info['ids'], $info['sort']);
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * add games 展示页面
	 */
	public function addAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1,'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		//已添加的大家玩的数据
		list(, $playGame) = Web_Service_Ad::getAdGames(array('ad_type'=>$this->ad_type));
		$playGame = Common::resetKey($playGame, 'link');
		$playGameIds= array_keys($playGame);
		$this->assign('playGameIds', $playGameIds);
		
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
	public function editPostAction() {
		$info = $this->getPost(array('id', 'sort','start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		$id = $this->getInput('id');
		$ret = Web_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(intval($info['sort']) < 0) $this->output(-1, '排序不能小于零'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Web_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Web_Service_Ad::deleteAd($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}