<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_GameController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Game/index',
		'addUrl' => '/Admin/Client_Game/add',
		'addPostUrl' => '/Admin/Client_Game/add_post',
		'editUrl' => '/Admin/Client_Game/edit',
		'editPostUrl' => '/Admin/Client_Game/edit_post',
		'deleteUrl' => '/Admin/Client_Game/delete',
		'deleteImgUrl' => '/Admin/Client_Game/delete_img',
		'uploadUrl' => '/Admin/Client_Game/upload',
		'uploadPostUrl' => '/Admin/Client_Game/upload_post',
		'step1Url' => '/Admin/Client_Game/add_step1',
		'step2Url' => '/Admin/Client_Game/add_step2',
		'batchUrl'=>'/Admin/Client_Game/batch',
		'batchAddUrl'=>'/Admin/Client_Game/batchAdd'
	);
	
	public $perpage = 20;
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
	    $page = intval($this->getInput('page'));	
		$s = $this->getInput(array('status', 'name'));
		$params = $search = array();
		
		if ($s['status']) $params['status'] = $search['status'] = $s['status'] - 1; 
		if ($s['name']) $search['name'] = $s['name'];
		
		list($total, $client_games) = Client_Service_Game::getGames($params);
        
        $client_games = Common::resetKey($client_games, "resource_game_id");
        $client_game_ids = array_unique(array_keys($client_games));
        
        if ($total) {
	        $params['id'] = array('IN',$client_game_ids);
			list($total, $resource_games) = Resource_Service_Games::search($page, $this->perpage, $search);
			$resource_games = Common::resetKey($resource_games, "id");
        }
        
        $this->cookieParams();
		$this->assign('search', $s);
		$this->assign('client_games', $client_games);
		$this->assign('resource_games', $resource_games);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s).'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * @param array $data
	 * @return boolean|multitype:
	 */
	private static function _gameData(array $data) {
		$tmp = array();
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			array_push($tmp,$value);
		}
		return $tmp;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = intval($this->getInput('id'));
		$sys_version = Common::getConfig('apiConfig', 'sys_version');
		$resolution = Common::getConfig('apiConfig', 'resolution');
		$price = Common::getConfig('apiConfig', 'price');
		$info = Client_Service_Game::get($id);
		$this->assign('info', $info);
		$resource_games = Resource_Service_Games::getResourceGames($info['resource_game_id']);
		$resource_imgs = Resource_Service_Img::getList(1, 10, array('game_id'=>$info['resource_game_id']));
		$this->assign('resource_games', $resource_games);
		$this->assign('resource_imgs', $resource_imgs[1]);
		//get client game category
		list(, $categorys) = Client_Service_Category::getAllCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$this->assign('subjects', $subjects);
		
		$subject_idxs = Client_Service_Game::getIdxGameClientSubjectBy(array('client_game_id'=>$id));
		
		$tmp = array();
		foreach ($subject_idxs as $key=>$value) {
			$tmp[] = $value['client_subject_id'];
		}
		$this->assign('subject_ids', $tmp);
		$this->assign('sys_version', $sys_version);
		$this->assign('resolution', $resolution);
		$this->assign('price', $price);
	}
	
	//批量添加
