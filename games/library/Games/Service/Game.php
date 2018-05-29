<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Games_Service_Game{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGame() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGame($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * get detail by package
	 * @param unknown_type $pacakge
	 * @return boolean|mixed
	 */
	public static function getByPackage($package) {
		if(!$package) return false;
		return self::_getDao()->where(array('package'=>$package));
	}
	
	/**
	 * 
	 * @param array $ids
	 */
	public function getGames($ids) {
		if (!is_array($ids)) return array();
		return self::_getDao()->gets($ids);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGame($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['update_time'] = Common::getTime();
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGame($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGame($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['update_time'] = Common::getTime();
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
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['ptype'])) $tmp['ptype'] = intval($data['ptype']);
		if(isset($data['pay_type'])) $tmp['pay_type'] = intval($data['pay_type']);
		if(isset($data['subject'])) $tmp['subject'] = intval($data['subject']);
		if(isset($data['downloads'])) $tmp['downloads'] = intval($data['downloads']);
		if(isset($data['language'])) $tmp['language'] = $data['language'];
		if(isset($data['package'])) $tmp['package'] = $data['package'];
		if(isset($data['activity'])) $tmp['activity'] = $data['activity'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['company'])) $tmp['company'] = $data['company'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = intval($data['sys_version']);
		if(isset($data['min_resolution'])) $tmp['min_resolution'] = intval($data['min_resolution']);
		if(isset($data['max_resolution'])) $tmp['max_resolution'] = intval($data['max_resolution']);
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Games_Dao_Game
	 */
	private static function _getDao() {
		return Common::getDao("Games_Dao_Game");
	}
}
