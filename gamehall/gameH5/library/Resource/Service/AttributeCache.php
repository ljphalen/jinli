<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏属性常驻缓存，数据采用hash结构存储。
 * 应用于前台服务器，提高游戏基本数据的响应速度。
 * @author fanch
 *
 */
class Resource_Service_AttributeCache extends Common_Service_Base{
	
	//过期时间为24小时
	const CACHE_EXPRIE = 86400;
	
	/**
	 * 更新属性数据到缓存
	 */
	public static function saveAtrributeToCache(){
		$ckey= ":attribute:all";
		self::_getCache()->delete($ckey);
		$data = Resource_Service_Attribute::getsBy(array('status'=>1));
		if($data){
			self::saveDataToCache($ckey, $data);
		}
	}

	/**
	 * 更新标签数据到缓存
	 */
	public static function saveLabelToCache(){
		$ckey= ":label:all";
		self::_getCache()->delete($ckey);
		$data = Resource_Service_Label::getsBy(array('status'=>1));
		if($data){
			self::saveDataToCache($ckey, $data);
		}
	}


	/**
	 * 根据属性类别获取所有的值
	 * @param int $type
	 */
	public static function getAttributeBytype($type){
		$result = array();
		$data = self::getAttributeToCache();
		if(!$data) return $result;
		foreach ($data as $value){
			$item = json_decode($value, true);
			if($item['at_type'] == $type) {
				$result[] = $item;
			}
		}
		return $result;
	}
	
	/**
	 * 根据类型获取所有属性
	 * @param int $type
	 */
	
	public static function getLabelBytype($type){
		$result = array();
		$data = self::getLabelToCache();
		if(!$data) return $result;
		foreach ($data as $value){
			$item = json_decode($value, true);
			if($item['btype'] == $type) {
				$result[] = $item;
			}
		}
		return $result;
	}
	
	
	/**
	 * 根据id获取属性值
	 * @param int $type
	 */
	
	public static function getAttributeByid($id){
		$data = self::getAttributeToCache();
		if(!$data) return array();
		return json_decode($data[$id], true);
	}
	
	/**
	 * 根据id 获取 标签值
	 * @param int $type
	 */
	
	public static function getLabelByid($id){
		$data = self::getLabelToCache();
		if(!$data) return array();
		return json_decode($data[$id], true);
	}
	
	/**
	 * 设置数据到指定缓存key中
	 * @param string $ckey
	 * @param array $data
	 */
	private static function saveDataToCache($ckey, $data){
		$cData = self::jsonData($data);
		if(!$cData) return array();
		self::_getCache()->hMSet($ckey, $cData);
		self::setCaheExpire($ckey);
	}
	
	/**
	 * 统一数据存储格式
	 * @param array $data
	 * @return 
	 */
	private static function jsonData($data){
		$result= array();
		foreach ($data as $value){
			$result[$value['id']] = (!is_null($value)) ? json_encode($value) : '';
		}
		return $result;
	}
	
	/**
	 * 获取属性数据
	 */
	private static function getAttributeToCache(){
		$ckey= ":attribute:all";
		$data = self::_getCache()->hGetAll($ckey);
		if(empty($data)){
			$data = array();
			$data = Resource_Service_Attribute::getsBy(array('status'=>1));
			if($data){
				self::saveDataToCache($ckey, $data);
				$data = self::jsonData($data);
			}else{
				$data = array();
			}
		}
		return $data;
	}
	
	/**
	 * 获取标签数据
	 */
	private static function getLabelToCache(){
		$ckey= ":label:all";
		$data = self::_getCache()->hGetAll($ckey);
		if(empty($data)){
			$data = Resource_Service_Label::getsBy(array('status'=>1));
			if($data){
				self::saveDataToCache($ckey, $data);
				$data = self::jsonData($data);
			}else{
				$data = array();
			}
		}
		return $data;
	}
	
	/**
	 * 设置key过期时间
	 * @param string $ckey
	 * @param int $time
	 */
	private static function setCaheExpire($ckey, $time=self::CACHE_EXPRIE){
		self::_getCache()->expire($ckey, $time);
	}
	
	/**
	 * 获取cache实例
	 */
	private static function _getCache() {
		return Common::getCache();
	}
	
	public static function test(){
// 				$data = Resource_Service_AttributeCache::getAttributeBytype(1);
// 				$data = Resource_Service_AttributeCache::getAttributeByid(108);
// 				$data = Resource_Service_AttributeCache::getlabelByid(1);
// 		        $data = Resource_Service_AttributeCache::getlabelBytype(115);
// 				echo '<pre/>';
// 				print_r($data);
	}
}