<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Installe{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllInstalle() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSortInstalle() {
		return self::_getDao()->getAllSortInstalle();
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
	public static function getInstalle($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateInstalle($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getInstalleDao()->updateInstalleStatus($id,$data['status']);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateInstalleDate($id) {
		$time = Common::getTime();
		return self::_getDao()->updateBy(array('update_time'=>$time), array('id'=>$id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function batchAddByInstalle($data,$id) {
		$tmp = array();
		$info = Client_Service_Installe::getInstalle(intval($id));
		foreach($data as $key=>$value) {
			$tmp[] = array(
					'id'=>'',
					'sort'=>0,
					'status'=>$info['status'],
					'installe_id'=>$id,
					'game_id'=>$value,
					'game_status'=>1,
			);
		}
		
		$time = Common::getTime();
		self::_getDao()->updateBy(array('update_time'=>$time), array('id'=>$id));
		
		return self::_getInstalleDao()->mutiInsert($tmp);
	}
	
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function batchSortByInstalle($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getInstalleDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}

	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getInstalleByGtypes($params) {
		if (!isset($params)) return false;
		return self::_getDao()->getBy($params,array('update_time'=>'DESC','id'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateInstalleTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteInstalle($id) {
		if (!$id) return false;
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchInstalle($data) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value) {
			self::_getDao()->deleteBy(array('id'=>$value));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateBatchInstalle($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchInstalle($ids, $status);
	}

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addInstalle($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['gtype'])) $tmp['gtype'] = intval($data['gtype']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['ids'])) $tmp['ids'] = $data['ids'];
		return $tmp;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookIdxData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['installe_id'])) $tmp['installe_id'] = intval($data['installe_id']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		return $tmp;
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxInstalleByInstalleId($id) {
		if (!$id) return false;
		return self::_getInstalleDao()->getsBy(array('installe_id'=>$id,'game_status'=>1),array('sort'=>'DESC','game_id'=>'DESC'));
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getIdxList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getInstalleDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getInstalleDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxInstalleByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getInstalleDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxInstalleByGameInstalleId($game_id,$installe_id) {
		if (!$game_id) return false;
		return self::_getInstalleDao()->getBy(array('game_id'=>$game_id,'installe_id'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateIdxInstalleStatus($game_id,$status) {
		if (!$game_id) return false;
		return self::_getInstalleDao()->updateIdxInstalleStatus($game_id,$status);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteByInstalleGames($data,$id) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value) {
			self::_getInstalleDao()->deleteBy(array('game_id'=>$value,'installe_id'=>$id));
		}
		
		$time = Common::getTime();
		self::_getDao()->updateBy(array('update_time'=>$time), array('id'=>$id));
		
		return true;
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @param unknown_type $Installe_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByInstalleId($game_id,$installe_id) {
		if (!$game_id) return false;
		return self::_getInstalleDao()->deleteBy(array('game_id'=>$game_id,'installe_id'=>$installe_id));
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameInstalles() {
		return self::_getInstalleDao()->getAllByGameStatus();
	}
	
	/**
	 * 
	 * @return Client_Dao_Installe
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Installe");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientInstalle
	 */
	private static function _getInstalleDao() {
		return Common::getDao("Client_Dao_IdxGameClientInstalle");
	}
}
