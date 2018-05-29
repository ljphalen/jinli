<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author ryan
 *
 */
class Dhm_Service_Goods {

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
		return self::_getDao()->get(($id));
	}
    
    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $sort
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $sort);
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
     * @param array $params
     * @param array $sort
     * @return array
     */
    public static function getsBy($params, $sort = array())
    {
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
		$ret = self::_getDao()->insert($data);
        if(!$ret)return false;
        return self::_getDao()->getLastInsertId();

	}

    /**
     * @param $data
     * @param $id
     * @return bool|int
     */
	public static function update($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, ($id));
	}


    /**
     * @param $id
     * @return bool|int
     */
	public static function delete($id) {
		return self::_getDao()->delete(($id));
	}
	
	/**
	 * @description 统计更新
	 * @param $id
	 * @return bool|int
	 */
	public static function updateTJ($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}


    /**
     * @param $data
     * @return array
     */
	private static function _cookData($data) {
		$tmp = array();
        if (isset($data['id']))          $tmp['id']          = intval($data['id']);
        if (isset($data['img']))         $tmp['img']         = $data['img'];
        if (isset($data['sort']))        $tmp['sort']        = intval($data['sort']);
        if (isset($data['title']))       $tmp['title']       = $data['title'];
        if (isset($data['status']))      $tmp['status']      = $data['status'];
        if (isset($data['tag_ids']))     $tmp['tag_ids']     = $data['tag_ids'];
        if (isset($data['content']))     $tmp['content']     = $data['content'];
        if (isset($data['brand_id']))    $tmp['brand_id']    = $data['brand_id'];
        if (isset($data['min_price']))   $tmp['min_price']   = $data['min_price'];
        if (isset($data['max_price']))   $tmp['max_price']   = $data['max_price'];
        if (isset($data['country_id']))  $tmp['country_id']  = $data['country_id'];
        if (isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
        if (isset($data['is_recommend']))$tmp['is_recommend']= $data['is_recommend'];
        return $tmp;
	}

	/**
	 *
	 * @return Dhm_Dao_Goods
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Goods");
	}
}
