<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_Service_GoodsUnipid{
	/**
	 * 获取记录列表
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$res = self::_getDao()->insert($data);
		if(!$res)return false;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 * 获取记录
	 * @param int $id
	 * @return boolean|mixed
	 */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

	public static function getItemId($url){
		if (!$url) return false;
		return hexdec(substr(sha1(html_entity_decode($url)), 0, 15));
	}
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params);
	}

	public static function replace($data) {
		if(!$data["goods_id"]) return false;
		$data = self::_cookData($data);
	    return self::_getDao()->replace($data);
	}
	
	/**
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort=array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $goods_id) {
	    if (!is_array($data)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updateBy($data, array("goods_id"=>intval($goods_id)));
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public function updateBy($data, $params) {
	    if (!is_array($params)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updateBy($data,$params);
	}
	
	/**
	 * 删除记录
	 * @param int $id
	 */
	public static function delete($id) {
	    return self::_getDao()->delete(intval($id));
	}


	/**
	 * Enter desription here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['unique_pid'])) $tmp['unique_pid'] = $data['unique_pid'];
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['request_count'])) $tmp['request_count'] = $data['request_count'];
		return $tmp;
	}
	
	/**
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAdd($data) {
	    if (!is_array($data)) return false;
	    self::_getDao()->mutiInsert($data);
	    return true;
	}
	
	/**
	 * @return Third_Dao_GoodsUnipid
	 */
	private static function _getDao() {
		return Common::getDao("Third_Dao_GoodsUnipid");
	}
}
