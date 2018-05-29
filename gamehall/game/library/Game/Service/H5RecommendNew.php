<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Service_RecommendNew
 * @author wupeng
 */
class Game_Service_H5RecommendNew{
	
	const SHOW_NUM = 6;
	const CACHE_EXPIRE =600;
	const GAME_OPEN_STATUS = 1;
	const GAME_CLOSE_STATUS = 0;
	
	const RECOMMEND_OPEN_STATUS = 1;
	const RECOMMEND_CLOSE_STATUS = 0;
	const RECOMMEND_INVALID_STATUS = -1;

	const PER_PAGE = 10;
        
	const QUERYGAME = '/Admin/Ad_Recommendnew/queryGame';
	
    const REC_TYPE_LIST = 0;
    const REC_TYPE_IMAGE = 1;
    const REC_TYPE_RANK = 2;
    const REC_TYPE_NEW = 3;
    const REC_TYPE_ACTIVE = 4;
    
    public static $rec_type = array(
        self::REC_TYPE_LIST => "合集", 
        self::REC_TYPE_IMAGE => "图片",
        self::REC_TYPE_RANK => "排行榜",
        self::REC_TYPE_NEW => "资讯",
        self::REC_TYPE_ACTIVE => "活动",
    );
    public static $m_h5_index_type = array(
        self::REC_TYPE_LIST => "collection", 
        self::REC_TYPE_IMAGE => "advertising",
        self::REC_TYPE_RANK => "ranking",
        self::REC_TYPE_NEW => "news",
        self::REC_TYPE_ACTIVE => "activity",
    );
    
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllRecommendnew() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}

	public static function getRecommendnewsBy($searchParams, $sortParams = array('sort' => 'DESC', 'id' => 'ASC')) {
	    $searchParams = array_merge($searchParams, array('tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getRecommendnewBy($searchParams, $sortParams = array()) {
	    $searchParams = array_merge($searchParams, array('tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$params = array_merge($params, array('tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
		$ret = self::getDao()->getList($start, $limit, $params, $sort);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getRecommendList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$params = array_merge($params, array('tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
		$ret = self::getDao()->getList($start, $limit, $params, $sort);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRecommendnew($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		$params = array_merge($params, array('tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
		return self::getDao()->getBy($params, $orderBy);
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('sort' => 'DESC', 'id'=>'ASC')) {
		if (!is_array($params)) return false;
		$params = array_merge($params, array('tmp_id' => Game_Service_H5HomeAutoHandle::getID()));
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRecommendnew($data, $id) {
		if (!is_array($data)) return false;
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteRecommendnew($id) {
	    return self::deleteRecommendnewList(array($id));
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, unknown, number>
	 */
	public static function deleteRecommendnewList($idList) {
		if (!is_array($idList)) return false;
        Common_Service_Base::beginTransaction();
        $ret = Game_Service_H5RecommendGames::deleteRecommendGames($idList);
        if (!$ret) {
            Common_Service_Base::rollBack();
            return false;
        }
        $ret = Game_Service_H5RecommendImgs::deleteRecommendImgsListByRecommend($idList);
        if (!$ret) {
            Common_Service_Base::rollBack();
            return false;
        }
        $ret = Game_Service_H5RecommendRank::deleteRanksListByRecommend($idList);
        if (!$ret) {
            Common_Service_Base::rollBack();
            return false;
        }
        $ret = Game_Service_H5RecommendNewHd::deleteNewHdListByRecommend($idList);
        if (!$ret) {
            Common_Service_Base::rollBack();
            return false;
        }
		$ret = self::getDao()->deletes("id", $idList);
		Game_Service_H5RecommendDelete::deleteAllRowByIdList(self::getDao()->getTableName(), $idList);
		if (!$ret) {
		    Common_Service_Base::rollBack();
		    return false;
		}
		Common_Service_Base::commit();
		return true;
	}

	public static function deleteRecommendnewByDayId($day_id) {
	    $keyParams = array('day_id' => $day_id);
	    Game_Service_H5RecommendDelete::deleteOneRowByDayId(self::getDao()->getTableName(), $day_id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRecommendnew($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::getDao()->insert($data);
		if($ret) {
		        return self::getDao()->getLastInsertId();
		}
		return false;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateListSort($idList, $sortList) {
	    if (!is_array($idList) || !is_array($sortList)) return false;
	    Common_Service_Base::beginTransaction();
	    foreach ($idList as $id) {
	        $ret = self::getDao()->update(array('sort' => $sortList[$id]), $id);
	        if (!$ret) {
	            Common_Service_Base::rollBack();
	            return false;
	        }
	    }
	    Common_Service_Base::commit();
	    return true;
	}

	public static function updateRecommendNewBy($data, $searchParams) {
	    return self::getDao()->updateBy($data, $searchParams);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['pgroup'])) $tmp['pgroup'] = $data['pgroup'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['day_id'])) $tmp['day_id'] = $data['day_id'];
		if(isset($data['rec_type'])) $tmp['rec_type'] = $data['rec_type'];
		$tmp['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
		return $tmp;
	}
    
	/**
	 * 
	 * @return Game_Dao_RecommendNew
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_H5RecommendNew");
	}
	
}