// 	function batchAddAction() {
// 		$info = $this->getPost(array('action', 'ids'));
// 		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
// 		sort($info['ids']);
// 		if($info['action'] =='online'){
// 			$ret = Client_Service_Game::batchAddByGame($info['ids'], 1);
// 		}
// 		if (!$ret) $this->output('-1', '操作失败.');
// 		$this->output('0', '操作成功.');
// 	}
	
	
    //批量上线，下线，排序
	function batchAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		
		if($info['action'] =='open'){
			$ret = Client_Service_Game::updateGameStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Game::updateGameStatus($info['ids'], 0);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Game::deleteGame($info['ids']);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Game::sortGameclientByGames($info['ids'], $info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * games list
	 */
	public function add_step1Action() {
		$page = intval($this->getInput('page'));
 
		$name = $this->getInput('keyword');
		$cid = $this->getInput('cid');
		$isadd = $this->getInput('isadd');
		
		$params = array();
		$search = array();
		if($name) {
			$params['name'] = $name;
			$search['keyword'] = $name;
		}
		if ($isadd) $search['isadd'] = $isadd;
		
		//获取库所有游戏
		list(, $resource_games) = Resource_Service_Games::getsBy(array('status'=>1));
		$ids = Common::resetKey($resource_games, 'id');
		$ids = array_keys($ids);
		$params['ids'] = $ids;

		
		if($cid) {
			$params['cid'] = $cid;
			//获取game_ids
			$game_ids = Resource_Service_Games::getResourceGamesByCategorys(array('category_id'=>$cid));
			$game_ids = Common::resetKey($game_ids, 'game_id');
			$cids = array_keys($game_ids);
			$params['ids'] = $cids;
			$search['cid'] = $cid;
			
		}
		if ($page < 1) $page = 1;
		//获取库分类
		list(, $categorys) = Resource_Service_Games::getResourceGamesAttribute(1);
		//获取本地所有游戏
		list(, $client_games) = Client_Service_Game::getAll();
		$client_games = Common::resetKey($client_games, 'resource_game_id');
		$this->assign('client_games', $client_games);
		$resource_game_ids = array_keys($client_games);
		$this->assign('resource_game_ids', $resource_game_ids);
		
		
		//已经添加
		if ($search['isadd'] == 2) {
			if($cid){
			    $params['ids'] = array_intersect($cids, $resource_game_ids);
			} else {
				$params['ids'] = array_intersect($ids, $resource_game_ids);
			}
		}	
		
		
		//未添加
		if ($search['isadd'] == 1){
			if($cid){
				if(array_intersect($cids, $resource_game_ids)){
					$params['ids'] = $cids;
				} else {
					$params['ids'] = array_diff($cids, $resource_game_ids);
				}
			} else {
				$params['ids'] = array_diff($ids, $resource_game_ids);
			}
		}
			
		
		//获取库所有游戏
		if ((($cid && (!array_intersect($cids, $resource_game_ids) or !$ids) or !array_intersect($ids, $resource_game_ids)) && $search['isadd'] == 2) or (($cid && (!array_diff($cids, $resource_game_ids) or !$ids) or !array_diff($ids, $resource_game_ids)) && $search['isadd'] == 1) or ($cid && !$cids)) {
			list($total, $games) = array(0, array());
		} else {
			list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params);
		}
		
		
		$this->assign('games', $games);
		//获取本地所有游戏
		list(, $local_game) = Client_Service_Game::getAll();
		$temp = array();
		foreach($local_game as $key=>$value){
			$temp[] = $value['resource_game_id'];
		}
		$this->assign('keyword', $name);
		$this->assign('cid', $cid);
		$this->assign('isadd', $isadd);
		$this->assign('game_ids', $temp);
		$this->assign('categorys', $categorys);
		$url = $this->actions['step1Url'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * add game page show
	 */
	public function add_step2Action() {
		$id = intval($this->getInput('id'));
		$sys_version = Common::getConfig('apiConfig', 'sys_version');
		$resolution = Common::getConfig('apiConfig', 'resolution');
		$price = Common::getConfig('apiConfig', 'price');
		$resource_games = Resource_Service_Games::getResourceGames($id);
		$resource_imgs = Resource_Service_Img::getList(1, 10, array('game_id'=>$id));
		$this->assign('resource_games', $resource_games);
		$this->assign('resource_imgs', $resource_imgs[1]);
		
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$this->assign('subjects', $subjects);
		
		list(, $categorys) = Client_Service_Category::getAllCategory();
		$this->assign('categorys', $categorys);
		$this->assign('resource_game_id', $id);
		$this->assign('sys_version', $sys_version);
		$this->assign('resolution', $resolution);
		$this->assign('price', $price);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'resource_game_id', 'category_id', 'name','resume','img','status','size','hits','create_time'));
		$info['create_time'] = Common::getTime();
		$subject = $this->getPost('subject');
		$info = $this->_cookData($info);
		$games = Client_Service_Game::getGameInfoByResourceId($info['resource_game_id']);
		//if (!$subject) $this->output('-1', '必须选择专题.');
		if ($games) {
			$ret = Client_Service_Game::updateClientGame($info,$subject,$games['id']);
		} else {
			$ret = Client_Service_Game::addClientGame($info, $subject);
		}
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'resource_game_id', 'category_id', 'name','resume','img','status','size','hits','create_time'));
		//修改的图片
		$info['create_time'] = Common::getTime();
		$subject = $this->getPost('subject');
		$info = $this->_cookData($info);
		//if (!$subject) $this->output('-1', '必须选择专题.');

		$ret = Client_Service_Game::updateClientGame($info,$subject,$info['id']);
		
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '游戏名称不能为空.'); 
		if(!$info['resume']) $this->output(-1, '描述不能为空.');
		if(!$info['category_id']) $this->output(-1, '分类不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Game::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Game::deleteClientGame($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
		$ret = Common::upload('img', 'game');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
