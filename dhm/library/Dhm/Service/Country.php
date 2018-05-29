<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Dhm_Service_Country{
    

    /**
     * @return array
     */
    public static function getAll() {
        return array(self::_getDao()->count(), self::_getDao()->getAll());
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

    /**
     * @param $id
     * @return bool|mixed
     */
	public static function getCountry($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

    /**
     * @param $id
     * @return bool|mixed
     */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

    /**
     * @param $data
     * @param $id
     * @return bool|int
     */
	public static function updateCountry($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

    /**
     * @param $id
     * @return bool|int
     */
	public static function deleteCountry($id) {
		return self::_getDao()->delete(intval($id));
	}

    /**
     * @param $data
     * @return bool|int
     */
	public static function addCountry($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 获取国家记录
	 * @param $params
	 * @param array $orderBy
	 * @return bool|mixed
	 */
	public static function getBy($params, $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * @param array $params
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
     * @param $data
     * @return array
     */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name']      = $data['name'];
		if(isset($data['name'])) $tmp['lang_code'] = $data['lang_code'];
		if(isset($data['name'])) $tmp['currency']  = $data['currency'];
		if(isset($data['logo'])) $tmp['logo']      = $data['logo'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Dhm_Dao_Country
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Country");
	}
}
