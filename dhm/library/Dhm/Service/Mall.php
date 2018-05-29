<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Dhm_Service_Mall{
	

    /**
     * get getList list for page
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
     */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

    public static function getAll(){
        return self::_getDao()->getAll();
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
	public static function update($data, $id) {
		if (!is_array($data)) return false;
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
     * @param $data
     * @return bool|int|string
     */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
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
	 * @param array $params
	 * @return array
	 */
	public static function getBy($params)
    {
		if (!is_array($params)) return false;
        return $ret = self::_getDao()->getBy($params);
	}





    /**
     * @param $data
     * @return array
     */
    private static function _cookData($data)
    {
        $tmp = array();
        if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
        if (isset($data['name'])) $tmp['name'] = $data['name'];
        if (isset($data['logo'])) $tmp['logo'] = $data['logo'];
        if (isset($data['link'])) $tmp['link'] = $data['link'];
        if (isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
        if (isset($data['is_top']))     $tmp['is_top']     = $data['is_top'];
        if (isset($data['status']))     $tmp['status']     = intval($data['status']);
        if (isset($data['type_id']))    $tmp['type_id']    = intval($data['type_id']);
        if (isset($data['descript']))   $tmp['descript']   = $data['descript'];
        if (isset($data['search_url'])) $tmp['search_url'] = $data['search_url'];
        if (isset($data['country_id'])) $tmp['country_id'] = intval($data['country_id']);
        return $tmp;
    }
	
	/**
	 * 
	 * @return Dhm_Dao_Mall
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Mall");
	}
}
