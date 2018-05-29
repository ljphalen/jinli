<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Service_RecommendGames
 * @author wupeng
 */
class Game_Service_RecommendGames{
    
	public static function getRecommendGameList($page = 1, $limit = 10, $params = array(), $sort = array('sort'=>'DESC', 'game_id'=>"asc")) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $sort);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}

	public static function getRecommendGamesBy($searchParams, $sortParams = array('sort'=>'DESC', 'game_id'=>"asc")) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function deleteRecommendGames($idList) {
		if (!is_array($idList)) return false;
	    return self::getDao()->deletes("recommend_id", $idList);
	}
	
	public static function deleteList($id, $deleteList) {
	    if(! $deleteList) {
	        return false;
	    }
	    return self::getDao()->deleteBy(array("recommend_id" => $id, "game_id" => array('IN', $deleteList)));
	}
	
	public static function getGames($id) {
	    return self::getDao()->getsBy(array("recommend_id" => $id), array('sort'=>'desc', 'game_id'=>"asc"));
	}
	
	public static function updateGameStatus($gameId, $status) {
	    $params = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    return self::getDao()->updateBy($data, $params);
	}
    
	public static function getCounts($params) {
	    return self::getDao()->count($params);
	}

	public static function addMutiRecommendGames($list) {
	    if (! $list) return false;
	    return self::getDao()->mutiFieldInsert($list);
	}
	
	public static function updateRecommendGame($data, $recommend_id, $game_id) {
	    self::getDao()->updateBy($data, array("recommend_id" => $recommend_id, "game_id" => $game_id));
	}
	
	private static function getDao() {
	    return Common::getDao("Game_Dao_RecommendGames");
	}
	
}
