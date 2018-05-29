<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 第三方API分类和列表功能
 * @author huangsg
 *
 */
class Gou_Service_PartnerAPI {
	/**
	 * 获取API列表
	 * @param number $page
	 * @param number $limit
	 * @param array $params
	 * @return array
	 */
	public static function getAPIList($page = 1, $limit = 10, $params = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取单条API信息
	 * @param int $id
	 * @return boolean|mixed
	 */
	public static function getAPIInfo($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 根据API的Sign获取单条API信息
	 * @param string $sign
	 * @return boolean|mixed
	 */
	public static function getAPIBySign($sign){
		if (empty($sign)) return false;
		return self::_getDao()->getBy(array('sign'=>$sign));
	}
	
	/**
	 * 检查API的sign是否存在。
	 * @param string $sign
	 * @param number $id id=0则表示添加数据时检查。不等于0，则表示修改时检查。
	 */
	public static function checkSign($sign, $id = 0){
		if (empty($sign)) return true;
		if ($id == 0){
			return self::_getDao()->count(array('sign'=>$sign));
		}
		return self::_getDao()->count(array('id'=>array('!=', $id), 'sign'=>array('=', $sign)));
	}
	
	/**
	 * 根据API分类ID获取分类下属的API列表
	 * @param int $cate_id
	 * @return boolean|mixed
	 */
	public static function getAPIByCateId($cate_id){
		if (empty($cate_id)) return false;
		return self::_getDao()->getsBy(array('cate_id'=>$cate_id, 'status'=>1));
	}

	/**
	 * 添加API
	 * @param array $data
	 * @return boolean
	 */
	public static function addAPI($data){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 更新API
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateAPI($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 删除API
	 * @param int $id
	 * @return boolean|number
	 */
	public static function deleteAPI($id){
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * 获取可用的API分类
	 * @return bool|mixed
	 */
	public static function getAPICateAble(){
		return self::_getCateDao()->getsBy(array('status'=>1));
	}
	
	/**
	 * 获取所有的API分类
	 * @return boolean|mixed
	 */
	public static function getAPICate(){
		return self::_getCateDao()->getAll();
	}

	/**
	 * 获取API分类列表
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookCateData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getCateDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取API分类的信息
	 * @param int $id
	 * @return boolean|mixed
	 */
	public static function getAPICateInfo($id){
		if (!intval($id)) return false;
		return self::_getCateDao()->get(intval($id));
	}
	
	/**
	 * 添加API分类
	 * @param array $data
	 * @return boolean|number
	 */
	public static function addAPICate($data){
		if (empty($data)) return false;
		$data = self::_cookCateData($data);
		return self::_getCateDao()->insert($data);
	}
	
	/**
	 * 更新API分类信息
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateAPICate($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookCateData($data);
		return self::_getCateDao()->update($data, intval($id));
	}
	
	/**
	 * 删除API分类
	 * @param int $id
	 * @return boolean
	 */
	public static function deleteAPICate($id){
		return self::_getCateDao()->delete(intval($id));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['cate_id'])) $tmp['cate_id'] = intval($data['cate_id']);
		if(isset($data['sign'])) $tmp['sign'] = $data['sign'];
		if(isset($data['api_url'])) $tmp['api_url'] = $data['api_url'];
		if(isset($data['remark'])) $tmp['remark'] = $data['remark'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	private static function _cookCateData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Gou_Dao_PartnerAPI
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_PartnerAPI");
	}
	
	/**
	 *
	 * @return Gou_Dao_PartnerAPICate
	 */
	private static function _getCateDao() {
		return Common::getDao("Gou_Dao_PartnerAPICate");
	}
}