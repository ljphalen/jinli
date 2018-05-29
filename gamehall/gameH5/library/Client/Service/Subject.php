<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Subject extends Common_Service_Base{
    
    const CLIENT_URL = "/client/subject/detail";
    
    const SUBTYPE_LIST = 0;
    const SUBTYPE_CUSTOM = 1;
    
    /**主题类型*/
    public static $subType = array(
        self::SUBTYPE_LIST => '列表',
        self::SUBTYPE_CUSTOM => '自定义'
    );
    
    /**视觉模板*/
    public static $viewTpl = array(
        self::SUBTYPE_LIST => array(1 => '模板1-2', 0 => '模板1-1'),
        self::SUBTYPE_CUSTOM => array(1 => '模板2-2', 0 => '模板2-1'),
    );
    
    /**自定义模板游戏列表*/
    public static $costomTpl = array(
        1 => '一个游戏',
        6 => '六个游戏'
    );
    
    
	const SUBJECT_STATUS_OPEN = 1;
	const SUBJECT_STATUS_CLOSE = 0;
	const SUBJECT_STATUS_INVALID = -1;
    
	public static function getAllSubject() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
    
	/**客户端首页获取热专题*/
	public static function getTopList($params = array(), $limit = 2) {
	    return self::getDao()->getList(0, $limit, $params, array('sort'=>'DESC','id'=>'DESC'));
	}

	public static function getsBy($searchParams, $sortParams = array('sort'=>'desc', 'id'=>'desc')) {
	    $list = self::getDao()->getsBy($searchParams, $sortParams);
	    return $list;
	}
	
	public static function getCanUseSubjects($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		
		$time = Common::getTime();
		$params['status'] = 1;
		$params['start_time'] = array('<', $time);
		$params['end_time'] = array('>', $time);
		$ret = self::getDao()->getList(intval($start), intval($limit), $params,array('sort'=>'desc', 'id'=>'desc'));
		$total = self::getDao()->count($params);
		return array($total, $ret); 
	}
	
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getSubject($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	public static function getOnlineSubject($id) {
		$params['id'] = $id;
		$params['status'] = 1;
		$params['start_time'] = array('<=', Common::getTime());
		$params['end_time'] = array('>=', Common::getTime());
		return self::getDao()->getBy($params,array('id'=>'DESC'));
	}
	
	public static function updateSubject($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	public static function updateGameSubject($data, $id) {
        if (! is_array($data)) return false;
        // 开始事务
        $trans = parent::beginTransaction();
        try {
            self::updateSubject($data, $id);
            if (isset($data['status'])) {
                Client_Service_SubjectGames::updateSubjectGamesStatus($id, $data['status']);
            }
            // 事务提交
            if ($trans) return parent::commit();
            return true;
        } catch (Exception $e) {
            parent::rollBack();
            return false;
        }
	}
	
	public static function batchUpdate($data, $status) {
		if (!is_array($data)) return false;
		foreach($data as $value) {
			self::getDao()->update(array('status' => $status), $value);
			Client_Service_SubjectGames::updateSubjectGamesStatus($value, $status);
		}
		return true;
	}
	
	public static function updateSubjectTJ($id) {
		if (!$id) return false;
		return self::getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	public static function deleteSubject($id) {
	//开始事务
		$trans = parent::beginTransaction();
		try {
		    Client_Service_SubjectGames::deleteGamesBySubjectId($id);
		    Client_Service_SubjectItems::deleteItemsBySubjectId($id);
			//删除主题
			$ret = self::getDao()->delete(intval($id));
			if (!$ret) throw new Exception('Delete Subject fail.', -205);
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	public static function addSubject($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret = self::getDao()->insert($data);
		if($ret) {
		    return self::getDao()->getLastInsertId();
		}
		return false;
	}
	
	/**更新排序*/
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

	public static function saveSubject($subject, $items, $games) {
	    $trans = parent::beginTransaction();
	    try {
	        $oldGames = array();
            if(! $subject['id']) {
                $subjectId = Client_Service_Subject::addSubject($subject);
                $subject['id'] = $subjectId;
            }else{
                Client_Service_Subject::updateSubject($subject, $subject['id']);
                $oldGames = Client_Service_SubjectGames::getSubjectAllGames($subject['id']);
            }
            $oldGameList = array();
            foreach ($oldGames as $game) {
                $oldGameList[$game['item_id'].'_'.$game['game_id']] = $game;
            }
            
            if($items) {
                $count = count($items);
                for ($i = 0; $i < $count; $i++) {
                    $item = $items[$i];
                    $itemGames = $games[$i];
                    if(! $item['item_id']) {
                        $item['sub_id'] = $subject['id'];
                        $itemId = Client_Service_SubjectItems::addSubjectItems($item);
                        $item['item_id'] = $itemId;
                    }else{
                        Client_Service_SubjectItems::updateSubjectItems($item, $item['item_id']);
                    }
                    foreach ($itemGames as $game) {
                        $game['subject_id'] = $subject['id'];
                        $game['item_id'] = $item['item_id'];
                        $game['status'] = $subject['status'];
                        if(! $game['id']) {
                            Client_Service_SubjectGames::addSubjectGames($game);
                        }else{
                            Client_Service_SubjectGames::updateSubjectGames($game, $game['id']);
                        }
                        if(isset($oldGameList[$game['item_id'].'_'.$game['game_id']])) {
                            unset($oldGameList[$game['item_id'].'_'.$game['game_id']]);
                        }
                    }
                }
            }else{
                foreach ($games as $game) {
                    $game['subject_id'] = $subject['id'];
                    $game['status'] = $subject['status'];
                    if(! $game['id']) {
                        Client_Service_SubjectGames::addSubjectGames($game);
                    }else{
                        Client_Service_SubjectGames::updateSubjectGames($game, $game['id']);
                    }
                    if(isset($oldGameList[$game['item_id'].'_'.$game['game_id']])) {
                        unset($oldGameList[$game['item_id'].'_'.$game['game_id']]);
                    }
                }
            }
            foreach ($oldGameList as $oldGame) {
                Client_Service_SubjectGames::deleteSubjectGames($oldGame['id']);
            }
	        return parent::commit();
	        return true;
	    } catch (Exception $e) {
	        parent::rollBack();
	    }
	    return false;
	}
	
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['hdinfo'])) $tmp['hdinfo'] = $data['hdinfo'];		
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['hot'])) $tmp['hot'] = intval($data['hot']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['sub_type'])) $tmp['sub_type'] = intval($data['sub_type']);
		if(isset($data['view_tpl'])) $tmp['view_tpl'] = intval($data['view_tpl']);
		if(isset($data['pgroup'])) $tmp['pgroup'] = intval($data['pgroup']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Subject
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_Subject");
	}
}
