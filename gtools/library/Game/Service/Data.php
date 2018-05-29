<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Data
 * @author rainkid
 *
 */
class Game_Service_Data extends Common_Service_Base{

	public static function getAllGame() {
		return array(self:: _getStockDao()->count(), self::_getStockDao()->getAll());
	}
	
	public static function getsByGame($params){
		return self::_getStockDao()->getsBy($params);
	}
	
	public static function getItems($id) {
		return self:: _getStockDao()->get($id);
	}
	
	public static function getByVersion($params){
		return self::_getVersionDao()->getBy($params);
	}
	
	//---------------相关DAO--------------------------
	private static function _getStockDao() {
		return Common::getDao("Game_Dao_Stock");
	}
	
	private static function _getAttributeDao() {
		return Common::getDao("Game_Dao_Attribute");
	}
	
	private static function _getCategoryDao() {
		return Common::getDao("Game_Dao_Category");
	}
	
	private static function _getLabelDao() {
		return Common::getDao("Game_Dao_Label");
	}
		
	private static function _getVersionDao() {
		return Common::getDao("Game_Dao_Version");
	}
}