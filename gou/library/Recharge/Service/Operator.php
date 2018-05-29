<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 话费充值运营商管理
 * @author huangsg
 *
 */

class Recharge_Service_Operator extends Common_Service_Base {
	/**
	 * 获取运营商价格数据
	 * @param unknown $opid
	 * @return unknown
	 */
	public static function getList($opid) {
		$opid = intval($opid);
		$list = self::_getDao()->getsBy(array('operator'=>$opid));
		return $list;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		//$data = self::_cookData($data);
		return self::_getDao()->getBy($params);
	}
	
	/**
	 * 修改价格数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updatePrice($data, $id){
		if (empty($data)) return false;
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @return Recharge_Dao_Operator
	 */
	private static function _getDao() {
		return Common::getDao("Recharge_Dao_Operator");
	}
}