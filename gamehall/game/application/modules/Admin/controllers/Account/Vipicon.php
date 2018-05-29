<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * vip图标
 * Account_VipiconController
 * @author wupeng
 */
class Account_VipiconController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Account_Vipicon/index',
		'addUrl' => '/Admin/Account_Vipicon/add',
		'editUrl' => '/Admin/Account_Vipicon/edit',
		'addPostUrl' => '/Admin/Account_Vipicon/addPost',
		'editPostUrl' => '/Admin/Account_Vipicon/editPost',
		'deleteUrl' => '/Admin/Account_Vipicon/delete',
		'batchUpdateUrl'=>'/Admin/Account_Vipicon/batchUpdate',
	    'uploadUrl' => '/Admin/Account_Vipicon/upload',
	    'uploadPostUrl' => '/Admin/Account_Vipicon/upload_post',
	);

	public $perpage = 20;

	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$requestData = $this->getInput(array('vip', 'img'));
		$searchParams = $this->getSearchParams($requestData);
		$sortParams = array('vip' => 'DESC');

		list($total, $list) = User_Service_VipIcon::getPageList($page, $this->perpage, $searchParams, $sortParams);

		$this->assign('search', $requestData);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	    $this->assign('vipList', User_Config_Vip::getVipList());
	}

	public function addAction() {
	    $this->assign('vipList', User_Config_Vip::getVipList());
	}

	public function addPostAction() {
		$requestData = $this->getInput(array('vip', 'img'));
		$postData = $this->checkRequestData($requestData);
		$result = User_Service_VipIcon::addVipIcon($postData);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$keys = $this->getInput(array('vip'));
		$info = User_Service_VipIcon::getVipIcon($keys['vip']);
		$this->assign('info', $info);
		$this->assign('vipList', User_Config_Vip::getVipList());
	}

	public function editPostAction() {
		$requestData = $this->getInput(array('vip', 'img'));
		
		$postData = $this->checkRequestData($requestData);
		$editInfo = User_Service_VipIcon::getVipIcon($requestData['vip']);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');
		$updateParams = array_diff_assoc($postData, $editInfo);
		if (count($updateParams) >0) {
			$ret = User_Service_VipIcon::updateVipIcon($updateParams, $requestData['vip']);
			if (!$ret) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$keys = $this->getInput(array('vip'));
		$info = User_Service_VipIcon::getVipIcon($keys['vip']);
		if (!$info) $this->output(-1, '无法删除');
		$result = User_Service_VipIcon::deleteVipIcon($keys['vip']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function batchUpdateAction() {
		$requestData = $this->getPost(array('action', 'keys'));
		if (!count($requestData['keys'])) $this->output(-1, '没有可操作的项.');
		$keys = $requestData['keys'];

		if($requestData['action'] =='delete'){
			$ret = User_Service_VipIcon::deleteVipIconList($keys);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}

	private function getSearchParams($search) {
	    $searchParams = array();		
		if ($search['vip']) $searchParams['vip'] =  $search['vip'];
		if ($search['img']) $searchParams['img'] = array('like', $search['img']);
	    return $searchParams;
	}

	private function checkRequestData($requestData) {
		if(!isset($requestData['vip'])) $this->output(-1, 'vip等级不能为空.');
		if(!isset($requestData['img'])) $this->output(-1, 'icon不能为空.');
		return $requestData;
	}


	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function upload_postAction() {
	    $ret = Common::upload('img', 'vipicon');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function addVipValueAction() {
	    $uuid = $this->getInput('uuid');
        $value = $this->getInput('expr');
	    if($uuid && $value) {
	        $type = $this->getInput('type');
	        Account_Service_User::addUserVipActivityExpr($uuid, $value);
	        $user = Account_Service_User::getUserInfo(array('uuid' => $uuid));
	        $expr = array();
	        $expr[User_Service_VipExpr::F_ADD_EXPR] = $value;
	        $expr[User_Service_VipExpr::F_CREATE_TIME] = Common::getTime();
	        $expr[User_Service_VipExpr::F_EXPR] = $user['vip_mon_expr'] + $user['vip_act_expr'];
	        $expr[User_Service_VipExpr::F_LOGS] = $type;
	        $expr[User_Service_VipExpr::F_TYPE] = User_Service_VipExpr::TYPE_ACTIVITY;
	        $expr[User_Service_VipExpr::F_UUID] = $uuid;
	        User_Service_VipExpr::addVipExpr($expr);
	        $this->output(0, '添加成功');
	    }
	}
	
}
