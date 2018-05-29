<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Service_RecommendGames
 * @author wupeng
 */
class Game_Service_RecommendGames{
    
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getRecommendGameList($page = 1, $limit = 10, $params = array(), $sort = array('sort'=>'DESC', 'game_id'=>"asc")) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $sort);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 删除游戏
	 * @param unknown $idList
	 * @return Ambigous <boolean, unknown, number>
	 */
	public static function deleteRecommendGames($idList) {
	    return self::getDao()->deletes("recommend_id", $idList);
	}
	
	/***
	 * 推荐的游戏列表
	 */
	public static function updateItemsList ($id, $gameList) {
	    list($deleteList, $addList) = self::getDeleteAddList($id, $gameList);
	    if(count($deleteList) == 0 && count($addList) == 0) {
	        return true;
	    }
	    Common_Service_Base::beginTransaction();
        $ret = self::deleteList($id, $deleteList);
        if (! $ret) {
            Common_Service_Base::rollBack();
            return false;
        }
    	$ret =  self::addList($id, $addList);
        if (! $ret) {
            Common_Service_Base::rollBack();
            return false;
        }
	    Common_Service_Base::commit();
	    return true;
	}
	
	/**删除*/
	private static function deleteList($id, $deleteList) {
	    foreach ($deleteList as $gameId) {
	        self::getDao()->deleteBy(array("recommend_id" => $id, "game_id" => $gameId));
	    }
	    return true;
	}
	
	/**添加*/
	private static function addList($id, $addList) {
	    if (count($addList) > 0) {
	        $data = array();
	        foreach ($addList as $gameId) {
	            $data[] = array(
	                    "recommend_id" => $id,
	                    "game_id" => $gameId,
	                    "sort" => 0,
	                    "game_status" => 1
	            );
	        }
	        return self::getDao()->mutiFieldInsert($data);
	    }
	    return true;
	}
	
	/**获取删除和添加的ＩＤ*/
	private static function getDeleteAddList($id, $gameList) {
	    $deleteList = array();
	    $list = self::getGames($id);
	    foreach ($list as $game) {
	        $gameId = $game["game_id"];
	        if(in_array($gameId, $gameList)) {
	            $key = array_search($gameId, $gameList);
	            if ($key !== false)
	                array_splice($gameList, $key, 1);
	        }else{
	            $deleteList[] = $gameId;
	        }
	    }
	    return array($deleteList, $gameList);
	}

	/**
	 * 更新推荐游戏排序
	 * @param unknown $id
	 * @param unknown $sorts
	 * @return boolean
	 */
	public static function updateItemsSort($id, $sorts) {
	    $list = self::getGames($id);
	    foreach ($list as $game) {
	        if ($game["sort"] == $sorts[$game["game_id"]]) continue;
	        $data = array("sort" => $sorts[$game["game_id"]]);
	        self::getDao()->updateBy($data, array("recommend_id" => $id, "game_id" => $game["game_id"]));
	    }
	    return true;
	}

	/**
	 * 获取推荐的游戏
	 * @param unknown $id
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getGames($id) {
	    return self::getDao()->getsBy(array("recommend_id" => $id), array('sort'=>'desc', 'game_id'=>"asc"));
	}

	/**
	 * 更新游戏状态字段(游戏打开关闭的时候需要调用)
	 * @param unknown $gameId
	 * @param unknown $status
	 */
	public static function updateGameStatus($gameId, $status) {
	    $params = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    return self::getDao()->updateBy($data, $params);
	}
    
	public static function getCounts($params) {
	    return self::getDao()->count($params);
	}
	
	/**
	 *
	 * @return Game_Dao_RecommendGames
	 */
	private static function getDao() {
	    return Common::getDao("Game_Dao_RecommendGames");
	}
	
}
