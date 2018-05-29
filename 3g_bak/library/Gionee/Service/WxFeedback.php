<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信反馈类
 */
class Gionee_Service_WxFeedback {

	public static function getList($page = 1, $limit = 10, $params = array(), $orderby = array()) {
		$start = (max($page, 1) - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderby);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	public static  function count($params=array()){
		return self::_getDao()->count($params);
	}
	/**
	 * @param int $id
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	public static function getsBy($where = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($where, $orderBy);
	}

	public static function getBy($where, $orderBy = array()) {
		return self::_getDao()->getBy($where, $orderBy);
	}

	/**
	 * @param array $data
	 * @param int $id
	 */
	public static function set($data, $id) {
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * @param array $ids
	 * @param array $data
	 */
	public static function sets($ids, $data) {
		if (!is_array($data) || !is_array($ids)) {
			return false;
		}
		return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 * @param int $id
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		$ret = self::_getDao()->insert($data);
		if ($ret) {
			return self::_getDao()->getLastInsertId();
		}
		return false;
	}

	
	/**
	 * 获得关注送积分统计信息
	 */
	public static function getRewardsData($where =array(),$orderBy= array(),$page,$pageSize=20){
		if(!is_array($where)) return false;
		if($page<1){
			$page = 1;
		}
		$result = array();
		$data =  self::_getDao()->getRewardsData($where,$orderBy,$pageSize*($page-1),$pageSize);
		foreach ($data as $k=>$v){
			$result[$v['get_date']] = $v['total'];
		}
		return $result;
	}
	
	
	/**
	 * @return Gionee_Dao_WxFeedback
	 */
	public static function _getDao() {
		return Common::getDao("Gionee_Dao_WxFeedback");
	}
}