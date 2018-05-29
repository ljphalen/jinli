<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FeedbackController extends Super_BaseController {
    /**
     *  User Feedback
     */
	
	public $actions = array(
		'indexUrl' => '/super/feedback/index',
		'addPostUrl' => '/super/feedback/add_post',
	);
	
	public $perpage = 20;
	
    public function indexAction() {
    	$title = '反馈';
    	$configs = Game_Service_Config::getAllConfig();
    	$this->assign('configs', $configs);
    	$this->assign('title', $title);
    }
    
    
    /**
     *
     * Enter description here ...
     */
    public function add_postAction() {
    	$info = $this->getPost(array('mobile', 'qq','react', 'reply','status','create_time','result'));
    	$info = $this->_cookData($info);    	
    	$result = Game_Service_React::addReact($info);
    	if (!$result) $this->output(-1, '操作失败');
    	$webroot = Common::getWebRoot().'/super/feedback/index';
    	$this->output(0, '意见反馈成功',  array('type'=>'redirect','url'=>$webroot));
    	
    	
    }
    
    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
    	$webroot = Common::getWebRoot();
    	if(!$info['react']) $this->output(-1, '反馈意见不能为空');
    	if(Util_String::strlen($info['react']) > 500){
    		$this->output(-1, '最多只能输入500个字', array('type'=>'redirect','url'=>$webroot.'/super/feedback/'));
    	}
    	if(!preg_match("/^[1-9]\d{5,11}$/",$info['qq']) && $info['qq']) {
    		$this->output(-1, '请确保你输入的QQ号码正确', array('type'=>'redirect','url'=>$webroot.'/super/feedback/'));
    	}
    	if(!preg_match("/^1[3458]\d{9}$/", $info['mobile']) && $info['mobile']){
    		$this->output(-1, '请确保你输入的手机号码正确', array('type'=>'redirect','url'=>$webroot.'/super/feedback/'));
    	}
    	return $info;
    }
}