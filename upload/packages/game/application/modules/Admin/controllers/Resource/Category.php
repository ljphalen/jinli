<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 分类管理控制器
 * @author fanch
 *
 */
class Resource_CategoryController extends Admin_BaseController {
	
	public $actions = array(
			'indexUrl' => '/Admin/Resource_Category/index',
			'addUrl' => '/Admin/Resource_Category/add',
			'addPostUrl' => '/Admin/Resource_Category/addPost',
			'editUrl' => '/Admin/Resource_Category/edit',
			'editPostUrl' => '/Admin/Resource_Category/editPost',
			'subListUrl' => '/Admin/Resource_Category/subList',
			'addSubUrl' => '/Admin/Resource_Category/addSub',
			'addSubPostUrl' => '/Admin/Resource_Category/addSubPost',
			'editSubUrl' => '/Admin/Resource_Category/editSub',
			'editSubPostUrl' => '/Admin/Resource_Category/editSubPost',
			'batchUpdateCategoryUrl'=>'/Admin/Resource_Category/batchUpdateCategory',
			'gameListUrl' => '/Admin/Resource_Category/gameList',
			'batchUpdateGameUrl'=>'/Admin/Resource_Category/batchUpdateGame',
			'deleteUrl' => '/Admin/Resource_Category/delete',
			'uploadUrl' => '/Admin/Resource_Category/upload',
			'uploadPostUrl' => '/Admin/Resource_Category/upload_post',
			'uploadImgUrl' => '/Admin/Resource_Category/uploadImg',
	);
	
	public $perpage = 20;
	public $appCacheName = array("APPC_Channel_Category_index","APPC_Client_Category_index");
	//主分类属性表at_type:取值为1
	public $parentCategoryType = 1;
	//子分类属性表at_type:取值为10
	public $subCategoryType = 10;
	
	/**
	 * 分类管理页面
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		list($total, $result) = Resource_Service_Attribute::getList($page, $this->perpage, array('at_type' => $this->parentCategoryType), array('sort'=>'DESC'));
		$category = array();
		if($result){
			foreach ($result as $value){
				$subcategory = Resource_Service_Attribute::getsBy(array('at_type' => $this->subCategoryType, 'parent_id' => $value['id'], 'status'=> 1), array('sort'=>'DESC'));
				$value['subCategory'] = $subcategory ? $subcategory : array();
				$category[] = $value;
			}
		}
		$this->assign('result', $category);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 添加主分类页面
	 */
	public function addAction(){
	}
	
