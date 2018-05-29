<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 * 8.20 更改
 */
class Client_Service_Game extends Common_Service_Base{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
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
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * Client_Service_Game::getCanUseAllGames
	 * @return multitype:string Ambigous <multitype:, multitype:>
	 */
	public function getCanUseAllGames() {
		$ret = self::_getDao()->getAll(array('sort' => 'DESC','id' => 'DESC'));
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 *
	 * @return multitype:string Ambigous <multitype:, multitype:>
	 */
	public function getAllGames() {
		$ret = self::_getDao()->getsBy(array('status'=>1), array('sort' => 'DESC','id' => 'DESC'));
		$total = self::_getDao()->count(array('status'=>1));
		return array($total, $ret);
	}
	
	/**
	 * 根据条件查询游戏
	 * @param unknown_type $data
	 * @return multitype:unknown Ambigous <boolean, mixed, multitype:>
	 */
	public static function getGames($params = array()) {
		$ret = self::_getDao()->getsBy($params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateGameStatus($ids, $status) {
		if (!is_array($ids)) return false;
		
		list($resource_game_ids, $game_ids) = self::_getGameIds($ids);
		if (!count($game_ids)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新游戏
			$ret = self::_getDao()->updates('id', $game_ids, array('status'=>$status));
			if (!$ret) throw new Exception("Update Game fail.", -202);
			
			$v = array();
			$vl = array();
			//更新分类索引游戏
			$ret = self::_getCategoryDao()->updates('resource_game_id', $resource_game_ids, array('status'=>$status));
			if (!$ret) throw new Exception('Update Category fail.', -205);
			
			//更新专题索引游戏
			$ret = self::_getSubjectDao()->updates('resource_game_id', $resource_game_ids, array('status'=>$status));
			if (!$ret) throw new Exception('Update Subject fail.', -205);
				
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			print_r($e->getMessage());
			parent::rollBack();
			return false;
		}
	}
	
	private static function _getGameIds($ids_str) {
		$game_ids = $resource_game_ids = array();
		foreach($ids_str as $key=>$value) {
			$v = explode('|', $value);
			$game_ids[] = $v[0];
			$resource_game_ids[] = $v[1];
		}
		return array($resource_game_ids, $game_ids);		
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGame($ids) {
		if (!is_array($ids)) return false;
		
		list($resource_game_ids, $game_ids) = self::_getGameIds($ids);
		if (!count($game_ids)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//删除本地游戏
			$ret = self::_getDao()->deletes('id', $game_ids);
			if (!$ret) throw new Exception('Delete Games fail.', -205);
			
			//删除分类索引
			$ret = Client_Service_Game::deleteCategoryGameByGameIds($game_ids);
			if (!$ret) throw new Exception('Delete Category Games fail.', -205);
			
			//删除专题索引
			$ret = Client_Service_Game::deleteSubjectGameyByGameIds($game_ids);
			if (!$ret) throw new Exception('Delete Subject Games fail.', -205);
			
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			print_r($e->getMessage());
			parent::rollBack();
			return false;
		}
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
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
		return self::_getDao()->getBy(array('resource_game_id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGameByStatu($status) {
		return self::_getDao()->getsBy(array('status'=>$status),array('sort'=>'desc', 'id'=>'desc'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGameByResourceId($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getBy(array('resource_game_id'=>$id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGameInfoByResourceId($id) {
		if (!intval($id)) return false;
		$params = array("resource_game_id"=>intval($id), "status"=>1);
		return self::_getDao()->getBy($params, array("sort"=>'DESC','id'=>'DESC'));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $cid
	 * @return Ambigous <boolean, number>
	 */
	public static function batchAddByCategory($data, $cid) {
		$tmp = array();
		$v = array();
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			$info = Resource_Service_Games::getResourceGames($v[0]);
			$tmp[] = array(
					'id'=>'',
					'status'=> $info['status'],
					'category_id'=>$cid,
					'game_id'=>$v[0],
					'resource_game_id'=>$v[1],
					'sort'=>0
			);
		}
		return self::_getCategoryDao()->mutiInsert($tmp);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGameclientByCategoryGames($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getCategoryDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteGameclientByCategory($id) {
		return 	self::_getCategoryDao()->deleteBy(array('category_id'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteCategoryGameByGameIds($ids) {
		return 	self::_getCategoryDao()->deletes('game_id', $ids);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateGameclientByCategory($id, $status) {
		return 	self::_getCategoryDao()->updateBy(array('status'=>$status),array('category_id'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateGameclientBySubject($id,$status) {
		return 	self::_getSubjectDao()->updateBy(array('status'=>$status),array('subject_id'=>$id));
	}
	
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameclientByCategory($id,$status) {
		return 	self::_getCategoryDao()->getsBy(array('category_id'=>$id,'status'=>$status));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getCategoryGames(array $params = array()) {
		$result = self::_getCategoryDao()->getsBy($params, array('sort'=>'DESC','id'=>'DESC'));
		$count = self::_getCategoryDao()->count($params);
		return array($count, $result);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getSubjectGames($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getSubjectDao()->getList($start, $limit, $params, array('sort'=>'DESC','game_id'=>'DESC'));
		$total = self::_getSubjectDao()->count($params);
		return array($total, $ret);
	}
	
	
	public static function getBySubjectGame($params){
	    if(!is_array($params)){
	        return false;
	    }
	    return self::_getSubjectDao()->getBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getSubjectBySubjectId(array $params = array()) {
		$result = self::_getSubjectDao()->getsBy($params, array('sort'=>'DESC','resource_game_id'=>'DESC'));
		$count = self::_getSubjectDao()->count($params);
		return array($count, $result);
	}
	
	
	/**
	 *
	 * @return multitype:
	 */
	public static function getIdxSubjectBySubjectAllId() {
		return self::_getSubjectDao()->getAll();
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameclientByCategoryId($id) {
		return 	self::_getCategoryDao()->getsBy(array('resource_game_id'=>$id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortGameclientByCategoryGames($data, $sorts) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getCategoryDao()->update(array('sort'=>$sorts[$v[0]]), $v[0]);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortGameclientByGames($data, $sorts) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->update(array('sort'=>$sorts[$v[0]]), $v[0]);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteSubjectGameyByGameIds($ids) {
		return 	self::_getSubjectDao()->deletes('game_id', $ids);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateSubjectsBySubjectIds($subject_id,$status) {
		if (!$subject_id) return false;
		//sprintf('UPDATE  %s SET status = %d WHERE subject_id =  %d', $this->getTableName(), $status,  $subject_id);
		return 	self::_getSubjectDao()->updateBy(array('status'=>$status), array('subject_id' => $subject_id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameclientByAllSubject($id) {
		return 	self::_getSubjectDao()->getsBy(array('subject_id'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGuessByGame(array $params = array()) {
		$result = self::_getGuessDao()->getsBy($params, array('sort'=>'DESC','game_id'=>'DESC'));
		$count = self::_getGuessDao()->count($params);
		return array($count, $result);
	}
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function geGuesstList($page = 1, $limit = 10, $params = array(),$orderBy = array('sort'=>'DESC','game_id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getGuessDao()->getList($start, $limit, $params,  $orderBy);
		$total = self::_getGuessDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $cid
	 * @return Ambigous <boolean, number>
	 */
	public static function batchAddByGuess($data) {
		$item = $guessGameIds =  array();
		$guessData = self::_getGuessDao()->getAll();
		if($guessData){
			$guessData = Common::resetKey($guessData, 'game_id');
			$guessGameIds = array_keys($guessData);
		}
		foreach($data as $key=>$value) {
			$info = Resource_Service_Games::getResourceGames($value);
			if(!in_array($value, $guessGameIds)){
				$item = array(
					'sort'=>0,
					'status'=> 1,
					'game_id'=>$value,
					'game_status'=>$info['status'],
					'online_time'=>$info['online_time'],
					'downloads'=>$info['downloads']
				);
				self::_getGuessDao()->insert($item);
			}
		}
		return true;
	}
	
	/**
	 * 更新猜你喜欢索引表游戏的上线时间和下载量
	 * @param unknown_type $online_time
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateIdxGameGuessOntime($online_time, $downloads, $game_id) {
		if (!$game_id) return false;
		return self::_getGuessDao()->updateBy(array('online_time'=>intval($online_time),'downloads'=>$downloads), array('game_id'=>intval($game_id),'game_status'=>1));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGuess($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getGuessDao()->deleteBy(array('game_id'=>$v[1]));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortGuessGame($data, $sorts) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getGuessDao()->update(array('sort'=>$sorts[$v[1]]), $v[0]);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameGuess($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_getGuessDao($data);
		return self::_getGuessDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGuessByGameId($game_id) {
		if (!intval($game_id)) return false;
		return self::_getGuessDao()->getBy(array('game_id'=>intval($game_id)));
	}
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateGuessGameId($status,$game_id) {
		if (!$game_id) return false;
		return self::_getGuessDao()->updateBy(array('game_status'=>intval($status)), array('game_id'=>intval($game_id)));
	}
	
	public static function batchUpdate($data,$status) {
		if (!is_array($data)) return false;
		foreach($data as $value) {
			self::_getDao()->update(array('status' => $status), $value);
			//更新主题索引
			$subject_ids = Client_Service_Game::getGameclientByAllSubject(intval($value));
			if($subject_ids){
				Client_Service_Game::updateSubjectsBySubjectIds(intval($value),$status);
			}
		}
		return true;
	}
	
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getMonthRankList($page = 1, $limit = 10, $params = array(),$orderBy = array('sort'=>'DESC','game_id'=>'DESC')) {
		$params = self::_cookMonthRankParams($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getMonthRankDao()->getList($start, $limit, $params,  $orderBy);
		$total = self::_getMonthRankDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $cid
	 * @return Ambigous <boolean, number>
	 */
	public static function batchAddByMonthRank($data) {
		$tmp = array();
		foreach($data as $key=>$value) {
			$info = Resource_Service_Games::getResourceGames($value);
			$tmp[] = array(
					'id'=>'',
					'sort'=>0,
					'status'=> 1,
					'game_id'=>$value,
					'game_status'=>$info['status'],
					'online_time'=>$info['online_time'],
					'downloads'=>$info['downloads']
			);
		}
		return self::_getMonthRankDao()->mutiInsert($tmp);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteMonthRank($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getMonthRankDao()->deleteBy(array('game_id'=>$v[1]));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortMonthRankGame($data, $sorts) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getMonthRankDao()->update(array('sort'=>$sorts[$v[1]]), $v[0]);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameMonthRank($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_getMonthRankDao($data);
		return self::_getMonthRankDao()->update($data, intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getMonthRankByGame(array $params = array()) {
		$data = self::_cookGuessParams($params);
		$result = self::_getMonthRankDao()->getsBy($data, array('sort'=>'DESC','game_id'=>'DESC'));
		$count = self::_getMonthRankDao()->count($data);
		return array($count, $result);
	}
	
	/**
	 * 更新月榜索引表游戏的上线时间和下载量
	 * @param unknown_type $online_time
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateIdxGameMonthRankOntime($online_time, $downloads, $game_id) {
		if (!$game_id) return false;
		return self::_getMonthRankDao()->updateBy(array('online_time'=>intval($online_time),'downloads'=>$downloads), array('game_id'=>intval($game_id),'game_status'=>1));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getMonthRankByGameId($game_id) {
		if (!intval($game_id)) return false;
		return self::_getMonthRankDao()->getBy(array('game_id'=>intval($game_id)));
	}
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateMonthRankGameId($status,$game_id) {
		if (!$game_id) return false;
		return self::_getMonthRankDao()->updateBy(array('game_status'=>intval($status)), array('game_id'=>intval($game_id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId(); 
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameClientSubjects() {
		return self::_getSubjectDao()->getAll();
	}
	
	/**
	 *
	 * @param unknown_type $client_game_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameClientSubjectBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getSubjectDao()->getsBy($params);
	}
	
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameClientCategorys() {
		return self::_getCategoryDao()->getAll();
	}
	
	/**
	 *
	 * @param unknown_type $client_game_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameClientCategoryBy($page = 1, $limit = 10, $params = array()) {
		if (!is_array($params)) return false;
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::_getCategoryDao()->getList($start, $limit, $params);
		$total = self::_getCategoryDao()->count($params);
		return array($total, $result);
	}
	
	/**
	 *
	 * @param unknown_type $client_game_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameClientBySubject($page = 1, $limit = 10, $params = array()) {
		if (!is_array($params)) return false;
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::_getSubjectDao()->getList($start, $limit, $params);
		$total = self::_getSubjectDao()->count($params);
		return array($total, $result);
	}
	
	
	/**
	 *
	 * @param unknown_type $client_category_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameClientCategoryByCategoryId($params = array()) {
		if (!is_array($params)) return false;
		return self::_getCategoryDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $client_subject_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameClientSubjectBySubjectId($params = array()) {
		if (!is_array($params)) return false;
		return self::_getSubjectDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxSubjectBySubjectGameId($game_id) {
		if (!$game_id) return false;
		return self::_getSubjectDao()->getsBy(array('resource_game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateSubjectsByGameIds($game_id,$status) {
		if (!$game_id) return false;
		//sprintf('UPDATE  %s SET game_status = %d WHERE resource_game_id =  %d', $this->getTableName(), $statu,  $id);
		return self::_getSubjectDao()->updateBy(array('game_status'=>$status), array('resource_game_id' => $game_id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addClientGame($data,$subject) {
	if (!is_array($data)) return false;
	
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//添加游戏
			$client_game_id = self::add($data);
			if (!$client_game_id) throw new Exception("Add Game fail.", -202);
			
			//添加专题索引
			if($subject){
			$subjects = self::_cookIdxData($subject, $client_game_id, ($data['status']));
			
			$ret = self::_getSubjectDao()->mutiInsert($subjects);
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
	 * @param unknown_type $data
	 */
	public static function updateClientGame($data,$subject, $id) {
	if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//更新游戏
			$ret = self::update($data, $id);
			if (!$ret) throw new Exception("Update Game fail.", -202);

			
			//删除专题索引
			$ret = self::_getSubjectDao()->deleteBy(array('client_game_id'=>$id));
			if (!$ret) throw new Exception('Delete Subject fail.', -205);
				
			//添加专题索引
			$subjects = self::_cookIdxData($subject, $id, $data['status']);
			if (count($subjects)) {
				$ret = self::_getSubjectDao()->mutiInsert($subjects);
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
	 * @param unknown_type $data
	 */
	public static function deleteClientGame($id) {
	  if (!$id) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//删除游戏
			$info = self::get($id);
			//Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
			
			$ret = self::delete($id);
			if (!$ret) throw new Exception("Delete Game fail.", -202);
			
			//删除专题索引
			$ret = self::_getSubjectDao()->deleteBy(array('client_game_id'=>$id));
			if (!$ret) throw new Exception('Delete Subject fail.', -205);
			
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
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['resource_game_id'])) $tmp['resource_game_id'] = intval($data['resource_game_id']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	private static function _cookInxParams($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
		if(isset($data['subject_id'])) $tmp['subject_id'] = intval($data['subject_id']);
		if(isset($data['resource_game_id'])) $tmp['resource_game_id'] = intval($data['resource_game_id']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_status'])) $tmp['game_status'] = intval($data['game_status']);
		return $tmp;
	}
	
	private static function _cookGuessParams($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
		if(isset($data['game_status'])) $tmp['game_status'] = intval($data['game_status']);
		if(isset($data['online_time'])) $tmp['online_time'] = intval($data['online_time']);
		if(isset($data['downloads'])) $tmp['downloads'] = intval($data['downloads']);
		return $tmp;
	}
	
	
	private static function _cookMonthRankParams($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
		if(isset($data['game_status'])) $tmp['game_status'] = intval($data['game_status']);
		if(isset($data['online_time'])) $tmp['online_time'] = intval($data['online_time']);
		if(isset($data['downloads'])) $tmp['downloads'] = intval($data['downloads']);
		return $tmp;
	}
	
	private static function _cookIdxData($data, $client_game_id, $status) {
		$tmp = array();
		foreach($data as $key=>$value) {
			if ($value != '') {
				$tmp[] = array('id'=>'', 'status'=>$status, 'client_subject_id'=>$value, 'client_game_id'=>$client_game_id);
			}
		}
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Game
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Game");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientSubject
	 */
	private static function _getSubjectDao() {
		return Common::getDao("Client_Dao_IdxGameClientSubject");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientCategory
	 */
	private static function _getCategoryDao() {
		return Common::getDao("Client_Dao_IdxGameClientCategory");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientGuess
	 */
	private static function _getGuessDao() {
		return Common::getDao("Client_Dao_IdxGameClientGuess");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientMonthRank
	 */
	private static function _getMonthRankDao() {
		return Common::getDao("Client_Dao_IdxGameClientMonthRank");
	}
}
