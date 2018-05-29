<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_News
 * @author fanzh
 *
*/
class Gou_Service_News{
	
	
	/**
	 *
	 * 获取新闻分页列表
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
		
	/**
	 *
	 * 获取可用新闻分页列表
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getCanUseNews($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseNews($start, $limit, $params, $orderBy);
		$total = self::_getDao()->getCanUseNewsCount($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * 获取一条新闻
	 * @param int $id
	 */
	public static function getNews($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加一条新闻
	 * @param array $data
	 * @return boolean 
	 */
	public static function addNews($data){
		if(!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * 更新单条新闻
	 * @param array $data
	 * @param int $id
	 */
	public static function updateNews($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * 加入新闻统计
	 * @param unknown_type $id
	 */
	public static function updateNewsTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(16, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	
	/**
	 *
	 * 删除新闻
	 * 
	 * @param int $id 新闻ID
	 */
	public static function deleteNews($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * @param $version_id
	 * @param $value
	 * @return 获取统计的短链接
	 */
	public static function getShortUrl($version_id, $value) {
	    list($model_id, $channel_id) = explode('_', $value['module_channel']);
	    return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $value['link'], $value['title'], $value['channel_code']);
	}
	
	/**
	 * 参数过滤
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		if(isset($data['category'])) $tmp['category'] = $data['category'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['pub_time'])) $tmp['pub_time'] = intval($data['pub_time']);
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['module_id']) && isset($data['cid'])) $tmp['module_channel'] = intval($data['module_id']).'_'.intval($data['cid']);
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		return $tmp;
	}
	
	/**
	 * 
	 *
	 * @return Gou_Dao_News
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_News");
	}
	
}