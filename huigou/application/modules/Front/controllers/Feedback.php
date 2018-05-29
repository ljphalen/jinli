<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FeedbackController extends Front_BaseController {
    /**
     *  User Feedback
     */
	
	public $actions = array(
		'indexUrl' => '/feedback/index',
		'addUrl' => '/feedback/add',
		'addPostUrl' => '/feedback/add_post',
	);
	
	public $perpage = 20;
	
    public function indexAction() {
    	$title = "意见反馈";
    	$this->assign('title', $title);
    	list($total, $reacts) = Gc_Service_FeedBack::getAllFeedBack();
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
    	$info = $this->getPost(array('mobile', 'react','reply','status'));
    	if($this->userInfo){
    		$info['mobile'] = $this->userInfo['username'];
    	}
    	$info = $this->_cookData($info);
    	
    	 
    	$result = Gc_Service_FeedBack::addFeedback($info);
    	if (!$result) $this->output(-1, '操作失败');
    	$webroot = Yaf_Application::app()->getConfig()->webroot;
    	$this->output(0, '意见反馈成功.', array('feedback'=>'1'));
    }
    
    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
    	if(!$info['react']) $this->output(-1, '反馈意见不能为空.');
    	return $info;
    }
}