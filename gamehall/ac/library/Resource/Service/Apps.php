<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_Apps extends Common_Service_Base{

	
	/**
	 * 根据条件检索
	 */
	public static function getsBy($params, $orderBy=array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
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
	 * 获取最后添加的ID
	 * @return string
	 */
	public static function getLastInsertId(){
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 * 添加应用
	 * @param unknown $data
	 * @throws Exception
	 * @return boolean
	 */
	public static function addApps($data) {
		if (!is_array($data)) return false;
		$appData = self::_cookData($data);
		return self::_getDao()->insert($appData);
	}
	
	/**
	 * 更新应用
	 * @param unknown $data
	 * @param unknown $id
	 * @throws Exception
	 * @return boolean
	 */
	public static function updateApps($data) {
		if (!is_array($data)) return false;
		$appId=intval($data['id']);
		//更新应用基本数据
		$appData = self::_cookData($data);
		$ret = self::_getDao()->update($appData, $appId);
		if (!$ret) return false;
		self::upReleation($appId);
		return true;
	}
	
	/**
	 * 上下线应用
	 * @param unknown $data
	 * @param unknown $id
	 * @throws Exception
	 * @return boolean
	 */
	public static function updateStatus($data) {
		if (!is_array($data)) return false;
		$appId=intval($data['id']);
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新应用基本状态
			$appData = self::_cookData($data);
			$ret = self::_getDao()->update($appData, $appId);
			if (!$ret) throw new Exception('Update App Resource fail.', -205);
			//应用下线操作
			if($appData['status'] == 0){
				//删除应用分类索引数据
				Resource_Service_IdxAppsCategory::deleteBy(array('app_id' => $appId));
				//删除应用机型索引数据
				Resource_Service_IdxAppsPgroup::deleteBy(array('app_id' => $appId));
			}
			//上线状态数据联动变更
			if($appData['status'] == 1){
				Resource_Service_IdxAppsPgroup::upIdxTime($appId, $data['create_time']);
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
	 * 检测app否为上线状态来更新关联数据
	 * @param unknown $appId
	 */
	public static function upReleation($appId){
		$app = self::get(array('id'=>$appId));
		$time = Common::getTime();
		if($app['status'] == 1){
			Resource_Service_IdxAppsPgroup::upIdxTime($appId, $time);
		}
	}
	
	/**
	 * 更新基本数据
	 * @param unknown $data
	 * @return boolean
	 */
	public static function updateBy($data, $params){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->updateBy($data, $params);
		if (!$ret) return false;
		$appId = $params['id'];
		self::upReleation($appId);
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteApps($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['score'])) $tmp['score'] = $data['score'];
		if(isset($data['belong'])) $tmp['belong'] = $data['belong'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['imgId'])) $tmp['icon'] = $data['imgId'];
		if(isset($data['min_os'])) $tmp['min_os'] = $data['min_os'];
		if(isset($data['class'])) $tmp['class'] = $data['class'];
		if(isset($data['package'])) {
			$tmp['package'] = $data['package'];
			$tmp['packagecrc'] = crc32(trim($data['package']));
		}
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['version_code'])) $tmp['version_code'] = $data['version_code'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}

	/**
	 *
	 * @return Resource_Dao_Apps
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Apps");
	}
	
}
