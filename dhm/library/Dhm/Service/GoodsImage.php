<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author ryan
 *
 */
class Dhm_Service_GoodsImage {

    /**
     * all record
     * @return array
     */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
    
    /**
     * @param $id
     * @return mixed
     */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}
    
    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
     */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params);
	}
	

    /**
     * @param $params
     * @param array $sort
     * @return array|bool
     */
	public static function getsBy($params, $sort = array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsData($params, $sort) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $list = self::_getDao()->getsBy($params, $sort);
	    return $list;
	}

    /**
     * @param $data
     * @return bool|int
     */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

    /**
     * @param $data
     * @param $id
     * @return bool|int
     */
	public static function update($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}


    /**
     * @param $id
     * @return bool|int
     */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}


    /**
     * @param array $params
     * @return bool|int
     */
	public static function delBy($params) {
		return self::_getDao()->deleteBy($params);
	}


    /**
     * @param $data
     * @return array
     */
	private static function _cookData($data) {
		$tmp = array();
        if (isset($data['id']))          $tmp['id']          = intval($data['id']);
        if (isset($data['goods_id']))    $tmp['goods_id']    = intval($data['goods_id']);
        if (isset($data['img']))         $tmp['img']         = $data['img'];
		return $tmp;
	}

	/**
	 *
	 * @return Dhm_Dao_GoodsImage
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_GoodsImage");
	}
}
