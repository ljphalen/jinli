<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐banner图
 * Game_Service_RecommendBanner
 * @author wupeng
 */
class Game_Service_H5RecommendRank {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    const INDEX_RANK_SHOWNUM = 5;
    
	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllRecommendRank() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}

	/**
	 * 查询记录
	 * @return array
	 */
	public static function getRecommendRanksBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    $searchParams['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
	    return self::getDao()->getsBy($searchParams, $sortParams);
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
		$searchParams['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	/**
	 * 根据主键查询一条记录
	 * @param int $id
	 * @return array
	 */
	public static function getRecommendRank($id) {
		if (!$id) return null;
		$keyParams = array('id' => $id, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID());
		return self::getDao()->getBy($keyParams);
	}
	
	public static function getRankByRecommendId($id) {
	    if (!$id) return null;
	    $keyParams = array('recommend_id' => $id, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID());
	    return self::getDao()->getBy($keyParams);
	}
	
	/**
	 * 根据主键更新一条记录
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateRecommendRank($data, $id) {
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
	public static function deleteRecommendRank($id) {
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
	public static function deleteRecommendRankList($keyList) {
		if (!is_array($keyList)) return false;
		Game_Service_H5RecommendDelete::deleteAllRowByIdList(self::getDao()->getTableName(), $keyList);
		return self::getDao()->deletes('id', $keyList);
	}
	
	public static function deleteRanksListByRecommend($idList) {
	    if (!is_array($idList)) return false;
	    Game_Service_H5RecommendDelete::deleteAllRowByRecommendIdList(self::getDao()->getTableName(), $idList);
	    return self::getDao()->deletes("recommend_id", $idList);
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addRecommendRank($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		$ret = self::getDao()->insert($dbData);
		if($ret) {
		    return self::getDao()->getLastInsertId();
		}
		return false;
	}
	
	public static function addMutiRecommendRank($list) {
	    if (! $list) return false;
	    return self::getDao()->mutiFieldInsert($list);
	}
	
	public static function updateRecommendRankBy($data, $searchParams) {
	    $dbData = self::checkField($data);
	    if (!is_array($dbData)) return false;
	    return self::getDao()->updateBy($dbData, $searchParams);
	}
	
	/**
	 * 检查数据库字段
	 * @param array $data
	 * @return array
	 */
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['rank_type'])) $dbData['rank_type'] = $data['rank_type'];
		if(isset($data['recommend_id'])) $dbData['recommend_id'] = $data['recommend_id'];
		$dbData['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
		return $dbData;
	}

	/**
	 * @return Game_Dao_RecommendBanner
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_H5RecommendRank");
	}
	
}
