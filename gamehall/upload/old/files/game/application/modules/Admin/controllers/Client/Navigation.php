<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 客户端按钮导航
 * Client_NavigationController
 * @author wupeng
 */
class Client_NavigationController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Navigation/index',
		'addUrl' => '/Admin/Client_Navigation/add',
		'editUrl' => '/Admin/Client_Navigation/edit',
		'addPostUrl' => '/Admin/Client_Navigation/addPost',
		'editPostUrl' => '/Admin/Client_Navigation/editPost',
		'deleteUrl' => '/Admin/Client_Navigation/delete',
		'batchUpdateUrl'=>'/Admin/Client_Navigation/batchUpdate',
	    'uploadUrl' => '/Admin/Client_Navigation/upload',
	    'uploadPostUrl' => '/Admin/Client_Navigation/upload_post',
	    
		'infoUrl' => '/Admin/Client_Navigation/info',
	);
	
	private $backUrl = array(
	    Client_Service_Navigation::WEB_GAME => "/Admin/Game_Webgame/index"
	);

	public $perpage = 20;

	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$requestData = $this->getInput(array('id', 'module', 'title', 'view_type', 'icon_url', 'param', 'sort', 'status'));
		$searchParams = $this->getSearchParams($requestData);
		$sortParams = array('module' => 'asc', 'sort' => 'DESC', 'id' => 'DESC');

		list($total, $list) = Client_Service_Navigation::getPageList($page, $this->perpage, $searchParams, $sortParams);

		$this->assign('search', $requestData);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	    $this->assign('module', Client_Service_Navigation::$module);
	    $this->assign('backUrl', $this->backUrl[$requestData['module']]);
	}

	public function addAction() {
	    $this->assign('module', Client_Service_Navigation::$module);
	}

	public function addPostAction() {
		$requestData = $this->getInput(array('module', 'title', 'view_type', 'icon_url', 'param', 'sort', 'status'));
		$postData = $this->checkRequestData($requestData);
		$result = Client_Service_Navigation::addNavigation($postData);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$keys = $this->getInput(array('id'));
		$info = Client_Service_Navigation::getNavigation($keys['id']);
	    $this->assign('module', Client_Service_Navigation::$module);
		$this->assign('info', $info);
	}

	public function editPostAction() {
		$requestData = $this->getInput(array('id', 'module', 'title', 'view_type', 'icon_url', 'param', 'sort', 'status'));
		
		$postData = $this->checkRequestData($requestData);
		$editInfo = Client_Service_Navigation::getNavigation($requestData['id']);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');
		$updateParams = array_diff_assoc($postData, $editInfo);
		if (count($updateParams) >0) {
			$ret = Client_Service_Navigation::updateNavigation($updateParams, $requestData['id']);
			if (!$ret) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$keys = $this->getInput(array('id'));
		$info = Client_Service_Navigation::getNavigation($keys['id']);
		if (!$info) $this->output(-1, '无法删除');
		$result = Client_Service_Navigation::deleteNavigation($keys['id']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function batchUpdateAction() {
		$requestData = $this->getPost(array('action', 'keys'));
		if (!count($requestData['keys'])) $this->output(-1, '没有可操作的项.');
		$keys = $requestData['keys'];

		if($requestData['action'] =='delete'){
			$ret = Client_Service_Navigation::deleteNavigationList($keys);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}

	private function getSearchParams($search) {
	    $searchParams = array();		
		if ($search['id']) $searchParams['id'] =  $search['id'];
		if ($search['module']) $searchParams['module'] =  $search['module'];
		if ($search['title']) $searchParams['title'] = array('like', $search['title']);
		if ($search['view_type']) $searchParams['view_type'] = array('like', $search['view_type']);
		if ($search['icon_url']) $searchParams['icon_url'] = array('like', $search['icon_url']);
		if ($search['param']) $searchParams['param'] = array('like', $search['param']);
		if ($search['sort']) $searchParams['sort'] =  $search['sort'];
		if ($search['status']) $searchParams['status'] =  $search['status'];
	    return $searchParams;
	}

	private function checkRequestData($requestData) {
		if(!isset($requestData['module']) || !$requestData['module']) $this->output(-1, '模块不能为空.');
		if(!isset($requestData['title']) || !$requestData['title']) $this->output(-1, '标题不能为空.');
		if(!isset($requestData['view_type']) || !$requestData['view_type']) $this->output(-1, '显示窗口不能为空.');
// 		if(!isset($requestData['icon_url']) || !$requestData['module']) $this->output(-1, '图标不能为空.');
// 		if(!isset($requestData['param'])) $this->output(-1, '客户端参数不能为空.');
		if(!isset($requestData['sort'])) $this->output(-1, '排序不能为空.');
		if(!isset($requestData['status'])) $this->output(-1, '状态不能为空.');
		$requestData['param'] = html_entity_decode($requestData['param']);
		return $requestData;
	}

	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function upload_postAction() {
	    $ret = Common::upload('img', 'navigation');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function infoAction() {
	}
	
}