	/**
	 * 新增主分类数据
	 */
	public function addPostAction(){
		$info = $this->getPost(array('sort', 'title', 'img', 'img2', 'status'));
		$info = $this->cookParentCategoryData($info);
		//顶级分类增加固定字段值
		$info['at_type'] = $this->parentCategoryType;
		$info['parent_id'] = 0 ;
		$result = Resource_Service_Attribute::add($info);
		if (!$result) $this->output(-1, '操作失败');
		//分类版本变化
		$this->_changeRelationData(1);
		//属性缓存变更
		$this->_changeDataNotify();
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * 编辑主分类界面
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getBy(array('id'=>$id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editPostAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'img', 'img2', 'status'));
		$info = $this->cookParentCategoryData($info);
		$ret = Resource_Service_Attribute::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败');
		//更新大分类下的游戏
		$ret = Resource_Service_GameCategory::updateBy(array('status'=>$info['status']), array('parent_id' => $info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		//分类版本变化
		$this->_changeRelationData(1);
		//分类关联数据变化
		$this->_changeRelationData(2, array('status'=>$info['status'], 'id'=>$info['id']));
		//属性缓存变更
		$this->_changeDataNotify();
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 子分类管理页面
	 */
	public function subListAction() {
		$info = $this->getInput(array('page','id'));
		$parentCategory = Resource_Service_Attribute::getBy(array('id'=>$info['id']));
		$page = $info['page'];
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		$params = array('parent_id' => $info['id'], 'at_type'=>$this->subCategoryType);
		list($total, $result) = Resource_Service_Attribute::getList($page, $this->perpage, $params, array('sort'=>'DESC'));
		
		$this->assign('parentCategory', $parentCategory);
		$this->assign('result', $result);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 添加子分类页面
	 */
	public function addSubAction(){
		$parentId = $this->getInput('id');
		$this->assign('parentId', $parentId);
	}
	
	/**
	 * 新增子分类数据
	 */
	public function addSubPostAction(){
		$info = $this->getPost(array('sort', 'title','status','parentId'));
		$info = $this->cookSubCategoryData($info);
		//子分类增加固定字段值
		$info['at_type'] = $this->subCategoryType;
		$info['parent_id'] = $info['parentId'];
		$result = Resource_Service_Attribute::add($info);
		if (!$result) $this->output(-1, '操作失败');
		//分类版本变化
		$this->_changeRelationData(1);
		//属性缓存变更
		$this->_changeDataNotify();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 编辑子分类界面
	 */
	public function editSubAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getBy(array('id'=>$id));
		$this->assign('info', $info);
	}
	
	/**
	 * 子分类编辑提交操作
	 */
	public function editSubPostAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'status', 'parentId'));
		$ret = Resource_Service_Attribute::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败');
		//更新子分类中的游戏
		$ret = Resource_Service_GameCategory::updateBy(array('status'=>$info['status']), array('parent_id' => $info['parentId'], 'category_id'=>$info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		//分类版本变化
		$this->_changeRelationData(1);
		//属性缓存变更
		$this->_changeDataNotify();
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 主分类游戏排序
	 */
	public function gameListAction(){
		$search = $this->getInput(array('id','page','name','sort'));
		//分类基本数据
		$info = Resource_Service_Attribute::getBy(array('id' => $search['id']));
		$subCategory = Resource_Service_Attribute::getsBy(array('parent_id' => $search['id']));
		$this->assign('info', $info);
		$this->assign('subCategory', $subCategory);
		//开始检索
		$games = array();
		list($total, $result) = $this->searchData($search);	
		if($result){
			foreach ($result as $value){
				$game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
				$value['name'] = $game['name'];
				$value['img'] = $game['img'];
				$value['size'] = $game['size'];
				$value['version'] = $game['version'];
				$games[]=$value;
			}
		}
		$this->assign('total', $total);
		$this->assign('games', $games);
		$this->assign('search', $search);
		$url = $this->actions['gameListUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $search['page'], $this->perpage, $url));
	}

    //主分类/子分类批量排序
	public function batchUpdateCategoryAction() {
		$info = $this->getPost(array('ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		$ret = Resource_Service_Attribute::updateCategorySort($info['sort']);
		if (!$ret) $this->output('-1', '操作失败.');
		//分类版本变化
		$this->_changeRelationData(1);
		//属性缓存变更
		$this->_changeDataNotify();
		$this->output('0', '操作成功.');
	}
	
	//主分类/子分类游戏内容批量排序
	public function batchUpdateGameAction(){
		$info = $this->getPost(array('ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		foreach($info['sort'] as $key=>$value) {
			$ret = Resource_Service_GameCategory::updateBy(array('sort'=>$value), array('id'=>$key));
			if (!$ret) $this->output('-1', '操作失败.');
		}
		$this->output('0', '操作成功.');
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
	
	/**
	 * 多条件搜索游戏id处理(3种)
	 */
	private function searchData($search){
		$page = $search['page'] ? $search['page']:1;
		$result = $gids = array();
		$nameFlag = $search['name'] ? '1' : '0';
		$sortFlag = $search['sort'] ? '1' : '0';
		$flag = strval($nameFlag. $sortFlag);
		$params = array();
		$params['parent_id'] = $search['id'];
		$params['game_status'] = 1;
		$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
		switch ($flag){
			case '10':
				//10-仅使用名称
				$gids = $this->searchGame(array('name'=>array('LIKE', trim($search['name']))));
				if(!$gids) return array(0, array());
				$params['game_id'] = array('IN', $gids);
				break;
			case '01':
				//01-仅使用排序检索
				//最新排序
				if($search['sort'] == 1){
					$orderBy = array('online_time'=>'DESC');
				}
				//最热排序
				if($search['sort'] == 2){
					$orderBy = array('downloads'=>'DESC','game_id'=>'DESC');
				}
				//子分类排序
				if(!in_array($search['sort'], array(1, 2))){
					$params['category_id'] = $search['sort'];
					$params['parent_id'] = $search['id'];
					}
				break;
			case '11':
				//11-选择名称+排序
				$gids = $this->searchGame(array('name'=>array('LIKE', trim($search['name']))));
				if(!$gids) return array(0, array());
				$params['game_id'] = array('IN', $gids);
				if($search['sort'] == 1){
					$orderBy = array('online_time'=>'DESC');
				}
				//最热排序
				if($search['sort'] == 2){
					$orderBy = array('downloads'=>'DESC','game_id'=>'DESC');
				}
				//子分类排序
				if(!in_array($search['sort'], array(1, 2))){
					$params['category_id'] = $search['sort'];
					$params['parent_id'] = $search['id'];
				}
				break;
			default:
				//00-搜索全部的
		}
		if(isset($params['category_id'])){
			//子分类检索
			list($total, $result) = Resource_Service_GameCategory::getList($page, $this->perpage, $params, $orderBy);
		}else{
			list($total, $result) = Resource_Service_GameCategory::getListByMainCategory($page, $this->perpage, $params, $orderBy);
		}
		return array($total, $result);
	}
	
	/**
	 * 按条件检索游戏资源数据
	 * @param array $parms
	 */
	private function searchGame($params){
		$ret = array();
		$data = Resource_Service_Games::getsBy($params);
		if($data){
			$data =Common::resetKey($data, 'id');
			$ret = array_unique(array_keys($data));
		}
		return $ret;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private function cookParentCategoryData($info) {
		if(!$info['title']) $this->output(-1, '分类名称不能为空.');
		if(!$info['img']) $this->output(-1, '旧版本分类图片不能为空.');
		if(!$info['img2']) $this->output(-1, '新版本分类图片不能为空.');
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function cookSubCategoryData($info) {
		if(!$info['title']) $this->output(-1, '分类名称不能为空.');
		//子分类数量最多不能超过8个
		$data= Resource_Service_Attribute::getsBy(array('parent_id'=>$info['parentId']));
		if(count($data) >= 8) $this->output(-1, '子分类数量最多不能超过8个.');
		return $info;
	}
	
	/**
	 * 属性发生变更数据版本变化
	 * @param int $type
	 */
	private function _changeRelationData($type, $data = array()){
		if(!$type) return;
		switch ($type){
			case 1: //分类版本变更
				Game_Service_Config::setValue('DATA_VERSION_CATEGORYLIST', time());
				break;
			case 2: //分类状态变化
				//首页的专题列表的间隔图片链接对应的分类也下线
				Client_Service_Ad::updateByAd(array('status'=>$data['status']), array('ad_type'=>10, 'ad_ptype'=>2, 'link'=>$data['id']));
				//首页图片广告对应的专题也下线
				Client_Service_Ad::updateByAd(array('status'=>$data['status']), array('ad_type'=>9, 'ad_ptype'=>2, 'link'=>$data['id']));
				break;
		}
	}
	
	/**
	 * 数据变更通知
	 */
	private function _changeDataNotify(){
		//属性缓存变更通知
		Resource_Service_Attribute::notifyCache(1);
	}
}
