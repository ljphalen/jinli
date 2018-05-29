<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Service_Imsi
 * @author fanch
 *
 */
class Freedl_Service_Imsi extends Common_Service_Base{

	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array()) {
		$ret =  self::_getDao()->getBy($params, $orderBy);
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 根据imsi获取运营商跟地区编码
	 * @param $imsi
	 * @return false | array('','')
	 */
	public static function convert($imsi){
		if(!$imsi) return false;
		//网络号判断
		$cunet = substr(trim($imsi), 0, 5);
		//联通内容库判断
		if($cunet!='46001') return false;
		$imsiCode = substr(trim($imsi), 5, 5);
		$imsiData = self::getBy(array('imsi'=>$imsiCode));
		if(!$imsiData) return false;
		return array('cu', $imsiData['province']);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['imsi'])) $tmp['imsi'] = $data['imsi'];
		if(isset($data['province'])) $tmp['province'] = $data['province'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_Hd
	 */
	private static function _getDao() {
		return Common::getDao("Freedl_Dao_Imsi");
	}
}
