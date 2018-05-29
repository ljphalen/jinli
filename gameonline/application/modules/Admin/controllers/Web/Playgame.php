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
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		$name = $this->getInput('name');
		$category_id = intval($this->getInput('category_id'));
		$params = array();
		$search = array();
		if($name) {
			$search['name'] = $name;
			$params['name'] = array('LIKE', $name);
		}
		if($category_id) {
			$search['category_id'] = $category_id;
		}		
		//所有首页推荐广告游戏id
		list(, $idx_games) = Web_Service_Ad::getAdGames(array('ad_type'=>$this->ad_type));	
		$idx_games = Common::resetKey($idx_games, 'link');
		$this->assign('ad_games', $idx_games);
		$idx_games = array_keys($idx_games);
		
		//获取广告游戏的分类信息
		if($idx_games){
			$tmp = $category_games = $client_games = array();
			$adgame_categorys = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $idx_games)));//getIdxGameResourceCategorys();
			
			foreach($adgame_categorys as $key=>$value){
				$tmp[$value['game_id']][] = $value['category_id'];
			}
			$category_title = array();
			foreach($tmp as $key=>$val){
				foreach($val as $key1=>$val1){
					$category_title[$key][] = $categorys_id[$val1]['title'];
				}
			
			}
			
			//获取本地所有游戏
			if ($category_id) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $idx_games), 'status'=>1, 'category_id'=>$category_id,'game_status'=>1));
				$tmp = Common::resetKey($game_ids, 'game_id');
				$tmp = array_keys($tmp);
				if($game_ids){
					$params['id'] = array('IN',$tmp);
				} else {
					$params['create_time'] = 0;
				}
			
			} else {
				$params['id'] = array('IN',$idx_games);
			}
			
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $params);
			foreach($games as $key=>$value){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			}
			$oline_versions = common::resetkey($info, 'id');
		}
		

	    $this->assign('subjects', $subjects);
		$this->assign('ad_type', $this->ad_type);
		$this->assign('category_title', $category_title);
		$this->assign('search', $search);
		$this->assign('games', $games);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('total', $total);
		$this->assign('ad_ptype', $this->ad_ptype);
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
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
		
		$name = $this->getInput('name');
		$category_id = intval($this->getInput('category_id'));
		$params = array();
		$search = array();
		if($name) {
			$search['name'] = $name;
			$params['name'] = array('LIKE',$name);
		}
		if($category_id) {
			$search['category_id'] = $category_id;
		}
		$search['ad_type'] = $this->ad_type;
		
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		//获取广告游戏
		list(, $idx_games) = Web_Service_Ad::getAdGames(array('ad_type'=>$this->ad_type));
		$idx_games = Common::resetKey($idx_games, 'link');
		$idx_games = array_keys($idx_games);
		$this->assign('idx_games', $idx_games);
		
	    //获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			$this->assign('resource_game_ids', $resource_game_ids);
			if($resource_game_ids){
				$params['id'] = array('IN',$resource_game_ids);
			} else {
				$params['create_time'] = 0;
			}
			
			if ($category_id) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$category_id,'game_status'=>1));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
				if(!$ids){
					$params['create_time'] = 0;
				} else {
					$params['id'] = array('IN',$ids);
				}	
			}
			list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params);
		}

	    $this->assign('subjects', $subjects);
	    $this->assign('oline_versions', $oline_versions);
		$this->assign('ad_type', $this->ad_type);
		$this->assign('search', $search);
		$this->assign('games', $games);
		$this->assign('total', $total);
		$this->assign('ad_ptype', $this->ad_ptype);
		$this->assign('ad_type', $this->ad_type);
		$url = $this->actions['addUrl'].'/?' . http_build_query($search) . '&';
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