<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desTypeiption here ...
 * @author tiansh
 *
 */
class Type_Service_Type{
	

	/**
	 *
	 * Enter desTypeiption here ...
	 */
	public static function getAllType() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter desTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addType($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getType($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateType($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

    /**
     * @param $params
     * @param array $orderBy
     * @return bool|mixed
     */
    public static function getsBy($params, $sort=array()){
        if (!is_array($params) || !is_array($sort)) return false;
        $ret = self::_getDao()->getsBy($params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
	}
	
	/**
	 * @param $params
	 * @param array $orderBy
	 * @return bool|mixed
	 */
	public static function getCount($params){
	    if (!is_array($params)) return false;
	    return self::_getDao()->count($params);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTypeTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(19, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteType($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter desTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['keyword'])) $tmp['keyword'] = $data['keyword'];
		if(isset($data['is_recommend'])) $tmp['is_recommend'] = intval($data['is_recommend']);
		if(isset($data['type_id'])) $tmp['type_id'] = intval($data['type_id']);
		if(isset($data['ctype_id'])) $tmp['ctype_id'] = intval($data['ctype_id']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Type_Dao_Type
	 */
	private static function _getDao() {
		return Common::getDao("Type_Dao_Type");
	}
}
