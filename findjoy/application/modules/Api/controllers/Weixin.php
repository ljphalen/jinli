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
			$url = $user->createOauthUrlForCode(WeiXin_Config::OPEN_ID_CALL_URL, $chk);
			Common::log($url, "weixin.log");
			$this->redirect($url);
		}
		//=========步骤2：获取access_token============
		$user->code = $code;
		$user->getToken();
		if (!$user->openid) {
			$this->output(-1, "get openid error.");
		}
        //=========步骤2：获取用户信息============
        $user->getOutInfo();

	    //
		Util_Cookie::set('FJUNIID', $user->openid, true);
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
					'name' => '海淘资讯',
					'type' => 'view',
					'url'=>'http://dhm.miigou.com'
				),
				array(
					'name' => '热门港货',
					'type' => 'view',
					'url'=>'http://www.findjoy.cn'
				),
				array('name'=> '一键代买',
					  'sub_button' => array(
                          array('type' => 'click', 'key'=>'FJ_ONE_KEY', 'name' => '代买'),
                          array('type' => 'click', 'key'=>'FJ_SEARCH',  'name' => '搜索'),
						  array('type' => 'view', 'name' => '订单', 'url' => 'http://www.findjoy.cn/order/list'),
						  array('type' => 'view', 'name' => '购物车', 'url' => 'http://www.findjoy.cn/cart/index'),
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
