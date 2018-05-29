<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 游戏推荐
 * Game_Service_SingleListGames
 * @author wupeng
 */
class Game_Service_SingleListGames {

	public static function getSingleListGamesListBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getSingleListGamesBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getSingleListGames($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateSingleListGames($data, $id) {
		if (!$id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateSingleListGamesBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteSingleListGames($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteSingleListGamesList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteSingleListGamesBy($searchParams) {
		if (!$searchParams) return false;
		return self::getDao()->deleteBy($searchParams);
	}

	public static function addSingleListGames($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiSingleListGames($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function checkNewField($data) {
	    $record = array();
		$record['recommend_id'] = isset($data['recommend_id']) ? $data['recommend_id'] : "";
		$record['game_id'] = isset($data['game_id']) ? $data['game_id'] : 0;
		$record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
		$record['game_status'] = isset($data['game_status']) ? $data['game_status'] : 0;
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data['id'])) $record['id'] = $data['id'];
		if(isset($data['recommend_id'])) $record['recommend_id'] = $data['recommend_id'];
		if(isset($data['game_id'])) $record['game_id'] = $data['game_id'];
		if(isset($data['sort'])) $record['sort'] = $data['sort'];
		if(isset($data['game_status'])) $record['game_status'] = $data['game_status'];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_SingleListGames");
	}

	public static function updateGameStatus($gameId, $status) {
	    $params = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    return self::getDao()->updateBy($data, $params);
	}

}
