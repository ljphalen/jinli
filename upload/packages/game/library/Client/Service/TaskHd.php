<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_TaskHd{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		return self::_getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 * 查找有效的联运游戏活动
	 * @return array
	 */
	public static function getsUnionGamesHds() {
		$params =  array();
		$params['status'] = 1;
		$params['htype'] = 2;
		$params['hd_start_time'] = array('<',Common::getTime());
		$params['hd_end_time'] = array('>',Common::getTime());
		return self::_getDao()->getsBy($params,array('id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
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
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['htype'])) $tmp['htype'] = $data['htype'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['hd_start_time'])) $tmp['hd_start_time'] = $data['hd_start_time'];
		if(isset($data['hd_end_time'])) $tmp['hd_end_time'] = $data['hd_end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['hd_object'])) $tmp['hd_object'] = $data['hd_object'];
		if(isset($data['condition_type'])) $tmp['condition_type'] = $data['condition_type'];
		if(isset($data['condition_value'])) $tmp['condition_value'] = $data['condition_value'];
		if(isset($data['rule_type'])) $tmp['rule_type'] = $data['rule_type'];
		if(isset($data['game_version'])) $tmp['game_version'] = $data['game_version'];
		if(isset($data['game_object'])) $tmp['game_object'] = $data['game_object'];
		if(isset($data['rule_content'])) $tmp['rule_content'] = $data['rule_content'];
		if(isset($data['rule_content_percent'])) $tmp['rule_content_percent'] = intval($data['rule_content_percent']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_TaskHd
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_TaskHd");
	}
}
