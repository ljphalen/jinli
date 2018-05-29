<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_ConfigController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/Client_Config/index',
		'editPostUrl'=>'/admin/Client_Config/edit_post',
		'guessUrl' => '/Admin/Client_Config/guess',
		'editCtUrl' => '/Admin/Client_Config/editCt',
		'addCtUrl' => '/Admin/Client_Config/addCt',
		'uploadUrl' => '/Admin/Client_Config/upload',
		'uploadPostUrl' => '/Admin/Client_Config/upload_post',
		'batchUpdateUrl'=>'/Admin/Client_Config/batchUpdate',
	);
	
	public $appCacheName = array("APPC_Channel_Category_index","APPC_Client_Category_index");
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'config');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array('game_img','game_caini'));
		$config['game_img'] = $this->getInput('img');
	    foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function manAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function guessAction() {
		$page = intval($this->getInput('page'));
		$hot = $this->getInput('hot');
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		//获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		$client_games = Common::resetKey($client_games, 'id');
		
		$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
		$search = array();
		if($hot == 1){
			$search['hot'] = 1;
			$orderBy = array('online_time'=>'DESC');
		} else if($hot == 2){
			$search['hot'] = 2;
			$orderBy = array('downloads'=>'DESC');
		}
		
		list($total, $result) = Client_Service_Game::geGuesstList($page, $this->perpage,array('status'=>1,'game_status'=>1),$orderBy);
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('client_games', $client_games);
		$this->assign('oline_versions', $oline_versions);
		$url = $this->actions['guessUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	//批量操作
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Game::batchAddByGuess($info['ids']);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Game::deleteGuess($info['ids']);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Game::sortGuessGame($info['ids'], $info['sort']);
		} else if($info['action'] =='open'){
			$ret = Client_Service_Game::updateGameGuess($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Game::updateGameGuess($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		//开始搜索
		$search = $this->getInput(array('name', 'id'));
		$params = $gameParams = $info =  array();

		$params = array('status'=>1, 'game_status'=>1);
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
		
		if($search['id']){
			$params['game_id'] = $search['id'];
		}
		
		list($total, $guessGames) = Client_Service_Game::getGuessByGame($params);
		if($guessGames){
			foreach($guessGames as $value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
				$value['gameName'] = $game['name'];
				$value['gameIcon'] = $game['img'];
				$value['gameSize'] = $game['size'];
				$value['gameVersion'] = $game['version'];
				$info[] = $value;
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
	 * add games
	 */
	public function addCtAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1, 'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		//默认猜你喜欢数据
		list(, $defaultGuess) = Client_Service_Game::getGuessByGame(array('status'=>1,'game_status'=>1));
		$defaultGuess = Common::resetKey($defaultGuess, 'game_id');
		$defaultGuessIds = array_keys($defaultGuess);
		$this->assign('defaultGuessIds', $defaultGuessIds);
		
		//开始搜索
		$search = $this->getInput(array('name', 'category_id', 'id'));
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
		if($search['id']){
			$params['id'] = $search['id'];
		}
		
		list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params);
		if($games){
			foreach($games as $value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
				$info[] = $game;
			}
		}
		$this->assign('total', $total);
		$this->assign('games', $info);
		$this->assign('search', $search);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
}
