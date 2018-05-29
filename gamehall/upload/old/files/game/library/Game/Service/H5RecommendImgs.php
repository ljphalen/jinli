<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐图片
 * Game_Service_RecommendImgs
 * @author wupeng
 */
class Game_Service_H5RecommendImgs {

	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllRecommendImgs() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}

	/**
	 * 查询列表
	 * @return array
	 */
	public static function getRecommendImgsListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	/**
	 * 查询单条记录
	 * @return array
	 */
	public static function getRecommendImgsBy($searchParams, $sortParams = array()) {
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
	public static function getRecommendImgs($id) {
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
	public static function updateRecommendImgs($data, $id) {
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
	public static function deleteRecommendImgs($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		Game_Service_H5RecommendDelete::deleteOneRowById(self::getDao()->getTableName(), $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	/**
	 * 根据主键删除多条记录
	 * @param array $keyList
	 * @return boolean
	 */
	public static function deleteRecommendImgsList($keyList) {
		if (!is_array($keyList)) return false;
		Game_Service_H5RecommendDelete::deleteAllRowByIdList(self::getDao()->getTableName(), $keyList);
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteRecommendImgsListByRecommend($idList) {
		if (!is_array($idList)) return false;
		Game_Service_H5RecommendDelete::deleteAllRowByRecommendIdList(self::getDao()->getTableName(), $keyList);
	    return self::getDao()->deletes("recommend_id", $idList);
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addRecommendImgs($data) {
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
		if(isset($data['recommend_id'])) $dbData['recommend_id'] = $data['recommend_id'];
		if(isset($data['link_type'])) $dbData['link_type'] = $data['link_type'];
		if(isset($data['link'])) $dbData['link'] = $data['link'];
		if(isset($data['img'])) $dbData['img'] = $data['img'];
		$dbData['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
		return $dbData;
	}

	/**
	 * @return Game_Dao_RecommendImgs
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_H5RecommendImgs");
	}
	
}
