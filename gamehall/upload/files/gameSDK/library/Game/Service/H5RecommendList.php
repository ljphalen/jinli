<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Game_Service_RecommendList
 * @author wupeng
 */
class Game_Service_H5RecommendList {

	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllRecommendList() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
        
        /**
	 * 根据主键查询一条记录
	 * @param int $day_id
	 * @return array
	 */
	public static function getOneByDayId($day_id) {
		if (!$day_id) return null;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->getBy($keyParams);
	}

	/**
	 * 查询记录
	 * @return array
	 */
	public static function getRecommendListsBy($searchParams = array(), $sortParams = array()) {
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
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	/**
	 * 根据主键查询一条记录
	 * @param int $day_id
	 * @return array
	 */
	public static function getRecommendList($day_id) {
		if (!$day_id) return null;		
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->getBy($keyParams);
	}
	
	/**
	 * 根据主键更新一条记录
	 * Enter description here ...
	 * @param array $data
	 * @param int $day_id
	 * @return boolean
	 */
	public static function updateRecommendList($data, $day_id) {
		if (!$day_id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteRecommendListByDayId($day_id) {
        Common_Service_Base::beginTransaction();
        try {
            Game_Service_H5RecommendBanner::deleteRecommendBannerByDayId($day_id);
            Game_Service_H5RecommendEditLog::deleteRecommendEditLogByDayId($day_id);
            Game_Service_H5RecommendNew::deleteRecommendnewByDayId($day_id);

            $recommendList = Game_Service_H5RecommendNew::getRecommendnewsBy(array('day_id' => $day_id));
            if($recommendList) {
                $idList = array();
                foreach ($recommendList as $recommend) {
                    $idList[] = $recommend['id'];
                }
                Game_Service_H5RecommendGames::deleteRecommendGames($idList);
                Game_Service_H5RecommendImgs::deleteRecommendImgsListByRecommend($idList);
                Game_Service_H5RecommendNew::deleteRecommendnewByDayId($day_id);
            }
            $keyParams = array('day_id' => $day_id);
            self::getDao()->deleteBy($keyParams);
            Common_Service_Base::commit();
            return true;
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
        }
        return false;
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addRecommendList($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}
	
	public static function updateGameStatus($gameId, $status) {
	    //更新推荐列表中游戏的状态字段
	    Game_Service_H5RecommendGames::updateGameStatus($gameId, $status);
	}
	
	/**
	 * 专题关闭要更新推荐状态
	 */
	public static function updateSubjectStatus($subjectId, $status) {
	    $searchParams['link_type'] = Game_Service_Util_Link::LINK_SUBJECT;
	    $searchParams['link'] = $subjectId;
	    if($status == Client_Service_Subject::SUBJECT_STATUS_CLOSE) {
	        $data['status'] = Game_Service_H5RecommendBanner::STATUS_CLOSE;
	        Game_Service_H5RecommendBanner::updateRecommendBannerBy($data, $searchParams);
	        $imgList = Game_Service_H5RecommendImgs::getRecommendImgsListBy($searchParams);
	        $recommendIdList = array();
	        foreach ($imgList as $img) {
	            $recommendIdList[] = $img['recommend_id'];
	        }
	        if($recommendIdList) {
	            $searchParams = array('id' => array('IN', $recommendIdList));
	            Game_Service_H5RecommendNew::updateRecommendNewBy($data, $searchParams);
	        }
	    }
	}
	
	
	/**
	 * 检查数据库字段
	 * @param array $data
	 * @return array
	 */
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}
	
	/**
	 * @return Game_Dao_RecommendList
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_H5RecommendList");
	}
	
}
