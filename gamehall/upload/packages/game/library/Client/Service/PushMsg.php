<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_PushMsg{
	const STATE_CLOSE = 0;
	const STATE_OPEN = 1;
	
	const PushMsg_TYPE_GAMEID = 1;    //游戏内容
	const PushMsg_TYPE_SUBJECT = 2;   //专题
	const PushMsg_TYPE_CATEGOTY = 3;  //分类
	const PushMsg_TYPE_ACTIVITY = 4;  //活动
	const PushMsg_TYPE_LINK = 5;      //外链
	const PushMsg_TYPE_GIFT = 6;      //礼包
	const PushMsg_TYPE_TEXT = 7;      //文本信息
	
	const PushMsg_RECIVER_ALL_USER = 0;          //全部用户
	const PushMsg_RECIVER_ATTENTION = 1;         //接受对象已关注用户
	const PushMsg_RECIVER_INSTALL = 2;           //接受对象已安装用户
	const PushMsg_RECIVER_ATTENTION_INSTALL = 3; //接受对象已关注并且已安装游戏用户
	
	
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
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('start_time'=>'DESC','id'=>'DESC')) {
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('start_time'=>'DESC','id'=>'DESC')) {
		return self::_getDao()->getsBy($params,$orderBy);
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
	
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}
	
	public static function close($id) {
		$ret = self::_getDao()->updateBy(array('status'=>0), array('id'=>$id));
		return $ret;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateStatus($data,$statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$ret = self::_getDao()->update(array('status'=>$statu), $value);
		}
		return $ret;
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
		$ret =  self::_getDao()->insert($data);
		if(!$ret) {
		    return false;
		}
		return self::_getDao()->getLastInsertId();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['type'])) $tmp['type'] = $data['type'];
		if(isset($data['reciver_type'])) $tmp['reciver_type'] = $data['reciver_type'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['msg'])) $tmp['msg'] = $data['msg'];
		if(isset($data['contentId'])) $tmp['contentId'] = $data['contentId'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['last_author'])) $tmp['last_author'] = $data['last_author'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_PushMsg
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_PushMsg");
	}
}
