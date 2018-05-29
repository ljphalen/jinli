<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author yinjiayan
 *
 */
class WeiXin_Server_User extends WeiXin_Service_Base {
    public $openid;//用户的openid

    function __construct($openid) {
        $this->openid = $openid;
    }

    public function syncInfo() {
        $userInfo = $this->getUserInfo($this->openid);
        if(!$userInfo) {
            return;
        }
        if (!$userInfo['subscribe']) {
        	Admin_Service_Weixinuser::deleteByOpenId($this->openid);
        	return;
        }
        $groupId = WeiXin_Service_Base::getUserGroupId($this->openid);
        $data = array(
                        'open_id'=>$this->openid,
                        'nickname'=>$userInfo['nickname'],
                        'sex'=>$userInfo['sex'],
                        'province'=>$userInfo['province'],
                        'city'=>$userInfo['city'],
                        'country'=>$userInfo['country'],
                        'language'=>$userInfo['language'],
                        'subscribe_time'=>$userInfo['subscribe_time'],
                        'groupId' => $groupId,
                        'unionid'=>$userInfo['unionid'],
        );
        $exitUser = Admin_Service_Weixinuser::getByOpenId($this->openid);
        $weixinHeadImg = $userInfo['headimgurl'];
        $isHeadImgDownloaded = $exitUser && strrpos($exitUser['headimgurl'], strval(crc32($weixinHeadImg)));
        if (!$isHeadImgDownloaded) {
            $data['headimgurl'] = $weixinHeadImg;
        }
        if ($exitUser) {
            $ret = Admin_Service_Weixinuser::update($data, array(
                            'open_id'=>$data['open_id'],
            ));
        } else {
            Admin_Service_Weixinuser::add($data);
        }
        return $data;
    }
    
    /**
	 * 获取用户信息 
	 * @return mixed
	 */
	private function getUserInfo() {
		$params = array(
			"access_token"=>self::getToken(),
			'openid'=>$this->openid,
			"lang"=>'zh_CN'
		);
		$result = self::request("/user/info", $params);
		return json_decode($result, true);
	}
}
