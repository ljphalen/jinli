<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Freedl_Service_Hd extends Common_Service_Base{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllFreedl() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('start_time'=>'DESC','id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFreedl($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateFreedl($data,$id) {
		$ret = self::_getDao()->update($data, $id);
		return $id;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param array $orderBy
	 */
	public static function getsByFreedl($params,$orderBy = array('id'=>'DESC')) {
		$params = self::_cookData($params);
		$ret =  self::_getDao()->getsBy($params,$orderBy);
		return $ret;
	}
	
    public static function getActivatedItems($orderBy = array('id'=>'DESC')) {
        $currentTime = Common::getTime();
        $currentTime = Util_TimeConvert::floor($currentTime, Util_TimeConvert::RADIX_HOUR);
        $query['status'] = 1;
        $query['start_time'] = array('<=', $currentTime);
        $query['end_time'] = array('>=', $currentTime);
        return self::_getDao()->getsBy($query, $orderBy);
    }
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByFreedl($params, $orderBy = array()) {
		$ret =  self::_getDao()->getBy($params, $orderBy);
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByFreedl($data, $params) {
		if (!is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFreedl($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFreedl($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 * 检查游戏id是否是指定活动中专区免流量类型的游戏
	 * @param int $hdid
	 * @param int $gameid
	 */
	public static function checkFreedlGame($hdid, $gameid){
		$ret =  self::_getGameFreedlDao()->getBy(array('status'=>1 ,'freedl_id' => $hdid, 'game_id'=>$gameid));
		return $ret['id'] ? true : false;
	}
	
	/**
	 *免流量专区游戏列表
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getFreedList($page = 1, $limit = 10, $params = array(), $orderBy = array('game_id'=>'DESC')) {
		$params = self::_cookfreedlData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getGameFreedlDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getGameFreedlDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getGames($params) {
		return self::_getGameFreedlDao()->getsBy($params,array('game_id'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateByGames($data, $params) {
		return self::_getGameFreedlDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addMuFreedl($data) {
		if (!is_array($data) && !$data) return false;
		return self::_getGameFreedlDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByGame($params) {
		return self::_getGameFreedlDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return Ambigous <boolean, number>
	 */
	public static function batchAddByGames($data, $activity_id, $user) {
		$tmp = $temp = array();
		$size = 0.00;
		$num = 0;
		foreach($data as $key=>$value) {
			   $info = self::getByTmpFreedl(array('freedl_id'=>$activity_id,'game_id'=>$value));
			   if(!$info){
				   	$tmp[] = array(
				   			'id'=>'',
				   			'user' => $user,
				   			'sort' => '',
				   			'status'=> 1,
				   			'freedl_id' => $activity_id,
				   			'game_id'=> $value,
				   	);
				   	$temp[] = array(
				   			'id'=>'',
				   			'sort' => '',
				   			'status'=> 1,
				   			'freedl_id' => $activity_id,
				   			'game_id'=> $value,
				   	);
			   }
		}
		
		if($temp && $tmp){
			$rollData = Game_Service_Config::getValue($user.'freedHd_'.$activity_id);
			$rollData = unserialize($rollData);
			$rollData['add'] = ($rollData['add'] ? array_merge($rollData['add'], $temp ) : $temp);
			Game_Service_Config::setValue($user.'freedHd_'.$activity_id, serialize($rollData));
			$ret =self::_getGameTmpFreedlDao()->mutiInsert($tmp);
		}
		return true;
	}
	
	/**
	 * 计算添加游戏的总大小
	 */
	public static  function compNum($activity_id) {
		$size = 0.00;
		$num = 0;
		$games = Freedl_Service_Hd::getGames(array('freedl_id'=>$activity_id,'status'=>1));
		foreach($games as $k=>$v){
			$info = Resource_Service_Games::getGameVersionInfo($v['game_id']);
			$size = $size + $info['size'];
			$num ++;
		}
		return array($num, $size);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFreedlGame($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addTmpFreedl($data) {
		if (!is_array($data) && !$data) return false;
		return self::_getGameTmpFreedlDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteTmpFreedl($params) {
		return self::_getGameTmpFreedlDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getTmpGames($params) {
		return self::_getGameTmpFreedlDao()->getsBy($params,array('game_id'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByTmpFreedl($params) {
		return self::_getGameTmpFreedlDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateByTmpGames($data, $params) {
		return self::_getGameTmpFreedlDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByTmpHdFreedl($params) {
		return self::_getTmpDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteTmpHd($params) {
		return self::_getTmpDao()->deleteBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addTmpHd($data) {
		if (!is_array($data)) return false;
		$ret = self::_getTmpDao()->insert($data);
	}
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['htype'])) $tmp['htype'] = intval($data['htype']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['num'])) $tmp['num'] = $data['num'];
		if(isset($data['total'])) $tmp['total'] = $data['total'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['f_img'])) $tmp['f_img'] = $data['f_img'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['download'])) $tmp['download'] = $data['download'];
		if(isset($data['phone_consume'])) $tmp['phone_consume'] = $data['phone_consume'];
		if(isset($data['wifi_consume'])) $tmp['wifi_consume'] = $data['wifi_consume'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['refresh_time'])) $tmp['refresh_time'] = intval($data['refresh_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['explain'])) $tmp['explain'] = $data['explain'];
		if(isset($data['total_warning'])) $tmp['total_warning'] = $data['total_warning'];
		if(isset($data['user_warning'])) $tmp['user_warning'] = $data['user_warning'];
		if(isset($data['email_warning'])) $tmp['email_warning'] = $data['email_warning'];
		if(isset($data['blacklist_rule'])) $tmp['blacklist_rule'] = $data['blacklist_rule'];
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookfreedlData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['freedl_id'])) $tmp['freedl_id'] = $data['freedl_id'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_Hd
	 */
	private static function _getDao() {
		return Common::getDao("Freedl_Dao_Hd");
	}
	
	/**
	 *
	 * @return Freedl_Dao_IdxGameFreedlHd
	 */
	private static function _getGameFreedlDao() {
		return Common::getDao("Freedl_Dao_IdxGameFreedlHd");
	}
	
	/**
	 *
	 * @return Freedl_Dao_IdxGameTmpFreedlHd
	 */
	private static function _getGameTmpFreedlDao() {
		return Common::getDao("Freedl_Dao_IdxGameTmpFreedlHd");
	}
	
	/**
	 *
	 * @return Freedl_Dao_HdTmp
	 */
	private static function _getTmpDao() {
		return Common::getDao("Freedl_Dao_HdTmp");
	}
}
