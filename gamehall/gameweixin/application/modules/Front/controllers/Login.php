<?php
if (! defined('BASE_PATH')) exit('Access Denied!');

class LoginController extends Front_BaseController {
    
    private $gameRoot = '';
    
    public $actions = array(
                    'loginUrl' => '/Front/Login/login',
                    'loginPostUrl' => '/Front/Login/loginPost',
                    'registerUrl' => '/game/user/register',
                    'refreshgvcUrl' =>'/Front/Login/refreshgvc'
    );
    
    public function init() {
        parent::init();
        $this->gameRoot = Common::getConfig('apiConfig', 'gameApiRoot');
    }
    
    public function loginAction() {
        $openId = $this->getInput('token');
        $weixinUser = null;
        if ($openId) {
        	$weixinUser = Admin_Service_Weixinuser::getByOpenId($openId);
        	if (!$weixinUser) {
        	    $userServer = new WeiXin_Server_User($openId);
        	    $userServer->syncInfo();
        	    $weixinUser = Admin_Service_Weixinuser::getByOpenId($openId);
        	}
        }
        if (!$weixinUser) {
        	$this->assign('isUserInvalid', true);
        } else if ($weixinUser['is_binded']) {
            $this->assign('isBinded', true);
        } else {
            $webRoot = Yaf_Application::app()->getConfig()->webroot;
            $forgetUrl = Common::getConfig('apiConfig', 'forgeturl');
            $this->assign('token', $openId);
            $this->assign('gameRoot', $this->gameRoot);
            $this->assign('forgetUrl', $forgetUrl);
            $this->assign('redirectUrl', $webRoot.$this->actions['loginUrl']);
        }
    }
    
    /**
     * tn:手机号
	p:密码
	vid:随验证码一起下发的
	vty:同vid
	vtx:页面输入的验证码的值
     * @author yinjiayan
     */
    public function loginPostAction() {
        $openId = $this->getInput('token');
        $this->checkOpenId($openId);
        $redirectUrl = $this->getInput('redirectUrl');
        
        $input = $this->getInput(array('tn', 'p', 'vid', 'vty', 'vtx'));
        $result = $this->request('/api/user/login', $input);
        $resultData = json_decode($result, true);
        if ($resultData['data']['uuid']) {
        	$resultData['data']['redirectUrl'] = $redirectUrl ? $redirectUrl : '';
        	$this->bindAccount($openId, $resultData['data']['uuid']);
        } else if($resultData['data']['vmt'] == 1){
            $resultData['data']['vty'] = array('vtext');
        }
        exit(json_encode($resultData));
    }
    
    private function checkOpenId($openId) {
        if (!$openId) {
            $this->ajaxJsonOutput(array(
                            'r' => '1101',
                            'vmt' => '1',
                            'vty' => array('vtext')
            ));
        }
    }
    
    private function bindAccount($openId, $uuid) {
        $data = array(
                        'is_binded' => 1,
                        'binded_uuid' => $uuid
        );
        $params = array(
                        'open_id' => $openId
        );
        Admin_Service_Weixinuser::update($data, $params);
    }
    
    private function request($uri, $params=array(), $method = "POST") {
        $query = http_build_query((array)$params);
        $url = sprintf("%s%s?%s", $this->gameRoot, $uri, $query);
        $curl = new Util_Http_Curl($url);
        $result = $curl->send($method);
        return $result;
    }
    
    public function refreshgvcAction() {
        $result = $this->request('/api/user/refreshgvc');
        exit($result);
    }
}

?>