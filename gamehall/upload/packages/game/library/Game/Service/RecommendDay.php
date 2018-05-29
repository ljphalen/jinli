<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Game_Service_RecommendDay
 * @author wupeng
 */
class Game_Service_RecommendDay{
	
	const STATUS_OPEN = 1;         //开启状态
	const STATUS_CLOSE = 0;        //关闭状态

	public static function getRecommendByDayId($day_id) {
		return self::getDao()->getBy(array('day_id' => $day_id));
	}
	
	public static function getRecommendDayBy($searchParams, $sortParams = array('id' => 'asc')) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getRecommendDay($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
    
	public static function updateRecommendDay($data, $id) {
		if (!is_array($data)) return false;
		$data = self::checkField($data);
		return self::getDao()->update($data, intval($id));
	}
	
	public static function deleteRecommendDay($id) {
		return self::getDao()->delete(intval($id));
	}
	
	public static function deleteRecommendDayList($idList) {
		if (!is_array($idList)) return false;
		return self::getDao()->deletes("id", $idList);
	}

	public static function deleteRecommendDayByDayId($day_id) {
	    $keyParams = array('day_id' => $day_id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	public static function addRecommendDay($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}
	
	/**
	 * 更新游戏状态字段(游戏打开关闭的时候需要调用)
	 */
	public static function updateGameStatus($gameId, $status) {
	    $params = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    if($status == Resource_Service_Games::STATE_OFFLINE) {
	        $data['status'] = self::STATUS_CLOSE;
	    }
	    $result = self::getDao()->updateBy($data, $params);
        return $result;
	}
	
	private static function checkNewField($data) {
	    $record = array();
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['game_id'] = isset($data['game_id']) ? $data['game_id'] : 0;
	    $record['game_status'] = isset($data['game_status']) ? $data['game_status'] : Resource_Service_Games::STATE_ONLINE;
	    $record['content'] = isset($data['content']) ? $data['content'] : "";
	    $record['start_time'] = isset($data['start_time']) ? $data['start_time'] : 0;
	    $record['end_time'] = isset($data['end_time']) ? $data['end_time'] : 0;
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::STATUS_CLOSE;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}
    
	private static function checkField($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['day_id'])) $tmp['day_id'] = $data['day_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendDay");
	}
	
}
