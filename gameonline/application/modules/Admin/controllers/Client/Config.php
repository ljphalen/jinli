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
	
	public $appCacheName = array("APPC_Channel_Category_index","APPC_Client_Category_index","APPC_Kingstone_Category_index");
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
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('name','id'));
	
		$search = array();
		if ($s['name']) $search['name'] = array('LIKE',$s['name']);
	
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
	
		list(, $guess_games) = Client_Service_Game::getGuessByGame(array('status'=>1,'game_status'=>1));
		$guess_games = Common::resetKey($guess_games, 'game_id');
		$this->assign('guess_games', $guess_games);
		$guess_games_ids = array_unique(array_keys($guess_games));
	
		if (count($guess_games_ids)) {
			if($s['id']){
				$resource_games = array_intersect($guess_games_ids,array($s['id']));
				if($resource_games){
					$search['id'] = array('IN',$resource_games);
				} else {
					$search['create_time'] = 0;
				}
			} else {
				$search['id'] =  array('IN',$guess_games_ids);
			}
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $search);
			$games = Common::resetKey($games, "id");
		}
	
		$this->cookieParams();
		$this->assign('total', $total);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('games', $games);
		$this->assign('s', $s);
		$url = $this->actions['editCtUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		$s = $this->getInput(array('name','status','id', 'category_id'));
		$params = $search = array();
	
		if ($s['name']) $params['name'] = $s['name'];
		if ($s['status']) $params['status'] = $s['status'] - 1;

	
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
	
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		//获取猜你喜欢默认索引表游戏
		list(, $idx_games) = Client_Service_Game::getGuessByGame(array('status'=>1,'game_status'=>1));
		$idx_games = Common::resetKey($idx_games, 'game_id');
		$idx_games = array_keys($idx_games);
	
		//获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			$this->assign('resource_game_ids', $resource_game_ids);
			
			if ($s['category_id']) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$s['category_id'],'game_status'=>1));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
				if($ids){
					if($s['id']){
						$resource_games = array_intersect($ids,array($s['id']));
						if($resource_games){
							$params['id'] = array('IN',$resource_games);
						} else {
							$params['id'] = 0;
						}
					} else {
						$params['id'] = array('IN',$ids);
					}
				} else {
					$params['id'] = 0;
				}
			}  else {
				if($s['id']){
					 $resource_games = array_intersect($resource_game_ids,array($s['id']));
				     if($resource_games){
							$params['id'] = array('IN',$resource_games);
					 } else {
							$params['id'] = 0;
					 }
				} else {
					$params['id'] = array('IN',$resource_game_ids);
				}
			}

		}
		list($total, $games) = Resource_Service_Games::adminSearch($page, $this->perpage, $params, array('id'=>'DESC'));
		$this->assign('total', $total);
		$this->cookieParams();
		$this->assign('s', $s);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('idx_games', $idx_games);
		$this->assign('games', $games);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
}
