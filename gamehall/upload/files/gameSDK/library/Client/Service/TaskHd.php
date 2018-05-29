<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_TaskHd{
    
    // hd_object 字段, 参与用户
    const TARGET_USER_USER_ALL = 1;     // 全部用户
    const TARGET_USER_USER_BY_UUID = 2; // 定向用户，指定UUID
    
    // game_object 字段，参与游戏 
    const TARGET_GAME_GAME_ALL = 1;     // 全部游戏
    const TARGET_GAME_SINGLE_SUBJECT =2;// 定向专题
    const TARGET_GAME_GAMEID_LIST = 3;  // 定向游戏
    const TARGET_GAME_EXCLUDE_LIST = 4; // 排除游戏
    
    const A_COUPON_EFFECT_START_DAY = 1;
    const LOGIN_GAME = 2;     //登陆游戏
    
    const MIN_AMOUNT = 0.01;
    
    /**
     * 执行A券活动任务
     * @param string $eventName
     * @param array $request
     */
    public static function runTask($eventName, $request){
        $eventObj = new Task_Event($eventName, $request);
        Task_EventHandle::postEvent($eventObj);
    }
    
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
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
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
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		return self::_getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 * 查找有效的联运游戏活动
	 * @return array
	 */
	public static function getsUnionGamesHds() {
		$params =  array();
		$params['status'] = 1;
		$params['htype'] = 2;
		$params['hd_start_time'] = array('<',Common::getTime());
		$params['hd_end_time'] = array('>',Common::getTime());
		return self::_getDao()->getsBy($params,array('id'=>'DESC'));
	}
	
	/**
	 * 
	 * @return Client_Dao_TaskHd
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_TaskHd");
	}
}
