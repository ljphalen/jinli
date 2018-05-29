<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游推荐
 * Game_Service_GameWebRec
 * @author wupeng
 */
class Game_Service_GameWebRec {

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
    const REC_TYPE_OPEN = 3;
    public static $rec_type = array(
        self::REC_TYPE_LIST => "合集",
        self::REC_TYPE_IMAGE => "图片",
        self::REC_TYPE_GIFT => "礼包",
        self::REC_TYPE_OPEN => "开服",
    );
    
	public static function getGameWebRecListBy($searchParams, $sortParams = array('sort' => 'desc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameWebRecBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameWebRec($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameWebRec($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteGameWebRec($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameWebRecList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addGameWebRec($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		$ret = self::getDao()->insert($dbData);
		if($ret) {
	        return self::getDao()->getLastInsertId();
		}
		return false;
	}

	public static function updateGameWebRecBy($data, $searchParams) {
	    $dbData = self::checkField($data);
	    if (!is_array($dbData)) return false;
	    return self::getDao()->updateBy($dbData, $searchParams);
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['title'])) $dbData['title'] = $data['title'];
		if(isset($data['content'])) $dbData['content'] = $data['content'];
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['rec_type'])) $dbData['rec_type'] = $data['rec_type'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['pgroup'])) $dbData['pgroup'] = $data['pgroup'];
		if(isset($data['template'])) $dbData['template'] = $data['template'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['content'] = isset($data['content']) ? $data['content'] : "";
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['rec_type'] = isset($data['rec_type']) ? $data['rec_type'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::STATUS_CLOSE;
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
	    $record['pgroup'] = isset($data['pgroup']) ? $data['pgroup'] : 0;
	    $record['template'] = isset($data['template']) ? $data['template'] : 0;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
	    return $record;
	}
	
	private static function getDao() {
		return Common::getDao("Game_Dao_GameWebRec");
	}

}
