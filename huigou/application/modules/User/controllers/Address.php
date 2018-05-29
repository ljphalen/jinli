<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 收货地址
 * @author tiansh
 *
 */
class AddressController extends Front_BaseController {
    
    public $actions = array(
        'addUrl' => '/user/address/add',
    	'addPostUrl' => '/user/address/add_post',
    	'editUrl' => '/user/address/edit',
    	'editPostUrl' => '/user/address/edit_post',
    	'delUrl' => '/user/address/delete'
    );
    
    public $perpage = 20;
    
    /**
     * 
     * Enter description here ...
     */
    public function editAction() {
    	$gid = $this->getInput('gid');
    	$this->checkRight();
    	$title = "收货人地址修改";
    	$id = $this->getInput('id');
    	$addressInfo = Gc_Service_UserAddress::getUserAddress($id);
    	$this->assign('info', $addressInfo);
    	$this->assign('title', $title);
    	$this->assign('gid', $gid);
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function edit_postAction() {
    	$gid = $this->getInput('gid');
    	$info = $this->getPost(array('id','realname','province','city','country','detail_address','postcode', 'mobile','phone'));
    	$addressInfo = Gc_Service_UserAddress::getUserAddress($info['id']);
    	if ($addressInfo['user_id'] != $this->userInfo['id']) $this->output(-1, '用户信息不能编辑');
    	$info = $this->_cookData($info);
		$result = Gc_Service_UserAddress::updateUserAddress($info, $info['id']);
		if (!$result) $this->output(-1, '修改失败.');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if($gid) {
			$url = $webroot.'/order/detail/?&id='.$gid;
		} else {
			$url = $webroot.'/user/setting/index';
		}
		$this->output(0, '修改成功.', array('type'=>'redirect', 'url'=>$url));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function addAction() {
    	$this->checkRight();
    	$gid = $this->getInput('gid');
    	$title = "添加收货人地址";
    	$this->assign('title', $title);
    	$this->assign('gid', $gid);
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function add_postAction() {
    	$this->checkRight();
    	//现只保留一个收货地址
    	$gid = $this->getInput('gid');
    	$info = $this->getPost(array('realname','province','city','country','detail_address','postcode', 'mobile','phone'));
    	$info['user_id'] = $this->userInfo['id'];
    	$info['isdefault'] = 1;
    	$info = $this->_cookData($info);
		$result = Gc_Service_UserAddress::addUserAddress($info);
		if (!$result) $this->output(-1, '操作失败.');
		if($gid) {
			$url = $webroot.'/order/detail/?id='.$gid;
		} else {
			$url = $webroot.'/user/setting/index';
		}
		$this->output(0, '添加成功.', array('type'=>'redirect', 'url'=>$url));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function deleteAction() {
    	$id = intval($this->getInput('id'));
    	$addressInfo = Gc_Service_UserAddress::getUserAddress($id);
    	$webroot = Yaf_Application::app()->getConfig()->webroot;
    	if (!$addressInfo || $addressInfo['user_id'] != $this->userInfo['id']) {
    		$this->redirect($webroot.'/user/index/index');
    	}
    	$ret = Gc_Service_UserAddress::deleteUserAddress($id);
    	if (!$ret) $this->output(-1, '操作失败.');
    	$this->output(0, '操作成功.');
    }
    
    private function _cookData($info) {
    	if(!$info['realname']) $this->output(-1, '收货人姓名不能为空.');
    	if(!$info['mobile']) $this->output(-1, '手机号码不能为空.');
    	if (!preg_match('/^1[3458]\d{9}$/', $info['mobile'])) $this->output(-1, "手机号码格式不正确");
    	if(!$info['province'] || !$info['city'] || !$info['country']) $this->output(-1, '请选择省市区.');
    	if(!$info['detail_address']) $this->output(-1, '详细地址不能为空.');
    	return $info;
    }
}
