<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author huangsg
 *
 */
class Fanfan_Service_Topic extends Common_Service_Base {
	/**
	 * 获取主题数据
	 * @param integer $page
	 * @param integer $limit
	 * @param array $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * 获取单条主题数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getTopic($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 获取当天的主题数据。供API使用
	 */
	public static function getTodayTopic(){
		$currentDate = date('Ymd');
		/* $params = array(
			'time_line'=>array(
				array(">=", strtotime($currentDate . ' 00:00:00')),
				array('<=', strtotime($currentDate . ' 23:59:59'))),
		); */

		$time = COmmon::getTime();
		$params = array(
				'time_line'=>array(
				        'type_id'=>1,
						array(">=", $time - 7*24*3600),
						array('<=', $time)),
		);
		return self::_getDao()->getsBy($params, array('sort'=>'DESC', 'id'=>'DESC'));
	}

	/**
	 * 添加主题数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addTopic($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['time_line'] = time();
		return self::_getDao()->insert($data);
	}

    /**
     *
     * @param string $id topic_id
     * @return bool|int
     */
    public static function updateTJ($id) {
        if (!$id) return false;
        Gou_Service_ClickStat::increment(27, $id);
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
    }
	/**
	 * 修改主题数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateTopic($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$data['time_line'] = time();
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function parise($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('praise', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function checkParise($id) {
	    if (!$id) return false;
	    $cookie = json_decode(Util_Cookie::get('GOU-PRAISE', true), true);
	    if(in_array('zdm_'.$id, $cookie)) return true;
	    return false;
	}
	

	/**
	 * 删除主题数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteTopic($id){
		return self::_getDao()->delete(intval($id));
	}

	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['cate_id'])) $tmp['cate_id'] = intval($data['cate_id']);
		if(isset($data['type_id'])) $tmp['type_id'] = intval($data['type_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['topic_desc'])) $tmp['topic_desc'] = $data['topic_desc'];
		if(isset($data['goods_desc'])) $tmp['goods_desc'] = $data['goods_desc'];
		if(isset($data['banner_url'])) $tmp['banner_url'] = $data['banner_url'];
		if(isset($data['keywords'])) $tmp['keywords'] = $data['keywords'];
		if(isset($data['search_btn'])) $tmp['search_btn'] = $data['search_btn'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		return $tmp;
	}

	/**
	 *
	 * @return Fanfan_Dao_Topic
	 */
	private static function _getDao() {
		return Common::getDao("Fanfan_Dao_Topic");
	}
}