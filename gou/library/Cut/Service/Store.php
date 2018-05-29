<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Cut_Service_Store{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllStore() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(),$sort=array()) {
        $params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getStore($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function getCountByType() {
		$res = self::_getDao()->getCountByType();
		foreach ($res as $v) {
			$ret[$v['type_id']]=$v['num'];
		}
		return $ret;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateStore($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteStore($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addStore($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

    /**
     * @param int $id
     * @return bool|mixed
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		$data = self::_cookData($params);
		return self::_getDao()->getBy($data);
	}

	public static function updateBy($data,$params) {
		if (!is_array($params)) return false;
        $data = self::_cookData($data);
		return self::_getDao()->updateBy($data,$params);
	}

    /**
     * 更新推广链接
     * @param $data
     * @param $params
     * @return bool
     */
    public static function updateExt($data,$params) {
		if (!is_array($params)) return false;
        $data = self::_cookData($data);
        $row = self::getBy($params);
        if(!$row){
            $ret = self::_getDao()->insert($data);
        }else{
            $ret = self::_getDao()->updateBy($data,$params);
        }

        return $ret;
	}

    /**
     * @param $params
     * @param array $sort
     * @return bool|mixed
     */
    public static function getsBy($params,$sort=array()) {
		if (!is_array($params)) return false;
		$total = self::_getDao()->count($params);
		$ret = self::_getDao()->getsBy($params,$sort);
		return array($total,$ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateShopTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(15, $id);
		return self::_getDao()->increment('img', array('id'=>intval($id)));
	}
	
	/**
	 * sort
	 * @param array $sort
	 * @return boolean
	 */
	public static function sort($sorts) {
	    foreach($sorts as $key=>$value) {
	        self::_getDao()->update(array('sort'=>$value), $key);
	    }
	    return true;
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updates($ids, $data) {
	    if (!is_array($data) || !is_array($ids)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id']))          $tmp['id'] = intval($data['id']);
        if(isset($data['num_iid']))     $tmp['num_iid'] = intval($data['num_iid']);
        if(isset($data['type_id']))     $tmp['type_id'] = intval($data['type_id']);
        if(isset($data['type']))        $tmp['type'] = $data['type'];
        if(isset($data['shop_id']))     $tmp['shop_id'] = intval($data['shop_id']);
        if(isset($data['title']))       $tmp['title'] = $data['title'];
        if(isset($data['share_title']))       $tmp['share_title'] = $data['share_title'];
        if(isset($data['price']))       $tmp['price'] = $data['price'];
        if(isset($data['discount_price']))       $tmp['price'] = $data['discount_price'];
        if(isset($data['sort']))        $tmp['sort'] = intval($data['sort']);
        if(isset($data['pic_url']))     $tmp['img'] = $data['pic_url'];
        if(isset($data['img']))         $tmp['img'] = $data['img'];
        if(isset($data['description'])) $tmp['description'] = $data['description'];
        if(isset($data['status']))      $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Cut_Dao_Store
	 */
	private static function _getDao() {
		return Common::getDao("Cut_Dao_Store");
	}
}
