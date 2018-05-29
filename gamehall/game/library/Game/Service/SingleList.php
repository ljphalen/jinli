<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 单机推荐
 * Game_Service_SingleList
 * @author wupeng
 */
class Game_Service_SingleList {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;

    const LIST_HORIZONTAL = 0;
    const LIST_VERTICAL = 1;
    
    public static $list_template = array(
        self::LIST_HORIZONTAL => "横版",
        self::LIST_VERTICAL => "竖版"
    );
    public static $list_template_counts = array(
        self::LIST_HORIZONTAL => 6,
        self::LIST_VERTICAL => 5
    );

    const REC_TYPE_LIST = 0;
    const REC_TYPE_IMAGE = 1;
    const REC_TYPE_GIFT = 2;
    const REC_TYPE_ALONE = 3;
    public static $rec_type = array(
        self::REC_TYPE_LIST => "合集",
        self::REC_TYPE_IMAGE => "图片",
        self::REC_TYPE_GIFT => "礼包",
        self::REC_TYPE_ALONE => "单独推荐",
    );
    
    public static $status_list = array(
        self::STATUS_CLOSE => "关闭",
        self::STATUS_OPEN => "开启"
    );
    
    public static $alone_template = array(
        Game_Service_Util_Link::LINK_CONTENT => "游戏",
        Game_Service_Util_Link::LINK_ACTIVITY => "活动"
    );
    
	public static function getSingleListListBy($searchParams, $sortParams = array('sort' => 'desc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getSingleListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getSingleList($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateSingleList($data, $id) {
		if (!$id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateSingleListBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteSingleListBy($searchParams) {
		if (! $searchParams) return false;
		return self::getDao()->deleteBy($searchParams);
	}

	public static function deleteSingleList($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteSingleListList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addSingleList($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		$ret = self::getDao()->insert($data);
		if($ret) {
		    return self::getDao()->getLastInsertId();
		}
		return false;
	}

	public static function addMutiSingleList($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function checkNewField($data) {
	    $record = array();
		$record['title'] = isset($data['title']) ? $data['title'] : "";
		$record['content'] = isset($data['content']) ? $data['content'] : "";
		$record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
		$record['rec_type'] = isset($data['rec_type']) ? $data['rec_type'] : 0;
		$record['status'] = isset($data['status']) ? $data['status'] : 0;
		$record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
		$record['pgroup'] = isset($data['pgroup']) ? $data['pgroup'] : 0;
		$record['template'] = isset($data['template']) ? $data['template'] : 0;
		$record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data['id'])) $record['id'] = $data['id'];
		if(isset($data['title'])) $record['title'] = $data['title'];
		if(isset($data['content'])) $record['content'] = $data['content'];
		if(isset($data['day_id'])) $record['day_id'] = $data['day_id'];
		if(isset($data['rec_type'])) $record['rec_type'] = $data['rec_type'];
		if(isset($data['status'])) $record['status'] = $data['status'];
		if(isset($data['sort'])) $record['sort'] = $data['sort'];
		if(isset($data['pgroup'])) $record['pgroup'] = $data['pgroup'];
		if(isset($data['template'])) $record['template'] = $data['template'];
		if(isset($data['create_time'])) $record['create_time'] = $data['create_time'];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_SingleList");
	}

}
