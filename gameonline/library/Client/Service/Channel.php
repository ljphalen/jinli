<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Channel{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllChannel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSortChannel() {
		return self::_getDao()->getAllSortChannel();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public function getCanUseChannel($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseChannels($params, $start, $limit);
		$total = self::_getDao()->getCanUseChannelCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getChannel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateChannel($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function batchAddByChannel($data,$ctype) {
		if (!is_array($data)) return false;
		$tmp = array();
		foreach($data as $key=>$value) {
			$info = self::getChannelByGameId($value,$ctype);
			if(!$info){
				$tmp[] = array(
						'id'=>'',
						'sort'=>0,
						'ctype'=>$ctype,
						'game_id'=>$value,
						'game_status'=>1,
				);
			}
		}
		$ret = true;
		if($tmp) $ret =  self::_getDao()->mutiInsert($tmp);
		return $ret;
	}
		
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function batchSortByChannel($sorts,$ctype) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value,'ctype'=>$ctype), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function batchDeleteByChannel($data,$ctype) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->deleteBy(array('id'=>$v[0],'ctype'=>$ctype));
		}
		return true;
	}
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getChannelByGtype($ctype) {
		if (!$ctype) return false;
		return self::_getDao()->getsBy(array('ctype'=>$ctype,'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getChannelByGtypes($params) {
		if (!isset($params)) return false;
		return self::_getDao()->getChannelByGtypes($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getChannelByGameId($game_id,$ctype) {
		if (!$game_id) return false;
		return self::_getDao()->getsBy(array('game_id'=>$game_id,'ctype'=>$ctype));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateChannelTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteChannel($id) {
		if (!$id) return false;
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchChannel($data) {
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
	public static function updateBatchChannel($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchChannel($ids, $status);
	}
	
	
	public static function updateChannelStatus($game_id,$status,$ctype) {
		return self::_getDao()->updateChannelStatus($game_id, $status,$ctype);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addChannel($data) {
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
		if(isset($data['ctype'])) $tmp['ctype'] = intval($data['ctype']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
		if(isset($data['game_status'])) $tmp['game_status'] = intval($data['game_status']);
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		return $tmp;
	}

	/**
	 * 
	 * @return Client_Dao_Channel
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Channel");
	}
	
}
