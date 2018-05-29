<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Taste {

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllTaste() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(),$order = array('start_time'=>'DESC','game_id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $order);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function search($page = 1, $limit = 10, $params = array(), $order = array('start_time'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere = Db_Adapter_Pdo::sqlWhere($params);
		if (count($params['game_id'])) $sort = array('FIELD '=>self::quoteInArray($params['game_id'][1]));
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $sort);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 * where id条件特殊拼组
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'`game_id`'.','. implode(', ', $_returns) . ')';
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getTasteGames($params) {
		return self::_getDao()->getsBy($params,array('start_time'=>'DESC','game_id'=>'DESC'));
	}
	
	public static function getCountTasteGames($params) {
		return self::_getDao()->count($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTasteGame($params) {
		return self::_getDao()->getBy($params);
	}
	
	public static function getTasteCount($params) {
       return self::_getDao()->count($params);
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTaste($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateTaste($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateByTastes($data, $params) {
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateTasteTime($time, $start_time) {
		return self::_getDao()->updateBy(array('start_time'=>intval($time),'recovery_time'=>intval($time)), array('start_time'=>intval($start_time)));
	}
	
	
	public static function deleteTasteTime($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateByTasteStatus($status,$game_id) {
		return self::_getDao()->updateBy(array('game_status'=>intval($status)), array('game_id'=>intval($game_id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteTaste($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function turncateTaste() {
		return self::_getDao()->turncateTaste();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addTaste($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return Ambigous <boolean, number>
	 */
	public static function batchAddByTaste($data) {
		$tmp = array();
		foreach($data as $key=>$value) {
			$rets = self::getTasteGames(array('game_id'=>$value));
			if($rets){
				$ret = self::updateByTastes(array('start_time'=>'','status'=> 1),array('game_id'=>$value));
			} else {
				$tmp[] = array(
						'id'=>'',
						'status'=> 1,
						'game_id'=> $value,
						'game_status'=> 1,
						'start_time' => '',
						'recovery_time' => '',
				);
			}
		}
		if($tmp){
			$ret = self::_getDao()->mutiInsert($tmp);
		}
		return $ret;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateTasteStatus($sorts, $status) {
		if (!is_array($sorts)) return false;
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('status'=>$status), $value);
		}
		return true;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['game_status'])) $tmp['game_status'] = intval($data['game_status']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['recovery_time'])) $tmp['recovery_time'] = $data['recovery_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Taste
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Taste");
	}
}
