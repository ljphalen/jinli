<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_Ad{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllAd() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllAdSort() {
		return array(self::_getDao()->getAllAdCount(), self::_getDao()->getAllAdSort());
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $guideTypes
	 * @return boolean|multitype:
	 */
	public static function getByGuideTypes($guideTypes) {
		if (!is_array($guideTypes)) return false;
		return self::_getDao()->getByGuideTypes($guideTypes);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getCanUseAds($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseAds(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseAdCount($params);
		return array($total, $ret); 
	}
	
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getBy($params, $orderBy = array()) {
		if (!is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateAd($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateAdTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(2, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAd($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	

    /**
     * @param $version_id
     * @param $value
     * @return 获取统计的短链接
     */
    public static function getShortUrl($version_id, $value) {
		if (strpos($value['link'], 'http://') === false || !strpos($value['link'], 'https://') === false) {
			return $value['link'];
		}
        list($model_id, $channel_id) = explode('_', $value['module_channel']);
        return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $value['link'], $value['title'], $value['channel_code']);
    }
    
    /**
	 * sort
	 * @param array $sort
	 * @return boolean
	 */
	public static function sort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	
	/**
	 * 
	 * @param array $ids
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updates($ids, $data) {
	    if (!is_array($data) || !is_array($ids)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['ad_type'])) $tmp['ad_type'] = intval($data['ad_type']);
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		if(isset($data['ad_ptype'])) $tmp['ad_ptype'] = intval($data['ad_ptype']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['activity'])) $tmp['activity'] = $data['activity'];
		if(isset($data['clientver'])) $tmp['clientver'] = intval($data['clientver']);
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['search_time'])) $tmp['start_time'] = $data['search_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['module_id']) && isset($data['cid'])) $tmp['module_channel'] = intval($data['module_id']).'_'.intval($data['cid']);
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		if(isset($data['ptype_id'])) $tmp['ptype_id'] = $data['ptype_id'];
		if(isset($data['is_recommend'])) $tmp['is_recommend'] = $data['is_recommend'];
		if(isset($data['action'])) $tmp['action'] = $data['action'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Ad");
	}
}
