<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 异步任务
 * Resource_Service_AsynTask
 * @author wupeng
 */
class Resource_Service_AsynTask {

	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllAsynTask() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}

	/**
	 * 查询列表
	 * @return array
	 */
	public static function getAsynTaskListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	/**
	 * 查询单条记录
	 * @return array
	 */
	public static function getAsynTaskBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	/**
	 * 分页查询
	 * @param int $page
	 * @param int $limit
	 * @param array $searchParams
	 * @param array $sortParams
	 * @return array
	 */	 
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	/**
	 * 根据主键查询一条记录
	 * @param int $id
	 * @return array
	 */
	public static function getAsynTask($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	/**
	 * 根据主键更新一条记录
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateAsynTask($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	/**
	 * 根据主键删除一条记录
	 * @param int $id
	 * @return boolean
	 */
	public static function deleteAsynTask($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	/**
	 * 根据主键删除多条记录
	 * @param array $keyList
	 * @return boolean
	 */
	public static function deleteAsynTaskList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addAsynTask($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}
	
	/**
	 * 检查数据库字段
	 * @param array $data
	 * @return array
	 */
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['task_id'])) $dbData['task_id'] = $data['task_id'];
		if(isset($data['task'])) $dbData['task'] = $data['task'];
		if(isset($data['method'])) $dbData['method'] = $data['method'];
		if(isset($data['args'])) $dbData['args'] = $data['args'];
		if(isset($data['start_time'])) $dbData['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $dbData['end_time'] = $data['end_time'];
		if(isset($data['use_time'])) $dbData['use_time'] = $data['use_time'];
		if(isset($data['result'])) $dbData['result'] = $data['result'];
		return $dbData;
	}

	/**
	 * @return Resource_Dao_AsynTask
	 */
	private static function getDao() {
		return Common::getDao("Resource_Dao_AsynTask");
	}
	
}
