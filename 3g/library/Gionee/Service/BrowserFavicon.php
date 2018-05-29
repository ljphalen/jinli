<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信活动类
 */
class Gionee_Service_BrowserFavicon {

	public static function getByKey($key) {
		$row = Gionee_Service_BrowserFavicon::getDao()->getBy(array('key'=>$key));
		return $row;
	}

	/**
	 * @return Gionee_Dao_BrowserFavicon
	 */
	public static function getDao() {
		return Common::getDao("Gionee_Dao_BrowserFavicon");
	}
}
