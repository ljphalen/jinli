<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author ryan
 *
 */
class Dhm_Service_GoodsMall {

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
	 *
	 * @param array $params
	 * @param array $sort
	 * @return array
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
     * @param array $data
     * @param array $params
     * @return bool|int
     */
    public static function updateBy($data, $params = array())
    {
        if (!is_array($data) || !is_array($params)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updateBy($data, $params);
    }


    /**
     * @param $id
     * @return bool|int
     */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}


    /**
     * @param $data
     * @return array
     */
	private static function _cookData($data) {
		$tmp = array();
        if (isset($data['id']))          $tmp['id']          = intval($data['id']);
        if (isset($data['mall_id']))     $tmp['mall_id']     = $data['mall_id'];
        if (isset($data['type_id']))     $tmp['type_id']     = $data['type_id'];
        if (isset($data['goods_id']))    $tmp['goods_id']    = $data['goods_id'];
        if (isset($data['min_price']))   $tmp['min_price']   = floatval($data['min_price']);
        if (isset($data['max_price']))   $tmp['max_price']   = floatval($data['max_price']);
        if (isset($data['url']))         $tmp['url']         = $data['url'];

		return $tmp;
	}

	/**
	 *
	 * @return Dhm_Dao_GoodsMall
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_GoodsMall");
	}
}
