<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Freedl_Service_Blacklist{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllBlacklist() {
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
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * 后台根据账号搜索
	 *
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsearchList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		if($params['name']){
			$params = self::_getDao()->_cookParams($params);
		} else {
			$params = Db_Adapter_Pdo::sqlWhere($params);
		}
		$ret = self::_getDao()->searchBy($start, $limit, $params, $orderBy);
		$total = self::_getDao()->searchCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBlacklist($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateBlacklistStatus($data,$statu) {
		if (!is_array($data)) return false;
		$tmp = array();
		foreach($data as $key=>$value){
			$info = Freedl_Service_Blacklist::getBlacklist($value);
			if($info['utype'] == 1) {
				if(!in_array($info['imsi'], $tmp['imsi'])) $tmp['imsi'][] = $info['imsi'];
			} else {
				if(!in_array($info['imei'], $tmp['imei'])) $tmp['imei'][] = $info['imei'];
			}
		}
		if($tmp['imsi']) self::_getBlackData($tmp['imsi'], $statu, 'imsi', 1);
		if($tmp['imei']) self::_getBlackData($tmp['imei'], $statu, 'imei', 3);
		$ret = Freedl_Service_Permission::setBlackData();
		return $ret;
	}
	
	/**
	 * 批量添加或者移除黑名单
	 * @param array $data
	 * @return boolean|multitype:string unknown
	 */
	 private function _getBlackData($data,$statu,$ukey,$utype){
		$remove_time = '';
		//解除黑名单时间
		if(!$statu) $remove_time = Common::getTime();
		foreach($data as $key=>$value){
			$params = array();
			$params[$ukey] = $value;
			$params['utype'] = $utype;
			//查找该记录对应的所有IMSI或者IMEI，然后全部更新
			$blks = Freedl_Service_Blacklist::getsByBlacklist($params);
			foreach($blks as $k=>$v){
				$num = $v['num'];
				if($statu) $num = $v['num'] + 1;
				Freedl_Service_Blacklist::updateBlacklist(array('status'=>$statu,'remove_time'=>$remove_time,'num'=>$num),$v['id']);
			}
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsByBlacklist($params, $orderBy = array('id'=>'DESC')) {
		$ret =  self::_getDao()->getsBy($params, $orderBy);
		return $ret;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByBlacklist($params) {
		$ret =  self::_getDao()->getBy($params);
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBlacklist($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByBlacklist($data, $params) {
		if (!is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addBlacklist($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['utype'])) $tmp['utype'] = $data['utype'];
		if(isset($data['imsi'])) $tmp['imsi'] = $data['imsi'];
		if(isset($data['activity_id'])) $tmp['activity_id'] = $data['activity_id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['remove_time'])) $tmp['remove_time'] = $data['remove_time'];
		if(isset($data['num'])) $tmp['num'] = intval($data['num']);
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = $data['sys_version'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_Blacklist
	 */
	private static function _getDao() {
		return Common::getDao("Freedl_Dao_Blacklist");
	}
}
