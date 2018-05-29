<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 收货地址
 * @author tiansh  
 *
 */
class AddressController extends User_BaseController {
    
    public $actions = array(
        'indexUrl' => '/user/address/index',
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
    public function indexAction() {
		//收货地址
 		$address = Gou_Service_UserAddress::getDefaultAddress($this->userInfo['id']);
 		$this->assign('address',$address);
 		
 		$this->assign('user_info',$this->userInfo);
 		
 		$webroot = Common::getWebRoot();
 			
 		$nav = array(
 				'1'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/account/index"'),
 				'2'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/account/order_list"'),
 				'3'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/want/index"'),
 				'4'=> array('selected'=>'selected', 'href'=>''),
 		);
 		$this->assign('nav', $nav);
 		$this->assign('title', '收货地址');
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function editAction() {
    	$id = $this->getInput('id');
    	$refer = $this->getInput('refer');
    	
    	$webroot = Common::getWebRoot();
    	$address = Gou_Service_UserAddress::getUserAddress($id);
    	if (!$address || $address['user_id'] != $this->userInfo['id']) {
    		$this->redirect($webroot.'/user/address/index');
    	}
    	$this->assign('refer', $refer);
    	$this->assign('info', $address);
    	$this->assign('title', '收货地址');
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function edit_postAction() {
    	$info = $this->getPost(array('id','realname','province','city','country','detail_address','postcode', 'mobile','phone'));

    	$info['user_id'] = $this->userInfo['id'];
    	$info = $this->_cookData($info);
		$result = Gou_Service_UserAddress::updateUserAddress($info, $info['id']);
		if (!$result) $this->output(-1, '修改失败.');
		
		$webroot = Common::getWebRoot();
		$url = $webroot.$this->actions['indexUrl'];
		
		$refer = $this->getInput('refer');
		if ($refer) $url = html_entity_decode($refer);
		$this->output(0, '修改成功.', array('type'=>'redirect', 'url'=>$url));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function addAction() {
    	$refer = $this->getInput('refer');
    	$this->assign('refer', $refer);
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function add_postAction() {
    	//现只保留一个收货地址
    	$info = $this->getPost(array('realname','province','city','country','detail_address','postcode', 'mobile','phone'));
    	$refer = $this->getInput('refer');
    	
    	$info['user_id'] = $this->userInfo['id'];
    	$info['isdefault'] = 1;
    	$info = $this->_cookData($info);
		$result = Gou_Service_UserAddress::addUserAddress($info);
		if (!$result) $this->output(-1, '操作失败.');
		
		$webroot = Common::getWebRoot();
		$url = $webroot.$this->actions['indexUrl'];
		if ($refer) $url = html_entity_decode($refer);
		
		$this->output(0, '添加成功.', array('type'=>'redirect', 'url'=>$url));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function deleteAction() {
    	$id = intval($this->getInput('id'));
    	$consigneeInfo = Gou_Service_UserAddress::getUserAddress($id);
    	$webroot = Common::getWebRoot();
    	if (!$consigneeInfo || $consigneeInfo['user_id'] != $this->userInfo['id']) {
    		$this->redirect($webroot.'/user/account/index');
    	}
    	$ret = Gou_Service_UserAddress::deleteUserAddress($id);
    	if (!$ret) $this->output(-1, '操作失败.');
    	$this->output(0, '操作成功.');
    }
    
    private function _cookData($info) {
    	if(!$info['realname']) $this->output(-1, '收货人姓名不能为空');
    	if(!$info['mobile']) $this->output(-1, '手机号码不能为空');
    	if(strlen($info['postcode']) != 6) $this->output(-1, '请输入正确的邮编');
    	if (!preg_match('/^1[3458]\d{9}$/', $info['mobile'])) $this->output(-1, "手机号码格式不正确");
    	if(!$info['province'] || !$info['city'] || !$info['country']) $this->output(-1, '请选择省市区');
    	if(!$info['detail_address']) $this->output(-1, '详细地址不能为空');
    	
    	return $info;
    }
}
