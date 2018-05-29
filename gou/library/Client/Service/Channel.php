<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Client_Service_Channel {
	/**
	 * 获取渠道数据
	 * @param integer $page
	 * @param integer $limit
	 * @param array $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getAll(){
	    $time = Common::getTime();
		return self::_getDao()->getsBy(array('status'=>1,  'start_time'=>array('<', $time), 'end_time'=>array('>', $time)), array('sort'=>'DESC'));
	}
	
	/**
	 * 获取单条渠道数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getChannel($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
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
	 * 添加渠道数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addChannel($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//只能有推荐一个
		if($data['is_recommend'] == 1) {
		    self::_getDao()->updateBy(array('is_recommend'=>0), array('is_recommend'=>1));
		}
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改渠道数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateChannel($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);

		//只能有推荐一个
		if($data['is_recommend'] == 1) {
		    self::_getDao()->updateBy(array('is_recommend'=>0), array('is_recommend'=>1));
		}
		
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 删除渠道数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteChannel($id){
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * @param $version_id
	 * @param $value
	 * @return 获取统计的短链接
	 */
	public static function getShortUrl($version_id, $value) {
	    list($model_id, $channel_id) = explode('_', $value['module_channel']);
	    return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $value['link'], $value['name'], $value['channel_code']);
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['description'])) $tmp['description'] = $data['description'];
		if(isset($data['description1'])) $tmp['description1'] = $data['description1'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['top'])) $tmp['top'] = intval($data['top']);
		if(isset($data['cate_id'])) $tmp['cate_id'] = intval($data['cate_id']);
		if(isset($data['module_id']) && isset($data['cid'])) $tmp['module_channel'] = intval($data['module_id']).'_'.intval($data['cid']);
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		if(isset($data['color'])) $tmp['color'] = $data['color'];
		if(isset($data['is_recommend'])) $tmp['is_recommend'] = intval($data['is_recommend']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		if(isset($data['is_hot'])) $tmp['is_hot'] = intval($data['is_hot']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Client_Dao_Channel
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Channel");
	}
}