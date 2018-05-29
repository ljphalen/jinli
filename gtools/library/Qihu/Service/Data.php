<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Qihu_Service_Data extends Common_Service_Base{

	/**
	 *
	 * Enter description here ...
	 */
	public static function getItems($id) {
		return self::_getApkDao()->get($id);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getByItems($params) {
		return self::_getApkDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getsByItems($params) {
		return self::_getApkDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param unknown $data
	 * @param unknown $params
	 */
	public static function updateItems($data, $id) {
		return self::_getApkDao()->update($data, $id);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllApk() {
		return array(self::_getApkDao()->count(), self::_getApkDao()->getAll());
	}

	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getCategory($id) {
		return self::_getDetailDao()->get($id);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllCategory() {
		return array(self::_getCategoryDao()->count(), self::_getCategoryDao()->getAll());
	}
	
	
	
	/**
	 * 
	 * @return Game_Dao_Game
	 */
	private static function _getApkDao() {
		return Common::getDao("Qihu_Dao_Apk");
	}
	
	/**
	 * 
	 * @return Game_Dao_IdxGameLabel
	 */
	private static function _getCategoryDao() {
		return Common::getDao("Qihu_Dao_Category");
	}
	
	
}
