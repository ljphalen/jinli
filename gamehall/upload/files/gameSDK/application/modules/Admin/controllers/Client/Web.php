<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_WebController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Web/index',
		'addCtUrl' => '/Admin/Client_Web/addCt',
		'deleteUrl' => '/Admin/Client_Web/delete',
		'batchUpdateUrl'=>'/Admin/Client_Web/batchUpdate',
		'columnListUrl'=>'/Admin/Client_Web/columnList',
		'addColumnUrl'=>'/Admin/Client_Web/addColumn',
		'editColumnUrl'	=>'/Admin/Client_Web/editColumn',
		'delColumnUrl'=>'/Admin/Client_Web/delColumn',
		'addColumnPostUrl'=>'/Admin/Client_Web/addColumn_Post',
		'editColumnPostUrl'	=>'/Admin/Client_Web/editColumn_Post',
		'batchUpdateColumnUrl'=>'/Admin/Client_Web/batchUpdateColumn',
	);
	
	public $perpage = 20;
	//网游频道标识
	const CHANNEL_KEY = 'ONLINE_GAME';
	
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
		$params = array('ctype'=>2, 'game_status'=>1, 'game_status'=>1);
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
			$ret = Client_Service_Channel::batchAddByChannel($info['ids'],2);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Channel::batchDeleteByChannel($info['ids'],2);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Channel::batchSortByChannel($info['sort'],2);
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
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1, 'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		//网游游戏数据
		$webGame = Client_Service_Channel::getChannelByGtype(2);
		$webGame = Common::resetKey($webGame, 'game_id');
		$webGameIds = array_keys($webGame);
		$this->assign('webGameIds', $webGameIds);
		
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
	
	/**
	 * 网游频道栏目列表
	 */
	public function columnListAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$params = $this->getInput(array('name', 'status'));
		$search = array();
		$search['ckey'] = self::CHANNEL_KEY;
		if($params['name'])	$search['name'] = array('LIKE', $params['name']);
		if($params['status']) $search['status'] = $params['status']-1;
		
		list($total, $columns) = Client_Service_ChannelColumn::getList($page,$this->perpage,$search);
		
		$this->assign('search', $params);
		$this->assign('total', $total);
		$this->assign('columns', $columns);
		$url = $this->actions['channelUrl'] . '/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 添加网游频道栏目
	 */	
	public function addColumnAction(){
	}
	
	/**
	 * 编辑网游频道栏目
	 */
	public function editColumnAction(){
		$id = $this->getInput('id');
		$data = Client_Service_ChannelColumn::getColumn($id);
		$this->assign('info', $data);
	}
	
	/**
	 * 删除网游频道栏目
	 */
	public function delColumnAction() {
		$id = intval($this->getInput('id'));
		$result = Client_Service_ChannelColumn::delColumn($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	public function addColumn_PostAction(){
		$info = $this->getPost(array('sort', 'name', 'link', 'start_time', 'status'));
		$info = $this->_cookData($info);
		$info['ckey'] = self::CHANNEL_KEY;
	
		$result = Client_Service_ChannelColumn::addColumn($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	public function editColumn_PostAction(){
		$info = $this->getPost(array('id', 'ckey', 'sort', 'name', 'link', 'start_time', 'status'));
		$info = $this->_cookData($info);
		$ret =  Client_Service_ChannelColumn::updateColumn($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
	
	public function batchUpdateColumnAction(){
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) $this->output(-1, '没有可操作的项');
		foreach ($info['ids'] as $value) {
			$result = Client_Service_ChannelColumn::updateColumnBy(array('sort'=>$info['sort'][$value]), array('id'=>$value));
			if (!$result) $this->output(-1, '操作失败');
		}
		
		$this->output(0, '操作成功.');
	}
	
	private function _cookData($info){
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		if(!$info['link']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if(!$info['start_time']) $this->output(-1, '生效时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		return $info;
	}
	
}
