<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GouuserController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Gouuser/index',
		'editUrl' => '/Admin/Gouuser/edit',
		'editPostUrl' => '/Admin/Gouuser/edit_post',
	);
	
	public $perpage = 20;
	public $status = array(
				1 => '正常',
				2 => '冻结' 
			);
	public $sex = array(
				1 => '女',
				2 => '男' 
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('id', 'username', 'mobile', 'realname','status'));
		
		if ($param['id'] != '') $search['id'] = $param['id'];
		if ($param['username'] != '') $search['username'] = $param['username'];
		if ($param['mobile'] != '') $search['mobile'] = $param['mobile'];
		if ($param['realname'] != '') $search['realname'] = $param['realname'];
		if ($param['status']) $search['status'] = intval($param['status']);
		$perpage = $this->perpage;
		list($total, $users) = Gc_Service_User::getList($page, $perpage, $search);
		
		$this->assign('users', $users);
		$this->assign('status', $this->status);
		$this->assign('param', $search);
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$userInfo = Gc_Service_User::getUser(intval($id));
		
		$coinInfo = Api_Gionee_Pay::getCoin(array('out_uid'=>$userInfo['out_uid']));
		$coinInfo['gold_coin'] = Common::money($coinInfo['gold_coin']);
		$coinInfo['silver_coin'] = Common::money($coinInfo['silver_coin']);
		$coinInfo['freeze_gold_coin'] = Common::money($coinInfo['freeze_gold_coin']);
		$coinInfo['freeze_silver_coin'] = Common::money($coinInfo['freeze_silver_coin']);
		
		$userInfo = array_merge($userInfo, $coinInfo);
		
		//查询用户的收货地址
		$address = Gc_Service_UserAddress::getDefaultAddress($userInfo['id']);
		
		$this->assign('userInfo', $userInfo);
		$this->assign('sex', $this->sex);
		$this->assign('status', $this->status);
		$this->assign('address', $address);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status'));
		$ret = Gc_Service_User::updateUser($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新用户失败');
		$this->output(0, '更新用户成功.'); 		
	}
}