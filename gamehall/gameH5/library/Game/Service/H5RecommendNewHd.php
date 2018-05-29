<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐banner图
 * Game_Service_RecommendBanner
 * @author wupeng
 */
class Game_Service_H5RecommendNewHd {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    

	/**
	 * 查询新闻记录
	 * @return array
	 */
	public static function getRecommendNewBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    $searchParams = array_merge($searchParams, array('list_type' => '1', 'tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	/**
	 * 查询活动记录
	 * @return array
	 */
	public static function getRecommendHdBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    $searchParams = array_merge($searchParams, array('list_type' => '2', 'tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getNewsByRecommendId($recommendid) {
	    return self::getDao()->getList(0, 5, array('recommend_id' => $recommendid, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
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
	 * 根据recommend_id查询一条记录
	 * @param int $id
	 * @return array
	 */
	public static function getNewHdByRecommendId($id) {
		if (!$id) return null;
		$keyParams = array('recommend_id' => $id, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID());
		return self::getDao()->getList('0', '5', $keyParams);
	}
	
	/**
	 * 根据主键更新一条记录
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateRecommendInfo($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID());
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	/**
	 * 根据主键删除一条记录
	 * @param int $id
	 * @return boolean
	 */
	public static function deleteRecommendNewHd($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID());
		Game_Service_H5RecommendDelete::deleteOneRowById(self::getDao()->getTableName(), $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	/**
	 * 根据主键删除多条记录
	 * @param array $keyList
	 * @return boolean
	 */
	public static function deleteRecommendNewHdList($keyList) {
		if (!is_array($keyList)) return false;
		Game_Service_H5RecommendDelete::deleteAllRowByIdList(self::getDao()->getTableName(), $keyList);
		return self::getDao()->deletes('id', $keyList);
	}
	
	public static function deleteNewHdListByRecommend($idList) {
	    if (!is_array($idList)) return false;
	    Game_Service_H5RecommendDelete::deleteAllRowByRecommendIdList(self::getDao()->getTableName(), $idList);
	    return self::getDao()->deletes("recommend_id", $idList);
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addRecommendInfo($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}
	
	public static function addMutiRecommendInfo($list) {
	    if (!$list) return false;
	    if(!self::checkMutiInfo($list)) return false;
	    return self::getDao()->mutiFieldInsert($list);
	}
	
	public static function updateRecommendInfoBy($data, $searchParams) {
	    return self::getDao()->updateBy($data, $searchParams);
	}
	
	private static function checkMutiInfo($data) {
		foreach($data as $key => $value) {
			if($value['list_type'] == 'new') {
				$info = Client_Service_News::getNews($value['list_id']);
			} else {
				$info = Client_Service_Hd::getHd($value['list_id']);
			}
			if($info['status'] < 1) {
				return false;
			}
		}
                return true;
	}
	
	public static function updateMutiRecommendInfo($data) {
            if(!self::checkMutiInfo($data)) return false;
	    foreach($data as $key => $value) {
	        self::updateRecommendInfoBy($value, array('recommend_id' => $value['recommend_id'], 'list_sort' => $value['list_sort'], 'tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
	    }
            return true;
	}
	
	/**
	 * 检查数据库字段
	 * @param array $data
	 * @return array
	 */
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['list_type'])) $dbData['list_type'] = $data['list_type'];
		if(isset($data['list_id'])) $dbData['list_id'] = $data['list_id'];
		if(isset($data['list_title'])) $dbData['list_title'] = $data['list_title'];
		if(isset($data['recommend_id'])) $dbData['recommend_id'] = $data['recommend_id'];
		$dbData['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
		return $dbData;
	}

	/**
	 * @return Game_Dao_RecommendBanner
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_H5RecommendNewHd");
	}
	
}
