<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_Service_Shop{

    public static $channel=array(
    	1=>'淘宝',
    	2=>'天猫',
        3=>'京东',
    );
    public static $status = array(
    	0=>'未抓取',
    	1=>'抓取中',
    	2=>'已抓取',
    );
	/**
	 * 
	 * Enter description here ...
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
	public static function getShopsUrl($shop_id,$channel_id){
		switch ($channel_id) {
			case 1://淘宝
				return sprintf('http://shop.m.taobao.com/shop/shopIndex.htm?shop_id=%s', $shop_id);
				break;
			case 2://天猫
				return sprintf('http://shop.m.taobao.com/shop/shopIndex.htm?shop_id=%s', $shop_id);
				break;
			case 3://京东
				return sprintf('http://ok.jd.com/m/index-%s.htm', $shop_id);
				break;
			default:
				break;
		}
		return false;
	}


	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addShop($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
        if(empty($data['update_time']))$data['update_time'] = Common::getTime();
		$res = self::_getDao()->insert($data);
        if(!$res)return false;
        return self::_getDao()->getLastInsertId();
	}

    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params);
	}
	
	/**
	 *
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
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
	    if (!is_array($data)) return false;
	    $data = self::_cookData($data);
	    //用于cron spider update type
	    if($data['request_count'] >= 3) unset($data['type']);
	    return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
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
	 *
	 * del user
	 * @param int $id
	 */
	public static function deleteShop($id) {
	    return self::_getDao()->delete(intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$channels = Client_Service_Spider::channels($data['channel']);
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = $data['channel_id'];
		if(!empty($data['channel'])) $tmp['channel_id'] = $channels['channel_id'];
		if(isset($data['item_id'])) $tmp['shop_id'] = $data['item_id'];
		if(isset($data['shop_id'])) $tmp['shop_id'] = $data['shop_id'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(!empty($data['title'])) $tmp['name'] = $data['title'];
		if(isset($data['logo'])) $tmp['logo'] = $data['logo'];
		if(!empty($data['img'])) $tmp['logo'] = $data['img'];
		if(isset($data['data'])) $tmp['data'] = $data['data'];
		if(isset($data['request_count'])) $tmp['request_count'] = $data['request_count'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['favorite_count'])) $tmp['favorite_count'] = $data['favorite_count'];
		if(isset($data['system'])) $tmp['system'] = $data['system'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		return $tmp;
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAdd($data) {
	    if (!is_array($data)) return false;
	    self::_getDao()->mutiInsert($data);
	    return true;
	}
	
	/**
	 * 
	 * @return Third_Dao_Shop
	 */
	private static function _getDao() {
		return Common::getDao("Third_Dao_Shop");
	}
}
