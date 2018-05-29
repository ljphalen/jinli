<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_GiftGrabLog {
	
    const STATUS_NONE = 0;
    const STATUS_LOCK = 1;
    const STATUS_SENDED = 2;
    
	public static function getBy($params) {
	    return self::getDao()->getBy($params);
	}
	
	public static function count($params) {
	    return self::getDao()->count($params);
	}
	
	public static function update($data, $id) {
	    return self::getDao()->update($data, $id);
	}
	
	public static function updateBy($data, $params) {
	    return self::getDao()->updateBy($data, $params);
	}
	
	public static function install($data) {
	    return self::getDao()->insert($data);
	}
	
	/**
	 * 获取用户礼包数量
	 * @param unknown $uuid
	 * @return Ambigous <boolean, unknown, number>
	 */
	public static function userCodeCount($uuid) {
	    $codeQureyParams = array(
	                    'owner_uuid' => $uuid,
	                    'status' => self::STATUS_SENDED
	    );
	    return self::getDao()->count($codeQureyParams);
	}
	
	public static function getUserLogs($uuid) {
	    $codeQureyParams = array(
	                    'owner_uuid' => $uuid,
	                    'status' => self::STATUS_SENDED
	    );
	    return self::getDao()->getsBy($codeQureyParams, array('update_time'=>'DESC'));
	}
	
	public static function getList($page = 1, $perpage = 20, $params = array(), $orderBy = array()) {
	    if ($page < 1) {
	        $page = 1;
	    }
	    $start = ($page -1) * $perpage;
	    $list = self::getDao()->getList(intval($start), intval($perpage), $params, array('id'=>'DESC'));
	    $total = self::getDao()->count($params);
	    return array($total, $list);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_GiftGrabLog");
	}
}