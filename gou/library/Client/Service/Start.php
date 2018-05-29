<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanzh
 *
 */
class Client_Service_Start{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllStart() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}	
	
    /**
     * 
     * @param unknown_type $page
     * @param unknown_type $limit
     * @param unknown_type $params
     * @return multitype:unknown multitype:
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
	 * @param unknown_type $id
	 */
	public static function getStart($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 * get by
	 */
	public static function getBy($params = array(), $orderby = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderby);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateStart($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

    public static function updateTJ($id) {
        if (!$id) return false;
        Gou_Service_ClickStat::increment(34, $id);
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
    }
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteStart($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addStart($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['hits'])) $tmp['hits'] = $data['hits'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		if(isset($data['type_id'])) $tmp['type_id'] = intval($data['type_id']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Start
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Start");
	}
}
