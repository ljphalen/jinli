<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Store_Service_Info extends Common_Service_Base {
	/**
	 * 获取数据
	 * @param integer $page
	 * @param integer $limit
	 * @param array $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取单条数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getOne($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function add($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function update($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		return self::_getDao()->getBy($params, $sort);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 删除数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function delete($id){
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTJ($id, $type_id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment($type_id, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * @param $version_id
	 * @param $value
	 * @return 获取统计的短链接
	 */
	public static function getShortUrl($version_id, $value) {
	    list($model_id, $channel_id) = explode('_', $value['module_channel']);
	    return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $value['url'], $value['title'], $value['channel_code']);
	}
	
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['cate_id'])) $tmp['cate_id'] = intval($data['cate_id']);
		if(isset($data['info_type'])) $tmp['info_type'] = intval($data['info_type']);
		if(isset($data['version_type'])) $tmp['version_type'] = intval($data['version_type']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		if(isset($data['module_id']) && isset($data['cid'])) $tmp['module_channel'] = intval($data['module_id']).'_'.intval($data['cid']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Store_Dao_Info
	 */
	private static function _getDao() {
		return Common::getDao("Store_Dao_Info");
	}
}