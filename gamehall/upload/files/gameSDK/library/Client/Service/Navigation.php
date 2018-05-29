<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 客户端按钮导航
 * Client_Service_Navigation
 * @author wupeng
 */
class Client_Service_Navigation {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    
    const WEB_GAME = 'WEB_GAME';
    
    public static $module = array(
        self::WEB_GAME => '网游'
        
    );

	public static function getNavigationListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getNavigationBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getNavigation($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateNavigation($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteNavigation($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteNavigationList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addNavigation($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['module'])) $dbData['module'] = $data['module'];
		if(isset($data['title'])) $dbData['title'] = $data['title'];
		if(isset($data['view_type'])) $dbData['view_type'] = $data['view_type'];
		if(isset($data['icon_url'])) $dbData['icon_url'] = $data['icon_url'];
		if(isset($data['param'])) $dbData['param'] = $data['param'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['module'] = isset($data['module']) ? $data['module'] : "";
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['view_type'] = isset($data['view_type']) ? $data['view_type'] : "";
	    $record['icon_url'] = isset($data['icon_url']) ? $data['icon_url'] : "";
	    $record['param'] = isset($data['param']) ? $data['param'] : "";
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::STATUS_CLOSE;
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Client_Dao_Navigation");
	}

}
