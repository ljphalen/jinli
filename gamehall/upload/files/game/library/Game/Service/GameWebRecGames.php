<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 游戏推荐
 * Game_Service_GameWebRecGames
 * @author wupeng
 */
class Game_Service_GameWebRecGames {

	public static function getGameWebRecGamesListBy($searchParams, $sortParams = array('sort' => 'desc', 'game_id' => 'desc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameWebRecGamesBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('sort' => 'desc', 'game_id' => 'desc')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameWebRecGames($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameWebRecGames($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteGameWebRecGames($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameWebRecGamesList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteGameWebRecGamesBy($params) {
	    if (! $params) return false;
	    return self::getDao()->deleteBy($params);
	}

	public static function addGameWebRecGames($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}

	public static function addMutiGameWebRecGames($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function updateGameStatus($gameId, $status) {
	    $params = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    return self::getDao()->updateBy($data, $params);
	}
	
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['recommend_id'])) $dbData['recommend_id'] = $data['recommend_id'];
		if(isset($data['game_id'])) $dbData['game_id'] = $data['game_id'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['game_status'])) $dbData['game_status'] = $data['game_status'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['recommend_id'] = isset($data['recommend_id']) ? $data['recommend_id'] : 0;
	    $record['game_id'] = isset($data['game_id']) ? $data['game_id'] : 0;
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
	    $record['game_status'] = isset($data['game_status']) ? $data['game_status'] : 0;
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_GameWebRecGames");
	}

}
