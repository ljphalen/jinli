<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */
class Amigo_Service_Weather {
	/**
	 * 获取所有配置信息
	 * @return multitype:
	 */
	public static function getWeatherConfig(){
		return self::_getDao()->getAll();
	}
	
	/**
	 * 检查配置信息是否存在
	 * @param integer $id
	 * @param integer $parentID
	 * @return boolean
	 */
	public static function checkInfoExist($root_id, $parent_id){
		$rs = self::_getDao()->count(array('root_id'=>$root_id, 'parent_id'=>$parent_id));
		return !empty($rs) ? true : false;
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 * 将天气商品配置数据写入数据库
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function add($data){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 更新一项配置
	 * @param array $data
	 * @param integer $ids
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function update($data, $root_id, $parent_id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, array(
				'root_id'=>$root_id,
				'parent_id'=>$parent_id
		));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['keywords'])) $tmp['keywords'] = $data['keywords'];
		if(isset($data['num_iid'])) $tmp['num_iid'] = $data['num_iid'];
		if(isset($data['root_id'])) $tmp['root_id'] = $data['root_id'];
		if(isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Fanli_Dao_Type
	 */
	private static function _getDao() {
		return Common::getDao("Amigo_Dao_Weather");
	}
}