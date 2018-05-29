<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Client_Service_ChannelColumn
 * @author fanch
 *
*/
class Client_Service_ChannelColumn {
	
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getCanUseColumn($ckey){
		if(!$ckey) return false;
		$params = array(
				'ckey'=>$ckey,
				'status'=>1,
				'start_time'=> array('<=', Common::getTime())
		);
		
		return self::_getDao()->getsBy($params, array('sort'=>'DESC','id'=>'DESC'));
	}
	
	public static function getColumn($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	public static function delColumn($id){
		if (!intval($id)) return false;
		return self::_getDao()->delete(intval($id));
	}
	
	public static function addColumn($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	public static function updateColumn($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function updateColumnBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 参数过滤
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['ckey'])) $tmp['ckey'] = $data['ckey'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Client_Dao_Channel
	 */
	private static function _getDao() {
		return Common::getDao ( "Client_Dao_ChannelColumn" );
	}
}