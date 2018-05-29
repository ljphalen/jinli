<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Client_Service_SubjectGames
 * @author wupeng
 */
class Client_Service_SubjectGames {

    const SHOW_NUM = 4;
    
	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllSubjectGames() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 分页查询
	 * @param int $page
	 * @param int $limit
	 * @param array $searchParams
	 * @param array $sortParams
	 * @return array
	 */	 
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('sort'=>'desc', 'game_id'=>"asc")) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
    
	public static function getPageDistinctGameList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('sort'=>'desc', 'game_id'=>"asc")) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getDistinctGames($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->getDistinctCount($searchParams);
		return array($total, $ret);
	}
	
	public static function getSubjectGamesBy($searchParams = array(), $sortParams = array('sort'=>'desc', 'game_id'=>"asc")) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	/**
	 * 根据主键查询一条记录
	 * @param int $id
	 * @return array
	 */
	public static function getSubjectGames($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	/**
	 * 根据主键更新一条记录
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateSubjectGames($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	/**
	 * 根据主键删除一条记录
	 * @param int $id
	 * @return boolean
	 */
	public static function deleteSubjectGames($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	/**
	 * 根据主键删除多条记录
	 * @param array $keyList
	 * @return boolean
	 */
	public static function deleteSubjectGamesList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addSubjectGames($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}

	/**
	 * 获取专题子项游戏
	 * @param unknown $id
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getItemGames($id, $item_id) {
	    return self::getDao()->getsBy(array('subject_id' => $id, 'item_id' => $item_id), array('sort'=>'desc', 'game_id'=>"asc"));
	}
	
	/**
	 * 获取专题的所有游戏
	 * @param unknown $id
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getSubjectAllGames($id) {
	    return self::getDao()->getsBy(array('subject_id' => $id), array('sort'=>'desc', 'game_id'=>"asc"));
	}

	public static function getSubjectAllItemsGames($id) {
	    return self::getDao()->getsBy(array('subject_id' => $id), array('item_id' => 'asc', 'sort'=>'desc', 'game_id'=>"asc"));
	}
	
	/**
	 * 更新游戏列表
	 */
	public static function updateItemsList ($id, $item_id, $gameList) {
	    list($deleteList, $addList) = self::getDeleteAddList($id, $item_id, $gameList);
	    if(count($deleteList) == 0 && count($addList) == 0) {
	        return true;
	    }
	    Common_Service_Base::beginTransaction();
	    $ret = self::deleteList($id, $item_id, $deleteList);
	    if (! $ret) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    $ret =  self::addList($id, $item_id, $addList);
	    if (! $ret) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    Common_Service_Base::commit();
	    return true;
	}


	/**
	 * 更新专题状态
	 * @param $subject_id
	 * @param int $status
	 * @return boolean
	 */
	public static function updateSubjectGamesStatus($subject_id, $status) {
	    return 	self::getDao()->updateBy(array('status'=> $status), array('subject_id' => $subject_id));
	}
	
	/**删除*/
	private static function deleteList($id, $item_id, $deleteList) {
	    foreach ($deleteList as $gameId) {
	        self::getDao()->deleteBy(array("subject_id" => $id, "item_id" => $item_id, "game_id" => $gameId));
	    }
	    return true;
	}
	
	/**
	 * 游戏上线下线操作
	 * @param unknown $game_id
	 * @param unknown $status
	 * @return boolean
	 */
	public static function updateGamesStatus($game_id, $status) {
	    if (!$game_id) return false;
	    return self::getDao()->updateBy(array('game_status'=> $status), array('game_id' => $game_id));
	}
	
	/**添加*/
	private static function addList($id, $item_id, $addList) {
	    if (count($addList) > 0) {
	        $data = array();
	        foreach ($addList as $gameId) {
	            $data[] = array(
	                'sort' => 0, 
	                'resource_game_id' => $gameId, 
	                'status' => 0, 
	                'game_status' => 1, 
	                'subject_id' => $id, 
	                'game_id' => $gameId, 
	                'item_id' => $item_id, 
	                'resume' => '', 
	            );
	        }
	        return self::getDao()->mutiFieldInsert($data);
	    }
	    return true;
	}
	
	/**获取删除和添加的ＩＤ*/
	private static function getDeleteAddList($id, $item_id, $gameList) {
	    $deleteList = array();
	    $list = self::getItemGames($id, $item_id);
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
	public static function updateItemsSort($id, $item_id, $sorts) {
	    $list = self::getSubjectAllGames($id);
	    foreach ($list as $game) {
	        if ($game["sort"] == $sorts[$game["game_id"]]) continue;
	        $data = array("sort" => $sorts[$game["game_id"]]);
	        self::getDao()->updateBy($data, array("subject_id" => $id, "item_id" => $item_id, "game_id" => $game["game_id"]));
	    }
	    return true;
	}
	
	/**
	 * 删除主题对应的游戏
	 * @param unknown $subject_id
	 */
	public static function deleteGamesBySubjectId($subject_id) {
	    return self::getDao()->deleteBy(array('subject_id' => $subject_id));
	}
	
	/**
	 * 检查数据库字段
	 * @param array $data
	 * @return array
	 */
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['resource_game_id'])) $dbData['resource_game_id'] = $data['resource_game_id'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		if(isset($data['game_status'])) $dbData['game_status'] = $data['game_status'];
		if(isset($data['subject_id'])) $dbData['subject_id'] = $data['subject_id'];
		if(isset($data['game_id'])) $dbData['game_id'] = $data['game_id'];
		if(isset($data['item_id'])) $dbData['item_id'] = $data['item_id'];
		if(isset($data['resume'])) $dbData['resume'] = $data['resume'];
		return $dbData;
	}

	/**
	 * @return Client_Dao_SubjectGames
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_SubjectGames");
	}
	
}
