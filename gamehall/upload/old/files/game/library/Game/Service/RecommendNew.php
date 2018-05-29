<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Service_RecommendNew
 * @author wupeng
 */
class Game_Service_RecommendNew{
	
	const SHOW_NUM = 6;
	const CACHE_EXPIRE =600;
	const GAME_OPEN_STATUS = 1;
	const GAME_CLOSE_STATUS = 0;
	
	const RECOMMEND_OPEN_STATUS = 1;
	const RECOMMEND_CLOSE_STATUS = 0;
	const RECOMMEND_INVALID_STATUS = -1;

	const PER_PAGE = 10;
	
	const  LESS_SYSTEM_VERSION = 'Android4.1.0';
	const  GREATE_SYSTEM_VERSION = 'Android4.2.0';
	
	const  CLIENT_VERSION_156 = '1.5.6';
	const  CLIENT_VERSION_157 = '1.5.7';
	
    const REC_TYPE_LIST = 0;
    const REC_TYPE_IMAGE = 1;
    public static $rec_type = array(
        self::REC_TYPE_LIST => "合集", 
        self::REC_TYPE_IMAGE => "图片"
    );
    
    const LIST_HORIZONTAL = 0;
    const LIST_VERTICAL = 1;
    public static $list_template = array(
        self::LIST_HORIZONTAL => "横版",
        self::LIST_VERTICAL => "竖版"
    );
    
    
	public static function getRecommendnewsBy($searchParams, $sortParams = array('sort' => 'DESC', 'id' => 'ASC')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getRecommendnewBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getRecommendnew($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params, $orderBy);
	}

	public static function getsBy($params, $orderBy = array('sort' => 'DESC', 'id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	public static function updateRecommendnew($data, $id) {
		if (!is_array($data)) return false;
		$data = self::checkField($data);
		return self::getDao()->update($data, intval($id));
	}

	public static function deleteRecommendnewByRecommendList($idList) {
	    return self::getDao()->deletes('id', $idList);
	}
	
	public static function deleteRecommendnew($id) {
	    return self::deleteRecommendnewList(array($id));
	}
	
	public static function deleteRecommendnewList($idList) {
		if (!is_array($idList)) return false;
        Common_Service_Base::beginTransaction();
        $ret = Game_Service_RecommendGames::deleteRecommendGames($idList);
        if (!$ret) {
            Common_Service_Base::rollBack();
            return false;
        }
        $ret = Game_Service_RecommendImgs::deleteRecommendImgsListByRecommend($idList);
        if (!$ret) {
            Common_Service_Base::rollBack();
            return false;
        }
		$ret = self::getDao()->deletes("id", $idList);
		if (!$ret) {
		    Common_Service_Base::rollBack();
		    return false;
		}
		Common_Service_Base::commit();
		return true;
	}

	public static function deleteRecommendnewByDayId($day_id) {
	    $keyParams = array('day_id' => $day_id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	public static function addRecommendnew($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		$ret = self::getDao()->insert($data);
		if($ret) {
	        return self::getDao()->getLastInsertId();
		}
		return false;
	}
	
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
	    $dbData = self::checkField($data);
	    if (!is_array($dbData)) return false;
	    return self::getDao()->updateBy($dbData, $searchParams);
	}
	
	private static function checkNewField($data) {
	    $record = array();
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['content'] = isset($data['content']) ? $data['content'] : "";
	    $record['start_time'] = isset($data['start_time']) ? $data['start_time'] : 0;
	    $record['end_time'] = isset($data['end_time']) ? $data['end_time'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::RECOMMEND_CLOSE_STATUS;
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
	    $record['pgroup'] = isset($data['pgroup']) ? $data['pgroup'] : 0;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['rec_type'] = isset($data['rec_type']) ? $data['rec_type'] : 0;
	    $record['list_tpl'] = isset($data['list_tpl']) ? $data['list_tpl'] : 0;
		return $record;
	}
	
	private static function checkField($data) {
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
		if(isset($data['list_tpl'])) $tmp['list_tpl'] = $data['list_tpl'];
		return $tmp;
	}
    
	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendNew");
	}
	
}
