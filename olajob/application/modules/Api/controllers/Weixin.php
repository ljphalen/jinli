<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class WeixinController extends Api_BaseController {

	/**
	 * 接受消息
	 */
	
	public function recvAction() {
		$params = $this->getInput(array("signature", "timestamp", "nonce", "echostr", "msg_signature"));
		
		$ret = WeiXin_Service_Base::checkSignature($params["signature"], $params["timestamp"], $params["nonce"]);
		
		if (!$ret) Common::log("checkSignature faild", "weixin.log");
		//只校验签名
		if ($params["echostr"]) {
			exit($params["echostr"]);
		} else {
			$mc = new WeiXin_Msg_Center($params["msg_signature"], $params["timestamp"], $params["nonce"]);
			$from_xml= file_get_contents("php://input");
			$msg = array();

			$errCode = $mc->decryptMsg($from_xml, $msg);
			if ($errCode == 0) {
				$mc->dispatch($msg);
			} else {
				Common::log($errCode, "weixin.log");
				exit("");
			}
		}
	}

	/**
	 *
	 */
	public function openidAction() {
		$code = $this->getInput('code');
        $chk = $this->getInput('chk');
		$user = new WeiXin_Server_User();

		//=========步骤1：网页授权获取用户openid============

		if (!$code) {
			$call_url = urlencode(WeiXin_Config::OPEN_ID_CALL_URL);
			$url = $user->createOauthUrlForCode($call_url, true);
			Common::log('getcode:'.$url, "weixin.log");
			$this->redirect($url);
		}
		//=========步骤2：获取access_token============
		Common::log('code:'.$code, "weixin.log");
		$user->code = $code;
		$data = $user->getToken();
		if (!$data["openid"]) {
			$this->output(-1, "get openid error.");
		}
        //=========步骤2：获取用户信息============
        $info = $user->getOutInfo();
        
        //get img
        $file = '';
        if($info['headimgurl']) {
        	$dir = 'user';
	        $path = Ola_Service_User::getSavePath($dir);        
	    	$file_arr = Ola_Service_User::downImages(array($info['headimgurl']), $dir, $path);
	    	if($file_arr) $file = $file_arr[0];
        }
        
        //update user
        $data = array(
                      'weixin_open_id'=>$info['openid'],
                      'headimgurl'=>$file
                     );
        

        Ola_Service_User::update($data, $this->userInfo['id']);

	    //cookie
		Util_Cookie::set('OLAUNIID', $user->openid, true);
		$redirect_url = Util_Cookie::get("LR", true);
        if (strpos($redirect_url, "?") === false) {
            $this->redirect($redirect_url."?openid=".$user->openid);
        } else {
            $this->redirect($redirect_url."&openid=".$user->openid);
        }
	}

	/**
	 * 用户登录并获取用户openid
	 *
	 */

	public function tokenAction() {
		$ret = WeiXin_Service_Menu::getToken();
		print_r($ret);exit;
	}

    /**
     *
     */
    public function jsparamsAction() {
        $pageUrl = $this->getInput('pageUrl');
        if(!$pageUrl) $this->output(-1, "empty pageUrl input. ", array());
        $ret = WeiXin_Service_Base::getJsConfigParaeters($pageUrl);
        $this->output(0,"success", $ret);
    }

	/**
	 * 菜单接口
	 */
	public function menuAction() {
		$menu = array(
			'button' => array(
				array(
					'name' => '兼职列表',
					'type' => 'view',
					'url'=>'http://olajob.findjoy.cn'
				),
				array('name'=> '我的',
					  'sub_button' => array(
                          array('type' => 'view', 'name' => '我的收藏', 'url'=>'http://olajob.findjoy.cn/front/user/my_favorite'),
						  array('type' => 'view', 'name' => '我的报名', 'url' => 'http://olajob.findjoy.cn/front/user/my_signup'),
						  array('type' => 'view', 'name' => '发布兼职', 'url' => 'http://olajob.findjoy.cn/front/user/publish'),
					  )
				),
			)
		);
		$ret = WeiXin_Service_Menu::createMenu($menu);
		print_r($ret);exit;
	}

	/**
	 * 删除菜单
	 */
	public function delmenuAction() {
		$ret = WeiXin_Service_Menu::delMenu();
		print_r($ret);exit;
	}
}
