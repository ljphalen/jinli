<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Service_AppsVersion
 * @author fanch
 *
 */
class Resource_Service_AppsVersion extends Common_Service_Base{
	/**
	 * 根据条件检索
	 */
	public static function getsBy($params, $orderBy=array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 *
	 * @param unknown $id
	 * @return boolean|multitype:
	 */
	public static function get($id){
		if (!$id) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加应用版本
	 * @param unknown $data
	 * @throws Exception
	 * @return boolean
	 */
	public static function addVersion($data) {
		//增加到游戏内容库中
		if (!is_array($data)) return false;
		$versionData = self::_cookData($data);
		$appId = $versionData['app_id'];
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    $ret = self::_getDao()->insert($versionData);
		    $versionId = self::_getDao()->getLastInsertId();
		    if (!$ret) throw new Exception('add App Version fail.', -205);
		    if($data['status'] == 1){
		    	//更新应用内容库版本信息
		    	$appData = array(
		    			'link'=> $data['link'],
		    			'package' => $data['package'],
		    			'version' => $data['version'],
		    			'version_code' => $data['version_code'],
		    			'size' => $data['size']
		    	);
		    	Resource_Service_Apps::updateBy($appData, array('id' => $appId));
		    	//更新同一个应用下的其他版本状态为下线
		    	$ret = self::_getDao()->updateBy(array('status' => 0), array('id' => array('!=', $versionId), 'app_id' => $appId));
		    }
		    //事务提交
		    if($trans) return parent::commit();
		return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 更新版本数据
	 * @param unknown $data
	 * @param unknown $id
	 * @throws Exception
	 * @return boolean
	 */
	public static function updateVersion($data) {
		if (!is_array($data)) return false;
		$versionId = intval($data['id']);
		$appId = intval($data['app_id']);
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新应用基本数据
			$versionData = self::_cookData($data);
			$ret = self::_getDao()->update($versionData, $versionId);
			if (!$ret) throw new Exception('Update App Version fail.', -205);
			if($data['status'] == 1){
				//更新应用内容库版本信息
				$appData = array(
						'link'=> $data['link'],
						'package' => $data['package'],
						'version' => $data['version'],
						'version_code' => $data['version_code'],
						'size' => $data['size'],
						'create_time' => $data['create_time']
				);
				Resource_Service_Apps::updateBy($appData, array('id' => $appId));
				//更新同一个应用下的其他版本状态为下线
				$ret = self::_getDao()->updateBy(array('status' => 0), array('id' => array('!=', $versionId), 'app_id' => $appId));
			}
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['app_id'])) $tmp['app_id'] = $data['app_id'];
		if(isset($data['package'])) $tmp['package'] = $data['package'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['version_code'])) $tmp['version_code'] = $data['version_code'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	/**
	 *
	 * @return Resource_Dao_Apps
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_AppsVersion");
	}
}