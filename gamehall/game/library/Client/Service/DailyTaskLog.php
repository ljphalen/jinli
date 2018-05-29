<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_DailyTaskLog{
	
	const FINISHED_STATUS = 1;

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::getDao()->count(), self::getDao()->getAll());
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
		$ret = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 已经获取的连续登录奖励值
	 */
	public static function getContinueLoginHadToal(){
		
	}
	

	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNum($params) {
		if (!is_array($params)) return false;
		return self::getDao()->getNum($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTotal($params) {
		if (!is_array($params)) return false;
		return self::getDao()->getTotal($params);
	}
	
	public static function getReportList( $params = array()) {
		return self::getDao()->getReportList($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params,$orderBy);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		self::getDao()->insert($data);
		return self::getDao()->getLastInsertId();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['send_object'])) $tmp['send_object'] = $data['send_object'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['task_id'])) $tmp['task_id'] = $data['task_id'];
		if(isset($data['denomination'])) $tmp['denomination'] = $data['denomination'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['days'])) $tmp['days'] = $data['days'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['download_status'])) $tmp['download_status'] = $data['download_status'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['content_type'])) $tmp['content_type'] = $data['content_type'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_DailyTaskLog
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_DailyTaskLog");
	}
}
