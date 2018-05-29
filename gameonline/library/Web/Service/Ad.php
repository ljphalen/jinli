<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * web广告Service 文件
 * @author ljphalen
 *
 */
class Web_Service_Ad{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllAd() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseAds($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array("sort"=>"DESC","id"=>"DESC"));
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseNormalAds($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		
		$time = Common::getTime('Y-m-d H:i:s');
		$params['start_time'] = array('<=', $time);
		$params['end_time'] = array('>', $time);
		$params['status'] = 1;
		
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array("sort"=>"DESC","id"=>"DESC"));
		$total = self::_getDao()->count($params);
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateAdTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['ad_type'])) $tmp['ad_type'] = $data['ad_type'];
		if(isset($data['ad_ptype'])) $tmp['ad_ptype'] = $data['ad_ptype'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		//if(isset($data['ad_ltype'])) $tmp['ad_ltype'] = $data['ad_ltype'];
		return $tmp;
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getAdGames(array $params = array()) {
		$params = self::_cookData($params);
		$result = self::_getDao()->getsBy($params, array('sort'=>'DESC','id'=>'DESC'));
		$count = self::_getDao()->count($params);
		return array($count, $result);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $cid
	 * @return Ambigous <boolean, number>
	 */
	public static function addGameAd($data, $ad_type) {
		$tmp = array();
		$time = Common::getTime();
		foreach($data as $key=>$value) {
			$tmp[] = array(
					'id'=>'',
					'sort'=>0,
					'title'=>'',
					'ad_type'=> $ad_type,
					'ad_ptype'=>1,
					'link'=>$value,
					'img'=>'',
					'start_time'=>$time,
					'end_time'=>'',
					'status'=>1,
					'hits'=>0,
			);
		}
		return self::_getDao()->mutiInsert($tmp);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGameAd($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortGameAd($data, $sorts) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->update(array('sort'=>$sorts[$v[1]]), $v[0]);
		}
		return true;
	}
	
	/**
	 * 
	 * @return Web_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Web_Dao_Ad");
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
    public static function updateWebAdStatus($status,$id) {
		if (!$id) return false;
		return self::_getDao()->updateBy(array('status'=>intval($status)), array('link'=>intval($id),'ad_type'=>2));
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getWebAdByGameId($game_id) {
		if (!intval($game_id)) return false;
		return self::_getDao()->getBy(array('link'=>intval($game_id)));
	}
}