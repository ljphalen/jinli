<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class WeiXin_Service_Menu
 */
class WeiXin_Service_Menu extends WeiXin_Service_Base{
	/**
	 * @param $data
	 * @return mixed
	 */
	public static function createMenu($data) {
		$params = array(
			'access_token'=>self::getToken()
		);
		return self::_request('https://api.weixin.qq.com/cgi-bin/menu/create', $params, json_encode($data,JSON_UNESCAPED_UNICODE));
	}

	/**
	 * @return mixed
	 */
	public static function delMenu() {
		$params = array(
			'access_token'=>self::getToken()
		);
		return self::_request('https://api.weixin.qq.com/cgi-bin/menu/delete', $params, null);
	}
}