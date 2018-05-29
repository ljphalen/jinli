<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desSupplieription here ...
 * @author lichanghua
 *
 */
class Gou_Service_Supplier{
	

	/**
	 *
	 * Enter desSupplieription here ...
	 */
	public static function getAllSupplier() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter desSupplieription here ...
	 */
	public static function getAllSupplierSort() {
		return array(self::_getDao()->getAllSupplierSortCount(), self::_getDao()->getAllSupplierSort());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getSupplier($id) {
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 * 获取Supplier_id
	 * @return Ambigous <mixed, boolean, string>
	 */
	public function getSupplierId() {
		return Util_Cookie::get("GcSource", true);
	}
	
	/**
	 *
	 * @param array $params
	 * @return multitype:
	 */
	public static function getListBy($params = array()) {
		return self::_getDao()->getsBy($params, array('sort'=>'DESC'));
	}
	
	
	/**
	 *
	 * Enter desSupplieription here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSupplier($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateSupplier($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteSupplier($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return boolean
	 */
	public static function getSuppliersByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getSuppliersByIds($ids);
	}

	/**
	 *
	 * Enter desSupplieription here ...
	 * @param unknown_type $data
	 */
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['show_type'])) $tmp['show_type'] = $data['show_type'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_Supplier
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Supplier");
	}
}
