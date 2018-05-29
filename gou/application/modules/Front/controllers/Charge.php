<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ChargeController extends Front_BaseController {

    public $actions = array(
        'listUrl' => '/charge/index',
    	'fatePostUrl' => '/charge/fate_post',
    	'chargeUrl' => '/charge/charge',
    	'chargePostUrl' => '/charge/charge_post',
    );
    
    public function init() {
    	parent::init();
    	$this->redirect(Common::getWebRoot());
    }
	
    /**
     * 
     */
    public function indexAction() {
		$fated_logs = Gou_Service_FateLog::getFatedLogs(0,4);
		$this->assign('fate_logs', $fated_logs);
    }

    /**
     * 
     */
    public function fate_postAction() {
    	$info = $this->getPost(array('mobile', 'question', 'answer'));
    	
    	$fatedKey = $info['mobile'] . Util_Http::getClientIp() . 'fated';
    	$cache = Common::getCache();
    	
    	$result = Gou_Service_Fate::fate($info);
    	if (Common::isError($result)) {
    		$msg = $result['msg'];
    	} else {
    		$cache->set($fatedKey, 1, 60 * 60 * 24);
    		$msg = '恭喜您抽中了'.$result['price'].'元话费，您填写的手机号上会收到中奖确认短信，确认后话费会充到您填写的手机号上。';
    	}
    	
    	$this->assign('ref', '/charge/index');
    	$this->assign('msg', $msg);
		$this->getView()->display('charge/dialog.phtml');
    }
    
    /**
     * 
     */
    public function chargeAction() {
    	
    }
    
    /**
     * 
     */
    public function charge_postAction() {
    	$info = $this->getPost(array('mobile', 'question', 'answer'));
    	$result = Gou_Service_Fate::checkFate($info);
    	if (!Common::isError($result)) {
    		$msg = '验证通过，'.$result['price'].'元话费将在1个工作日内充值到你的手机中，手气不错，再<a href="http://caipiao.wap.taobao.com/?ttid=51sjl001" 
    				style="color:blue;text-decoration:underline;">去买柱彩票</a>吧！';
    	}  else {
    		$msg = $result['msg'];
    	}
    	$this->assign('ref', '/charge/charge');
    	$this->assign('msg', $msg);
    	$this->getView()->display('charge/dialog.phtml');
    }    
}