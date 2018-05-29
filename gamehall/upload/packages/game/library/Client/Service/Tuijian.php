<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Tuijian{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllTuijian() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSortTuijian() {
		return self::_getDao()->getAll(array('sort'=>'desc', 'id' => 'desc'));
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
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getCanUseTuijian($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$time = Common::getTime();
		$params['status']=1;
		$params['start_time']= array('<', $time);
		$params['end_time'] = array('>', $time);
		
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort' => 'desc','id' => 'desc'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTuijian($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateTuijian($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addTuijians($data,$start_time,$end_time) {
		$tmp = $v = array();
		$time = Common::getTime();
		foreach($data as $key=>$value) {
			$v = explode("|",$value);
			$tmp[] = array(
					'id'=>'',
					'sort'=> 0,
					'status'=>1,
					'ntype'=>$v[2],
					'n_id'=>$v[0],
					'start_time'=>$start_time,
					'end_time'=>$end_time,
					'update_time'=>$time
			);
		}
		return self::_getDao()->mutiInsert($tmp);
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortTuijians($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getTuijiansByNId($n_id) {
		if (!$n_id) return false;
		return self::_getDao()->getBy(array('n_id'=>$n_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTuijianTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteTuijian($id) {
		if (!$id) return false;
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchTuijian($data) {
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
	public static function updateBatchTuijian($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchTuijian($ids, $status);
	}
	
	
	public static function updateTuijianStatus($id,$status) {
		return self::_getDao()->updateBy(array('status'=>$status), array('id'=>$id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addTuijian($data) {
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
		if(isset($data['n_id'])) $tmp['n_id'] = $data['n_id'];
		if(isset($data['ntype'])) $tmp['ntype'] = $data['ntype'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Tuijian
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Tuijian");
	}
}
