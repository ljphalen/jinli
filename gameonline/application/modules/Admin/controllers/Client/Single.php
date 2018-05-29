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
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		$s  = $this->getInput(array('name', 'category_id'));
		
		$params = array();
		$single_games = array();
		if($s['name']) {
			$params['name'] = array('LIKE', $s['name']);
		}
		
		//获取单机游戏
		$single_games = Client_Service_Channel::getChannelByGtype(1);
		if($single_games){
			$idx_games = Common::resetKey($single_games, 'game_id');
			$this->assign('ad_games', $idx_games);
			$idx_games = array_keys($idx_games);
			
			//游戏分类
			$tmp = $category_games = $client_games = array();
			$category_games = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $idx_games)));
			foreach($category_games as $key=>$value){
				$tmp[$value['game_id']][] = $value['category_id'];
			}
			$category_title = array();
			foreach($tmp as $key=>$val){
				foreach($val as $key1=>$val1){
					$category_title[$key][] = $categorys_id[$val1]['title'];
				}
					
			}
			
			//获取本地所有游戏
			if ($s['category_id']) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $idx_games), 'status'=>1, 'category_id'=>$s['category_id'],'game_status'=>1));
				$tmp = Common::resetKey($game_ids, 'game_id');
				$tmp = array_keys($tmp);
				if($game_ids){
					$params['id'] = array('IN',$tmp);
				} else {
					$params['create_time'] = 0;
				}
					
			} else {
				$params['id'] = array('IN', $idx_games);
			}
				
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $params);
			foreach($games as $key=>$value){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			}
			$oline_versions = common::resetkey($info, 'id');
		}
		
		$this->assign('category_title', $category_title);
		$this->assign('s', $s);
		$this->assign('games', $games);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('total', $total);
		$this->assign('ad_ptype', $this->ad_ptype);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
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
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
	    $s  = $this->getInput(array('name', 'category_id'));
		
		$params = array();
		$single_games = array();
		if($s['name']) {
			$params['name'] = array('LIKE',$s['name']);
		}
		$search = array();
		$single_games = array();
		
		
		//获取单机游戏
		$single_games = Client_Service_Channel::getChannelByGtype(1);
		$idx_games = Common::resetKey($single_games, 'game_id');
		$idx_games = array_keys($idx_games);
		$this->assign('idx_games', $idx_games);

	    $client_games = Resource_Service_Games::getsBy(array('status'=>1));
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			if($resource_game_ids){
				$params['id'] = array('IN',$resource_game_ids);
			} else {
				$params['id'] = 0;
			}

			if ($s['category_id']) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$s['category_id'],'game_status'=>1));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
				if(!$ids){
					$params['id'] = 0;
				} else {
					$params['id'] = array('IN',$ids);
				}	
			}
			
		    //游戏分类
			$tmp = $category_games = $client_games = array();
			$category_games = Resource_Service_Games::getIdxResourceCategorys(array('game_id'=>array('IN', $params['id'])));
			foreach($category_games as $key=>$value){
				$tmp[$value['game_id']][] = $value['category_id'];
			}
			
			$category_title = array();
			foreach($tmp as $key=>$val){
				foreach($val as $key1=>$val1){
					$category_title[$key][] = $categorys_id[$val1]['title'];
				}
			
			}
			
			list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params);
			foreach($games as $key=>$value){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			}
			$oline_versions = common::resetkey($info, 'id');
		}
		
		$this->assign('category_title', $category_title);
	    $this->assign('oline_versions', $oline_versions);
		$this->assign('s', $s);
		$this->assign('games', $games);
		$this->assign('total', $total);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($s) . '&';
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
