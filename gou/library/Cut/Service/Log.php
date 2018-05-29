<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Cut_Service_Log{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllLog() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
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
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 4, $params = array(), $orderBy = array()) {
	    $params = self::_cookData($params);
	    if ($page < 1) $page = 1;
	    $start = ($page - 1) * $limit;
	    $sqlWhere  = self::_getDao()->_cookParams($params);
	    $ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
	    $total = self::_getDao()->searchCount($sqlWhere);
	    return array($total, $ret);
	}
	
	/**
	 *
	 * @param array $params
	 * @return bool|string
	 */
	public static function count($params = array()){
	    if (!is_array($params)) return false;
	    $params = self::_cookData($params);
	    $sqlWhere  = self::_getDao()->_cookParams($params);
	    return self::_getDao()->count($sqlWhere);
	}
	


    /**
     * @param int $id
     * @return bool|mixed
     */
    public static function getLog($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 
	 * @param unknown_type $params
	 * @return boolean|Ambigous <boolean, mixed>
	 */
    public static function getBy($params, $sort=array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $sort);
	}

    public static function getsBy($params,$sort=array()) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        $ret = self::_getDao()->getsBy($params,$sort);
        return array($total,$ret);
    }

    /**
     * @param array $data data
     * @param int   $id row 2 be updated
     * @return bool|int
     */
    public static function updateLog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

    /**
     * @param $id
     * @return bool|int
     */
    public static function updateLogTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(13, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}

    /**
     * @param int $id drop id
     * @return bool|int
     */
    public static function deleteLog($id) {
		return self::_getDao()->delete(intval($id));
	}

    /**
     * @param array $data data 2 be added
     * @return bool|int
     */
    public static function addLog($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

    /**
     * filter data for user input
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Cut_Dao_Log
	 */
	private static function _getDao() {
		return Common::getDao("Cut_Dao_Log");
	}
}
