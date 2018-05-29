<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 自营商城快递公司管理
 * @author huangsg
 *
 */
class Amigo_Service_Shipping extends Common_Service_Base {
	/**
	 * 获取快递公司列表
	 * @param unknown $page
	 * @param unknown $limit
	 * @param unknown $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getAll(){
		return  self::_getDao()->getAll(array('sort'=>'DESC'));
	}
	
	/**
	 * 获取一条快递公司记录
	 * @param unknown $id
	 * @return boolean
	 */
	public static function getOne($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加快读公司记录
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function add($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改快递公司
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean
	 */
	public static function update($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 删除快递公司数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deletePrice($id){
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * @param unknown $data
	 * @return multitype:unknown number
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Amigo_Dao_Shipping
	 */
	private static function _getDao() {
		return Common::getDao("Amigo_Dao_Shipping");
	}
}