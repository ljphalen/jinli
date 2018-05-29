<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Sdk_Service_Ad{

    
    const SHOW_TYPE_AD = 1;
    const SHOW_TYPE_ACTIVITY = 2;
    
    const  AD_STATUS_OPEN = 1;
    const  AD_STATUS_CLOSE = 0;
    
    const JOIN_GAME_TYPE_ALL = 0;
    const JOIN_GAME_TYPE_SPECIFY = 1;
    const JOIN_GAME_TYPE_EXCLUDE = 2;
    
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllAd() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy= array('sort'=>'DESC', 'start_time'=>'DESC', 'id'=>'DESC')) {
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
	
	public static function getBy($params = array() , $orderBy=array('sort'=>'DESC','id'=>'DESC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;

	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	
	public static function getsBy($params = array(), $orderBy=array('sort'=>'DESC','id'=>'DESC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	}
	
	public static function getsByWhere($params = array(), $orderBy=array('sort'=>'DESC','id'=>'DESC')){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE %s %s', $this->getTableName(), $where, $sort);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateAd($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortAd($sorts) {
		$time = Common::getTime();
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value, 'version_time'=>$time), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGameAd($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAd($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
	public static function deleteBy($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAd($data) {
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
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['ad_content'])) $tmp['ad_content'] = $data['ad_content'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['game_num'])) $tmp['game_num'] = $data['game_num'];
		if(isset($data['is_payment'])) $tmp['is_payment'] = $data['is_payment'];
		if(isset($data['game_ids'])) $tmp['game_ids'] = $data['game_ids'];
		if(isset($data['show_type'])) $tmp['show_type'] = $data['show_type'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['version_time'])) $tmp['version_time'] = $data['version_time'];
		if(isset($data['join_game_type'])) $tmp['join_game_type'] = $data['join_game_type'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Sdk_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Sdk_Dao_Ad");
	}
}
