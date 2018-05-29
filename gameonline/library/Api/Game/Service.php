<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * games api
 * @author lichanghua
 *
 */
class Api_Game_Service extends Common_Service_Base{
	
	/* get all games
	 * @return array
	 */
	public static function getAllResourceGames(){
		return  Resource_Service_Games::getAllResourceGames();
	}
	
	public static function getsByIds(array $ids) {
		if (!is_array($ids)) return false;
		return Resource_Service_Games::getByIds($ids);
	}
	
	public static function getGameImg($id) {
		if (!intval($id)) return false;
		return Resource_Service_Img::getList($page = 1, $limit = 10, array('game_id'=>$id));
	}
	
	
	public static function getAllGameImg() {
		return Resource_Service_Img::getAllGameImg();
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceGames($id) {
		if (!intval($id)) return false;
		return  Resource_Service_Games::getResourceGames(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceGamesAttribute($tid) {
		if (!intval($tid)) return false;
		return  Resource_Service_Attribute::getList(1, 150,array('at_type'=>$tid));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceGamesByIds($page, $limit, $parmes) {
		if (!is_array($parmes)) return false;
		return  Resource_Service_Games::getCanUseGames($page, $limit, $parmes);
	}
	
	
	/**
	 *
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceNewGamesByIds($page, $limit, $parmes) {
		if (!is_array($parmes)) return false;
		return  Resource_Service_Games::getCanUseNewGames($page, $limit, $parmes);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceTopGamesByIds($page, $limit, $parmes) {
		if (!is_array($parmes)) return false;
		return  Resource_Service_Games::getCanUseTopGamesByIds($page, $limit, $parmes);
	}
	
	
	/**
	 *
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceTopGames($page, $limit, $parmes) {
		if (!is_array($parmes)) return false;
		return  Resource_Service_Games::getCanUseTopGames($page, $limit, $parmes);
	}
	
    /**
     * 
     * @param unknown_type $parmes
     * @return boolean|Ambigous <boolean, mixed, multitype:>
     */
	public static function getResourceGamesByCategorys($parmes) {
		if (!is_array($parmes)) return false;
		return  Resource_Service_Games::getIdxGameResourceCategoryBy($parmes);
	}
	
	/**
	 * 
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getResourceIdxModelGames($version) {
		if (!$resolution) return false;
		return  Resource_Service_Games::getResourceIdxModelGames($parmes);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $parmes
	 * @return boolean|Ambigous <multitype:unknown, multitype:unknown multitype: >
	 */
	public static function getSearchResourceGamesByCategory($page, $limit, $parmes) {
		return  Resource_Service_Games::getSearchResourceGamesByCategory($page, $limit, $parmes);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return array
	 */
	public static function getResourceSortGamesByIds($page, $limit, $parmes) {
		if (!is_array($parmes)) return false;
		return  Resource_Service_Games::getCanUseSortGames($page, $limit, $parmes);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		return Resource_Service_Games::search($page , $limit , $params, $orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function addSearch($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		return Resource_Service_Games::addSearch($page , $limit , $params, $orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		return Resource_Service_Games::getResourceList($page , $limit , $params);
	}
	
	/**
	 * 
	 * @return Ambigous <multitype:unknown, multitype:unknown multitype: >
	 */
	public static function getKeyword() {
		return Resource_Service_Games::getKeyword();
	}
	
	/**
	 *
	 * @return Ambigous <multitype:unknown, multitype:unknown multitype: >
	 */
	public static function getHots() {
		return Resource_Service_Games::getHots();
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @param unknown_type $from
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getInfo($id,$from,$webroot) {
		return Resource_Service_Games::getInfo($id,$from,$webroot);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $webroot
	 * @return Ambigous <boolean, multitype:, unknown>
	 */
	public static function getGameList($data,$webroot) {
		return Resource_Service_Games::getGameList($data,$webroot);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $upimg
	 * @param unknown_type $img
	 * @param unknown_type $id
	 * @throws Exception
	 * @return boolean
	 */
	public static function updateResourceGamesIdx($data, $upimg, $img, $id){
		if (!is_array($data)) return false;
	    //开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//更新游戏
			$ret = Resource_Service_Games::updateResourceGames($data, $id);
			if (!$ret) throw new Exception("Update Game fail.", -202);
						
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
	 * @param unknown_type $id
	 * @throws Exception
	 * @return boolean
	 */
	public static function deleteResourceGamesIdx($id){
		if (!$id) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
		
			//删除游戏
			$info = Resource_Service_Games::getResourceGames($id);
			Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
			
			$ret = self::deleteResourceGames($id);
			if (!$ret) throw new Exception("Delete Game fail.", -202);
			
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
	 * @param unknown_type $data
	 * @return array
	 */
	public static function addResourceGamesIdx($data,$img){
	    if (!is_array($data)) return false;
	
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//添加游戏
			$game_id = Resource_Service_Games::addResourceGames($data);
			if (!$game_id) throw new Exception("Add Game fail.", -202);
			
			//添加游戏图片
			$tmp = array();
			foreach($img as $key=>$value) {
			   $tmp[] = array('id'=>'', 'game_id'=>intval($game_id), 'img'=>$value);
			}
			$ret= Game_Service_GameImg::addGameImg($tmp);
			if (!$ret) throw new Exception('Add GameImg fail.', -203);
			
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
}