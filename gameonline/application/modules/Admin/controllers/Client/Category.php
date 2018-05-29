<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_CategoryController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Category/index',
		'addUrl' => '/Admin/Client_Category/add',
		'addPostUrl' => '/Admin/Client_Category/add_post',
		'editUrl' => '/Admin/Client_Category/edit',
		'editPostUrl' => '/Admin/Client_Category/edit_post',
		'deleteUrl' => '/Admin/Client_Category/delete',
		'uploadUrl' => '/Admin/Client_Category/upload',
		'uploadPostUrl' => '/Admin/Client_Category/upload_post',
		'uploadImgUrl' => '/Admin/Client_Category/uploadImg',
		'editCtUrl' => '/Admin/Client_Category/editCt',
		'addCtUrl' => '/Admin/Client_Category/addCt',
		'batchUpdateUrl'=>'/Admin/Client_Category/batchUpdate'
	);
	
	public $perpage = 20;
	public $appCacheName = array("APPC_Channel_Category_index","APPC_Client_Category_index","APPC_Kingstone_Category_index");
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$perpage = $this->perpage;
		
		list($total, $result) = Resource_Service_Attribute::getsortList(1, $this->perpage,array('at_type'=>1,'status'=>1));
		
		$this->cookieParams();
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getResourceAttribute(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		$name = $this->getInput('name');
		$hot = $this->getInput('hot');
		
		$search = $parms = array(); 
		$search['id'] = $id;
		if ($name) {
			$parms['name'] = array('LIKE',$name);
			$search['name'] = $name;
		}
		
		$info = Resource_Service_Attribute::getResourceAttribute(intval($id));
		$this->assign('info', $info);
		
		$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
		if($hot == 1){
			$search['hot'] = 1;
			$orderBy = array('online_time'=>'DESC');
		} else if($hot == 2){
			$search['hot'] = 2;
			$orderBy = array('downloads'=>'DESC','game_id'=>'DESC');
		}
		$category_games = Resource_Service_Games::getGameResourceByCategoryIdStatus($id,$orderBy);
		$category_games = Common::resetKey($category_games, 'game_id');
		$resource_game_ids = array_unique(array_keys($category_games));
		
		if (count($resource_game_ids)) {
			$parms['id'] = array('IN',$resource_game_ids);
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $parms);
			$games = Common::resetKey($games, "id");
		}
		
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		//$this->cookieParams();
		$this->assign('total', $total);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('category_games', $category_games);
		$this->assign('games', $games);
		$this->assign('search', $search);
		$this->assign('id', $id);
		$this->assign('total', $total);
		$url = $this->actions['editCtUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));		
	}
	
    //批量上线，下线，排序
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');

		if($info['action'] =='sort'){
			$ret = Resource_Service_Attribute::updateCategorySort($info['sort']);
		} else if($info['action'] =='game_sort'){
			$ret = Resource_Service_Games::updateIdxCategorySort($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Category::getCategory(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Category::getCategory(intval($id));
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('name','status','isadd', 'category_id'));
		$params = $search = array();
		
		if ($s['name']) $search['name'] = $s['name'];$params['name'] = $s['name'];
		if ($s['status']) $search['status'] = $s['status'] - 1;
		if ($s['isadd']) $search['isadd'] = $s['isadd'];
		if ($s['category_id']) $search['category_id'] = $s['category_id'];
		
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 150,array('at_type'=>1));;
		$this->assign('categorys', $categorys);
		//分类信息		
		$info = Client_Service_Category::getCategory(intval($id));
		$this->assign('info', $info);
		
		//获取本地所有游戏
		list(, $client_games) = Client_Service_Game::getCanUseAllGames();
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'resource_game_id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			$this->assign('resource_game_ids', $resource_game_ids);
			$params['ids'] = $resource_game_ids;
			
			if ($s['category_id']) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$s['category_id']));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
				$params['ids'] = $ids;
			}

			
			//获取分类索引表游戏
			list(, $idx_games) = Client_Service_Game::getCategoryGames(array('category_id'=>$id));
			$idx_games = Common::resetKey($idx_games, 'resource_game_id');
			$idx_games = array_keys($idx_games);
			$this->assign('idx_games', $idx_games);
			

			
			//已经添加
			if ($search['isadd'] == 2) {
				if($s['category_id']){
					$params['ids'] = array_intersect(array_intersect($ids, $resource_game_ids),$idx_games);
				} else {
					$params['ids'] = array_intersect($resource_game_ids,$idx_games);
				}
			}
			//未添加
			if ($search['isadd'] == 1) {
				if($s['category_id']){
				  $params['ids'] = array_diff(array_intersect($ids, $resource_game_ids), array_intersect(array_intersect($ids, $resource_game_ids),$idx_games));
				} else {
				  $params['ids'] = array_diff($resource_game_ids,$idx_games);
				}
				
			}
				

		   if ((($s['category_id'] && (!array_intersect(array_intersect($ids, $resource_game_ids),$idx_games) or !$resource_game_ids) or !array_intersect($resource_game_ids,$idx_games)) && $search['isadd'] == 2) or (($s['category_id'] && (!array_diff(array_intersect($ids, $resource_game_ids), array_intersect(array_intersect($ids, $resource_game_ids),$idx_games)) or !$resource_game_ids) or !array_diff($resource_game_ids,$idx_games)) && $search['isadd'] == 1) or ($s['category_id'] && !$ids)) {
				list($total, $games) = array(0, array());
			} else {
				list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params);
			}
		}
		$this->assign('total', $total);
		$this->cookieParams();
		$this->assign('games', $games);
		$this->assign('search', $search);
		$url = $this->actions['addCtUrl'].'/?id='.$id.'&' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'img', 'status', 'hits'));
		$info = $this->_cookData($info);
		$result = Client_Service_Category::addCategory($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'img', 'status', 'hits'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Category::updateCategory($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '游戏分类名称不能为空.');
		if(!$info['img']) $this->output(-1, '分类图片不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Category::getCategory($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Category::deleteCategory($id);
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
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'category');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'category');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
}
