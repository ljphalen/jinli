<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * vip成长值
 * User_Service_VipExpr
 * @author wupeng
 */
class User_Service_VipExpr {
	
	const F_ID = 'id';
	const F_UUID = 'uuid';
	const F_TYPE = 'type';
	const F_ADD_EXPR = 'add_expr';
	const F_EXPR = 'expr';
	const F_LOGS = 'logs';
	const F_CREATE_TIME = 'create_time';
	
    /**消费*/
    const TYPE_MONEY = 1;
    /**活动*/
    const TYPE_ACTIVITY = 2;

	public static function getVipExprListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getVipExprBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array(self::F_CREATE_TIME => 'desc')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getVipExpr($id) {
		if (!$id) return null;		
		$keyParams = array(self::F_ID => $id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateVipExpr($data, $id) {
		if (!$id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array(self::F_ID => $id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateVipExprBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteVipExpr($id) {
		if (!$id) return false;
		$keyParams = array(self::F_ID => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteVipExprList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes(self::F_ID, $keyList);
	}

	public static function addVipExpr($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiVipExpr($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	private static function checkNewField($data) {
	    $record = array();
		$record[self::F_UUID] = isset($data[self::F_UUID]) ? $data[self::F_UUID] : "";
		$record[self::F_TYPE] = isset($data[self::F_TYPE]) ? $data[self::F_TYPE] : 0;
		$record[self::F_ADD_EXPR] = isset($data[self::F_ADD_EXPR]) ? $data[self::F_ADD_EXPR] : 0;
		$record[self::F_EXPR] = isset($data[self::F_EXPR]) ? $data[self::F_EXPR] : 0;
		$record[self::F_LOGS] = isset($data[self::F_LOGS]) ? $data[self::F_LOGS] : "";
		$record[self::F_CREATE_TIME] = isset($data[self::F_CREATE_TIME]) ? $data[self::F_CREATE_TIME] : 0;
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data[self::F_ID])) {
			$record[self::F_ID] = $data[self::F_ID];
		}
		if(isset($data[self::F_UUID])) {
			$record[self::F_UUID] = $data[self::F_UUID];
		}
		if(isset($data[self::F_TYPE])) {
			$record[self::F_TYPE] = $data[self::F_TYPE];
		}
		if(isset($data[self::F_ADD_EXPR])) {
			$record[self::F_ADD_EXPR] = $data[self::F_ADD_EXPR];
		}
		if(isset($data[self::F_EXPR])) {
			$record[self::F_EXPR] = $data[self::F_EXPR];
		}
		if(isset($data[self::F_LOGS])) {
			$record[self::F_LOGS] = $data[self::F_LOGS];
		}
		if(isset($data[self::F_CREATE_TIME])) {
			$record[self::F_CREATE_TIME] = $data[self::F_CREATE_TIME];
		}
		return $record;
	}

	private static function getDao() {
		return Common::getDao("User_Dao_VipExpr");
	}

}
