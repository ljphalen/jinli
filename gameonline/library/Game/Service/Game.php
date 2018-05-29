<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Game_Service_Game extends Common_Service_Base{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGame() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getSearchGames($page, $limit, $params) {
		$data = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getSearchGames(intval($start), intval($limit), $data);
		$total = self::_getDao()->getSearchGamesCount($data);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateGameTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGame($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGameInfo($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getGameInfo(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGame($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxSubjects() {
		return self::_getIdxGameSubjectDao()->getAll();
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxLabels() {
		return self::_getIdxGameLabelDao()->getAll();
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @return boolean|mixed
	 */
	public static function getIdxSubjectBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameSubjectDao()->getsBy($params);
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @return boolean|mixed
	 */
	public static function getIdxLabelBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameLabelDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $subject_id
	 * @return boolean|mixed
	 */
	public static function getIdxSubjectBySubjectId($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameSubjectDao()->getsBy($params);
	}
	
	
	/**
	 * 
	 * @return multitype:
	 */
	public static function getIdxSubjectBySubjectAllId() {
		return self::_getIdxGameSubjectDao()->getAll();
	}
	
	
	
	
	/**
	 *
	 * @param unknown_type $label_id
	 * @return boolean|mixed
	 */
	public static function getIdxLabelByLabelId($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameLabelDao()->getsBy($params);
	}
	
	/**
		return self::_getIdxGameLabelDao()->getsBy($params);
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxImgByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameImgDao()->getBy(array('game_id'=>$game_id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameIdx($data, $upimg, $img, $label, $subject, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//更新游戏
			$ret = self::updateGame($data, $id);
			if (!$ret) throw new Exception("Update Game fail.", -202);
						
			//删除标签索引
			$label_ret= self::_getIdxGameLabelDao()->deleteBy(array('game_id'=>$id));
			
			//删除专题索引
			$subject_ret = self::_getIdxGameSubjectDao()->deleteBy(array('game_id'=>$id));
			//修改的图片
			foreach($upimg as $key=>$value) {
				if ($key && $value) {
					Game_Service_GameImg::updateGameImg(array('img'=>$value), $key);
				}
			}
			//新增加的图片
			if ($img[0] != null) {
				$gimgs = array();
				foreach($img as $key=>$value) {
					if ($value != '') {
						$gimgs[] = array('game_id'=>$id, 'img'=>$value);
					}
				}
				$ret = Game_Service_GameImg::addGameImg($gimgs);
				if (!$ret) throw new Exception('add GameImg fail.', -203);
			}
				
			//添加标签索引
			$labels = self::_cookIdxData($label, $id, $data['status'], 'LABEL');
			if (count($labels)) {
				$ret= self::_getIdxGameLabelDao()->mutiInsert($labels);
				if (!$ret) throw new Exception('Delete label fail.', -204);
			}
				
			//添加专题索引
			$subjects = self::_cookIdxData($subject, $id, $data['status'], 'SUBJECT');
			if (count($subjects)) {
				$ret = self::_getIdxGameSubjectDao()->mutiInsert($subjects);
				if (!$ret) throw new Exception('Add Subject fail.', -205);
			}
			
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGame($id) {
		return self::_getDao()->delete(intval($id));
	}
	
   /**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
    public static function deleteGameIdx($id) {
    if (!$id) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//删除游戏
			$info = Game_Service_Game::getGame($id);
			Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
			
			$ret = self::deleteGame($id);
			if (!$ret) throw new Exception("Delete Game fail.", -202);
						
			//删除标签索引
			$label_ret= self::_getIdxGameLabelDao()->deleteBy(array('game_id'=>$id));
			
			//删除专题索引
			$subject_ret = self::_getIdxGameSubjectDao()->deleteBy(array('game_id'=>$id));
			
			//删除游戏预览图片
			$ret = self::_getIdxGameImgDao()->deleteBy(array('game_id'=>$id));
			if (!$ret) throw new Exception('Delete GameImg fail.', -205);
			
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGame($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId(); 
	}
	
	/**
	 * 
	 * @param array $data
	 * @param array $img
	 * @param array $label
	 * @param array $subject
	 * @throws Exception
	 * @return boolean
	 */
	public static function addGameIdx($data, $img, $label, $subject) {
		if (!is_array($data)) return false;
	
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//添加游戏
			$game_id = self::addGame($data);
			if (!$game_id) throw new Exception("Add Game fail.", -202);
			
			//添加游戏图片
			$imgs = self::_cookIdxData($img, $game_id, $data['status'], 'IMG');
			$ret= Game_Service_GameImg::addGameImg($imgs);
			if (!$ret) throw new Exception('Add GameImg fail.', -203);
			
			//添加标签索引
			$labels = self::_cookIdxData($label, $game_id, $data['status'], 'LABEL');
			$ret= self::_getIdxGameLabelDao()->mutiInsert($labels);
			if (!$ret) throw new Exception('Add label fail.', -204);
			
			//添加专题索引
			if($subject){
			$subjects = self::_cookIdxData($subject, $game_id, $data['status'], 'SUBJECT');
			$ret = self::_getIdxGameSubjectDao()->mutiInsert($subjects);
			if (!$ret) throw new Exception('Add Subject fail.', -205);
			} 
			
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	private static function _cookIdxData($data, $game_id, $status, $type) {
		$tmp = array();
		foreach($data as $key=>$value) {
			if ($value != '') {
				if ($type == 'IMG') {
					$tmp[] = array('id'=>'', 'game_id'=>$game_id, 'img'=>$value);
				} else if ($type == 'SUBJECT') {
					$tmp[] = array('id'=>'', 'status'=>$status, 'subject_id'=>$value, 'game_id'=>$game_id);
				} else if ($type == 'LABEL') {
					$tmp[] = array('id'=>'', 'status'=>$status, 'label_id'=>$value, 'game_id'=>$game_id);
				}
			}
		}
		return $tmp;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['language'])) $tmp['language'] = $data['language'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['recommend'])) $tmp['recommend'] = intval($data['recommend']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['company'])) $tmp['company'] = $data['company'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['tgcontent'])) $tmp['tgcontent'] = $data['tgcontent'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Game_Dao_Game
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_Game");
	}
	
	/**
	 * 
	 * @return Game_Dao_IdxGameLabel
	 */
	private static function _getIdxGameLabelDao() {
		return Common::getDao("Game_Dao_IdxGameLabel");
	}
	
	/**
	 * 
	 * @return Game_Dao_IdxGameSubject
	 */
	private static function _getIdxGameSubjectDao() {
		return Common::getDao("Game_Dao_IdxGameSubject");
	}
	
	/**
	 *
	 * @return Game_Dao_GameImg
	 */
	private static function _getIdxGameImgDao() {
		return Common::getDao("Game_Dao_GameImg");
	}
}
