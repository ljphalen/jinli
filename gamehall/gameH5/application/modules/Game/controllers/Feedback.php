<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FeedbackController extends Game_BaseController {
    /**
     *  User Feedback
     */
	
	public $actions = array(
		'indexUrl' => '/feedback/index',
		'addPostUrl' => '/feedback/addPost',
	);
	
	public $perpage = 20;
	
    public function indexAction() {
    	$configs = Game_Service_Config::getAllConfig();
    	$this->assign('configs', $configs);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function addPostAction() {
    	$inputVar = $this->getInput(array('opinion', 'phone','qq'));
    	$data = $this->bulidData($inputVar);    	
    	$result = Game_Service_React::addReact($data);
    	if (!$result) {
    	    $this->feedBackOut('操作失败');
    	}
    	$this->feedBackOut();
    }
    
    private function bulidData($inputVar) {
        if (!$inputVar['opinion'] && Util_String::strlen($inputVar['opinion']) > 500) {
            $this->feedBackOut('请输入反馈意见');
        } 
        $data['react'] = $inputVar['opinion'];
        
        if ($inputVar['phone']) {
        	if (!preg_match('/^1[3458]\d{9}$/', $inputVar['phone'])) {
        		$this->feedBackOut('手机号码有误');
        	}
        	$data['mobile'] = $inputVar['phone'];
        }
        
        if (!$inputVar['qq']) {
        	$this->feedBackOut('请输入QQ或邮箱');
        }
        
        if (preg_match('/^[1-9]\d{5,11}$/',$inputVar['qq'])) {
            $data['qq'] = $inputVar['qq'];
        } else if (preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/',$inputVar['qq'])) {
        	$data['email'] = $inputVar['qq'];
        } else {
            $this->feedBackOut('请输入格式正确的QQ或邮箱');
        }
        $data['create_time'] = time();
        return $data;
    }
    
    private function feedBackOut($msg = '') {
        if ($msg) {
        	$msg = '服务端返回';
        }
        $this->ajaxOutput(array(), true, $msg);
    }
    
    /**
     * 常见问题
     * @author yinjiayan
     */
    public function faqAction() {
        $aqContent = Game_Service_Config::getValue('game_feedback_faq');
        $this->assign('aqContent', $aqContent);
    }
    
    public function aboutAction() {
    }
    
    public function clientAction() {
        $game = array();
        if(ENV == 'product'){
            $game = Resource_Service_GameData::getGameAllInfo(117);
        } else {
            $game = Resource_Service_GameData::getGameAllInfo(66);
        }
        $this->assign('game', $game);
    }
}