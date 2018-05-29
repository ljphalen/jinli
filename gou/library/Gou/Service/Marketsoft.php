<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Gou_Service_Marketsoft
 * @author huangsg
 */
class Gou_Service_Marketsoft {
	
	/**
	 * 获取所有软件信息
	 */
	public static function getAllSoft(){
		return self::_getDao()->getsBy(array('status'=>1), array('id'=>'desc'));
	}
	
	/**
	 * 获取软件按信息列表
	 * @param number $page
	 * @param number $limit
	 * @param array $params
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取一条信息
	 * @param integer $id
	 * @return boolean
	 */
	public static function getOneSoft($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加软件信息
	 * @param array $data
	 * @return boolean
	 */
	public static function addSoft($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 更新软件信息
	 * @param array $data
	 * @param integer $id
	 * @return boolean
	 */
	public static function updateSoft($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 删除软件信息
	 * @param integer $id
	 */
	public static function deleteSoft($id){
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 添加软件信息时，检查apk_md5字段值是否重复
	 * @param string $apkMd5
	 */
	public static function checkApkMd5($apkMd5){
		if (empty($apkMd5)) return false;
		$result = self::_getDao()->count(array('apk_md5'=>$apkMd5));
		return !empty($result) ? true : false;
	}
	
	/**
	 *
	 * 参数过滤
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['package'])) $tmp['package'] = strval($data['package']);
		if(isset($data['apk'])) $tmp['apk'] = strval($data['apk']);
		if(isset($data['version'])) $tmp['version'] = strval($data['version']);
		if(isset($data['download_url'])) $tmp['download_url'] = strval($data['download_url']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['apk_md5'])) $tmp['apk_md5'] = strval($data['apk_md5']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Gou_Service_Marketsoft
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Marketsoft");
	}
}