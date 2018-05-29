<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Client_Service_Tag{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllTag() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}


    /**
     * @param int $id
     * @return bool|mixed
     */
    public static function getTag($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

    public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
    public static function getsBy($params=array(),$sort=array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$sort);
	}

    /**
     * @param array $data data
     * @param int   $id row 2 be updated
     * @return bool|int
     */
    public static function updateTag($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

    /**
     * @param $id
     * @return bool|int
     */
    public static function updateTagTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(13, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}

    /**
     * @param int $id drop id
     * @return bool|int
     */
    public static function deleteTag($id) {
		return self::_getDao()->delete(intval($id));
	}

    /**
     * @param array $data data 2 be added
     * @return bool|int
     */
    public static function addTag($data) {
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
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Tag
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Tag");
	}
}
