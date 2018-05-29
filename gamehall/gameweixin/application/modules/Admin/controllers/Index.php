<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class IndexController extends Admin_BaseController {

    public $actions = array(
			'editPasswdPostUrl' => '/Admin/Index/editPasswordPost',
			'logoutUrl' => '/Admin/Login/logout',
    		'indexUrl' => '/Admin/Index/Index',
	);
    
    public function indexAction() {
        $this->redirect('/Admin/Keyword/index#J_hash_reply');
    }
    
    public function editPasswordAction() {
    }
    
    public function editPasswordPostAction() {
    	$inputVar = $this->getInput(array('oldpassword', 'newpassword', 'renewpassword'));
    	$userinfo = Admin_Service_User::checkUser($this->userInfo['username'], $inputVar['oldpassword']);
    	if($userinfo['username'] == $this->userInfo['username']) {
    		if($inputVar['newpassword'] == $inputVar['renewpassword']) {
	    		Admin_Service_User::updateUser(array('password' => $inputVar['newpassword']), $this->userInfo['id']);
	    	} else {
	    		$this->showMsgHtml('两次密码不一致');
	    	}
    	} else {
    		$this->showMsgHtml('原密码验证不正确');
    	}
    	
    	$this->showMsgHtml('密码修改成功', $this->actions['index']);
    }
    
    
}
?>