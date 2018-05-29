<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Fanfan_Service_Topicgoods extends Common_Service_Base {
	
	/**
	 * 获取主题商品数据
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
	 * 获取单条主题商品数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getTopicgoods($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加主题商品数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addTopicgoods($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改主题商品数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateTopicgoods($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
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
	 * 删除主题商品数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteTopicgoods($id){
		return self::_getDao()->delete(intval($id));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['topic_id'])) $tmp['topic_id'] = intval($data['topic_id']);
		if(isset($data['goods_id'])) $tmp['goods_id'] = strval($data['goods_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['price'])) $tmp['price'] = floatval($data['price']);
		if(isset($data['pro_price'])) $tmp['pro_price'] = floatval($data['pro_price']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Client_Dao_Topiccate
	 */
	private static function _getDao() {
		return Common::getDao("Fanfan_Dao_Topicgoods");
	}
}