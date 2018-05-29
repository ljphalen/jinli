<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Subject extends Common_Service_Base{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllSubject() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getCanUseSubjects($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		
		$time = Common::getTime();
		$params['status'] = 1;
		$params['start_time'] = array('<', $time);
		$params['end_time'] = array('>', $time);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params,array('sort'=>'desc', 'id'=>'desc'));
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSubject($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	public static function getOnlineSubject($id) {
		$params['id'] = $id;
		$params['status'] = 1;
		$params['start_time'] = array('<=', Common::getTime());
		$params['end_time'] = array('>=', Common::getTime());
		return self::_getDao()->getBy($params,array('id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateSubject($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameSubject($data, $id) {
		if (!is_array($data)) return false;
		
		//开始事务
		$trans = parent::beginTransaction();
		try {
			self::updateSubject($data, $id);
		    //更新主题索引
			$subject_ids = Client_Service_Game::getGameclientByAllSubject(intval($id));
			if($subject_ids){
				 Client_Service_Game::updateSubjectsBySubjectIds(intval($id),$data['status']);
			}
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	public static function batchUpdate($data,$status) {
		if (!is_array($data)) return false;
		foreach($data as $value) {
			self::_getDao()->update(array('status' => $status), $value);
			//更新主题索引
			$subject_ids = Client_Service_Game::getGameclientByAllSubject(intval($value));
			if($subject_ids){
				Client_Service_Game::updateSubjectsBySubjectIds(intval($value),$status);
			}
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateSubjectTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteSubject($id) {
	//开始事务
		$trans = parent::beginTransaction();
		try {
			//删除主题索引
			$subject_ids = Client_Service_Game::getGameclientBySubject(intval($id));
			if($subject_ids){
			  $ret = Client_Service_Game::deleteGameclientBySubject(intval($id));
			  if (!$ret) throw new Exception('Delete Idx_Subject fail.', -205);
			}
			//删除主题
			$ret = self::_getDao()->delete(intval($id));
			if (!$ret) throw new Exception('Delete Subject fail.', -205);
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSubject($data) {
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
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['hdinfo'])) $tmp['hdinfo'] = $data['hdinfo'];		
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['hot'])) $tmp['hot'] = intval($data['hot']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Subject
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Subject");
	}
}
