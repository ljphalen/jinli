<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_Service_Web{
	public static $status = array(
			0=>'未抓取',
			1=>'抓取中',
			2=>'已抓取',
	);	
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	public static function getDomain($url){
		return parse_url($url,PHP_URL_HOST);
	}

	public static function addWeb($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		if(empty($data['update_time']))$data['update_time'] = Common::getTime();
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
	public static function update($data, $id) {
	    if (!is_array($data)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->update($data, intval($id));
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
	public static function deleteWeb($id) {
	    return self::_getDao()->delete(intval($id));
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		$channels = Client_Service_Spider::channels($data['channel']);
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['url_id'])) $tmp['url_id'] = $data['url_id'];
		if(!empty($data['item_id'])) $tmp['url_id'] = $data['item_id'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = $data['channel_id'];
		if(!empty($data['channel'])) $tmp['channel_id'] = $channels['channel_id'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['request_count'])) $tmp['request_count'] = $data['request_count'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
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
	 * @return Third_Dao_Web
	 */
	private static function _getDao() {
		return Common::getDao("Third_Dao_Web");
	}
}
