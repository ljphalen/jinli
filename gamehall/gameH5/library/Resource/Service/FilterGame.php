<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desLabeliption here ...
 * @author lichanghau
 *
 */
class Resource_Service_FilterGame extends Common_Service_Base{
	const GAME_STATE_ONLINE = 1;
	const GAME_STATE_OFFLINE = 0;
	const FILTER_CACHE_EXPRIE = 86400;
	const FILTER_GAME_CACHE_KEY = 'filter_game';
	
	/**
	 * 
	 * @param unknown $params
	 * @param unknown $orderBy
	 * @return boolean
	 */
	public static function getsBy($params, $orderBy = array('create_time'=>'DESC','game_id'=>'DESC')){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	public static function getBy($params, $orderBy = array('create_time'=>'DESC','game_id'=>'DESC')){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	public static function count($params){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function mutiFilterGames($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy= array('create_time'=>'DESC','game_id' => 'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean
	 */
	public static function mutiGames($data,$id) {
		if (!is_array($data) || !$id) return false;
		foreach($data as $k=>$v){
				$tmp[] = array(
							'id'=>'',
							'status'=>1,
						    'pgroup_id'=>$id,
							'game_id'=>$v,
							'create_time'=>Common::getTime(),
				);
		}
		if($tmp){
			 self::_getDao()->mutiInsert($tmp);
			 self::addFilterGameCache($id);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean
	 */
	public static function deleteGames($data,$id) {
		if (!is_array($data) || !$id) return false;
		foreach($data as $k=>$v){
			self::_getDao()->deleteBy(array('id'=>$v,'pgroup_id'=>$id));
		}
		self::setFilterGamesCache($id);
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteFilterGame($id) {
		$filterInfo = self::getBy(array('id'=>$id));
		$pgroupId = $filterInfo['pgroup_id'];
		$ret = self::_getDao()->delete(intval($id));
		self::setFilterGamesCache($pgroupId);
		return $ret;
	}
	
   public static function deleteBy($params) {
		$ret = self::_getDao()->deleteBy($params);
		self::deleteFilterGameCache($params['game_id']);
		return $ret;
	}
	
	public static function deleteFilterGameCache($gameId){
		if(!$gameId){
			return false;
		}	
		$pgroups = $this->getsByPgroups($gameId);
		foreach($pgroups as $key=>$value){
			self::setFilterGamesCache($value);
		}
		return true;
	}

	
	public static function addFilterGameCache($pgroupId){
		if(!$pgroupId){
			return false;
		}
		self::setFilterGamesCache($pgroupId);
		return true;
	}
	
	
	public static  function getFilterGames($pgroupId){
		if(!$pgroupId){
			return false;
		}
		$cache = Common::getCache();
		$cacheKey = $pgroupId.'_'.self::FILTER_GAME_CACHE_KEY;
		$filterGames = $cache->get($cacheKey);
		if($filterGames){
			return $filterGames;
		}else{
		   $filterData = self::getsBy(array('pgroup_id'=>$pgroupId,'status'=>self::GAME_STATE_ONLINE));
		   if($filterData){
			 $packages = self::getFilterGamesPackages($filterData);
			 if($packages) $cache->set($cacheKey, $packages, self::FILTER_CACHE_EXPRIE);
		   }
		}
	}
	
	
	private static function getsByPgroups($gameId) {
		$pgroups = $games = array();
		$games = self::getsBy(array('game_id'=>$gameId));
		foreach ($games as $key=>$value){
		   $pgroups[] = $value['pgroup_id'];
		}
		return array_unique($pgroups);
	}
	
	private static function getFilterGamesPackages($filterData) {
		$packages = array();
		foreach ($filterData as $key=>$value){
			 $info = Resource_Service_GameData::getGameAllInfo($value['game_id']);
			 if($info['status']) {
			    $packages[] = $info['package'];
			 }
		}
		return array_unique($packages);
	}
	
	private static function setFilterGamesCache($pgroupId) {
		if(!$pgroupId){
			return false;
		}
	    $cache = Common::getCache();
		$cacheKey = $pgroupId.'_'.self::FILTER_GAME_CACHE_KEY;

		$filterData = self::getsBy(array('pgroup_id'=>$pgroupId));
		if($filterData){
			$packages = self::getFilterGamesPackages($filterData);
			if($packages) $cache->set($cacheKey, $packages, self::FILTER_CACHE_EXPRIE);
		}else{
			$cache->delete($cacheKey);
		}
		return true;
	}
	
	public static function deleteFilterGamesCache($pgroupId) {
		if(!$pgroupId){
			return false;
		}
		$cache = Common::getCache();
		$cacheKey = $pgroupId.'_'.self::FILTER_GAME_CACHE_KEY;
		$ret = self::_getDao()->deleteBy(array('pgroup_id'=>$pgroupId));
		$cache->delete($cacheKey);
		return true;
	}
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['pgroup_id'])) $tmp['pgroup_id'] = $data['pgroup_id'];		
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Resource_Dao_FilterGame
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_FilterGame");
	}
}
