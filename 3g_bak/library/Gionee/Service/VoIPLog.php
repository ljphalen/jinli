<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_VoIPLog {

	public static function add($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}

	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params, $order=array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $order);
	}


	//获取数据
	public static function getList($page, $pageSize, $where, $sort) {
		$page = max(1, $page);
		return array(self::_getDao()->count($where), self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $sort));
	}

	public static function updateBy($params, $where) {
		return self::_getDao()->updateBy($params, $where);
	}

	public static function update($params, $id) {
		return self::_getDao()->update($params, $id);
	}

	//
	public static function delete($id) {
		if (!is_numeric($id)) return false;
		return self::_getDao()->delete($id);
	}

	public static function getTotalTimeByMonth($mobile, $m = '') {
		if (empty($mobile)) {
			return 0;
		}
		$row = self::_getDao()->getTotalTimeByMonth($mobile, $m);
		return isset($row['num']) ? intval($row['num']) : 0;
	}

	public static function getCensusDataByDate($page, $pageSize, $where = array(), $group = array(), $order) {
		if (!is_array($where) || !is_array($group)) return false;
		$data  = self::_getDao()->getCensusDataByDate(($page - 1) * $pageSize, $pageSize, $where, $group, $order);
		$total = count($data);
		return array($total, $data);
	}

	public static function getCommutniateList($page = 1, $pageSize = 20, $where = array(), $group = array(), $order = array()) {
		if (!is_array($where)) return false;
		return self::_getDao()->getCommutniateList($pageSize * ($page - 1), $pageSize, $where, $group, $order);
	}

	///获得单个用户拔打次数信息
	public static function getPerUserCallTimesInfo($page,$pageSize,$where =array(),$order=array())
	{
		if(!is_array($where)) return false;
		return self::_getDao()->getPerUserCallTimesInfo($pageSize*($page-1),$pageSize,$where,$order);
	}

	public static function getCalledStat($m, $called=false) {
		return self::_getDao()->getCalledStat($m, $called);
	}

	public static function getDurationTimeByHour($st,$et) {
		return self::_getDao()->getDurationTimeByHour($st,$et);
	}

	/**
	 * @return Gionee_Dao_VoIPLog
	 */
	private static function  _getDao() {
		return Common::getDao('Gionee_Dao_VoIPLog');
	}
}
