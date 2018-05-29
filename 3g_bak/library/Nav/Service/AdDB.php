<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 卡片导航 广告 数据库类
 * @author huwei
 *
 */
class Nav_Service_AdDB {

	/**
	 *
	 * @return Nav_Dao_AdList
	 */
	public static function getListDao() {
		return Common::getDao("Nav_Dao_AdList");
	}


}