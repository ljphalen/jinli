<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends Front_BaseController {
	
    /**
     * 
     */
    public function coinAction() {
    	$this->assign('title', '积分换购活动');
    }
    
    public function fanliAction() {
    	$this->assign('title', '积分返利');
    }
    


    /**
     * share
     */
    public function shareAction() {
    	$this->assign('title', '分享活动');
    }
    
    /**
     * share
     */
    public function shareTopAction() {
    	exit();
    	list(, $top_list) = Activity_Service_Share::getList(1, 10, array('status'=>1));
    	$this->assign('top_list', $top_list);
    	$this->assign('title', '分享活动');
    }
    
    /**
     * share
     */
    public function downloadAction() {
    	$uid = $this->getInput('uid');
    	$this->assign('uid', $uid);
    	$this->assign('title', '分享活动');
    }
    
    /**
     * 粉丝招募
     */
    public function fansAction() {
    	$this->assign('title', '粉丝招募');
    }
    
    /**
     * 母亲节活动
     */
    public function mothersdayAction() {
    	$this->assign('title', '母亲节活动');
    }
    
    /**
     * 积分活动入口
     */
    public function scoreAction() {
        $this->assign('title', '积分活动');
    }
    
    
    /**
     * apk download
     */
    public function apkDownloadAction() {
    	$uid = $this->getInput('uid');
    	$cookie_name = 'GOU_DOWNLOAD';
    	$share = Activity_Service_Share::getBy(array('uid'=>$uid));
    	$cookie = Util_Cookie::get($cookie_name, true);
    	if($share && !$cookie) {
    		Activity_Service_Share::update(array('hits'=>$share['hits']+1), $share['id']);
    		$data = array(
    				'uid'=>$uid,
    				'hits_time'=>Common::getTime()
    		);
    		Activity_Service_ShareLog::addLog($data);
    		Util_Cookie::set($cookie_name, $uid, true, Common::getTime() + 31104000, '/', self::getDomain());
    	}
    	$this->redirect('http://goudl.gionee.com/apps/shoppingmall/GN_Gou-banner.apk');
    }
    
}