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
	
    //本地缓存有效时间
    const LOCAL_CACHE_EXPIRE = 90;
	/**
	 * 更新属性数据到缓存
	 */
	public static function saveAtrributeToCache(){
		$ckey = Util_CacheKey::GAME_ALL_ATTRIBUTES;
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
	
    private static function getAttributeCacheKey($attrId) {
        return Util_CacheKey::GAME_ATTRIBUTE . $attrId;
    }

    private static function saveAttributeInLocalCache($attrId, $attribute) {
        $cache = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
        $key = self::getAttributeCacheKey($attrId);
        return $cache->set($key, $attribute, self::LOCAL_CACHE_EXPIRE);
    }

    private static function getAttributeFromLocalCache($attrId) {
        $cache = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
        $key = self::getAttributeCacheKey($attrId);
        return $cache->get($key);
    }

    /**
     * 根据id获取属性值
     * @param int $type
     */
    
    public static function getAttributeByid($id){
        $attribute = self::getAttributeFromLocalCache($id);
        if (false === $attribute) {
            $attribute = self::getAttributeToCache($id);
            if(!$attribute) {
                return array();
            }
            self::saveAttributeInLocalCache($id, $attribute);
        }
        return json_decode($attribute, true);
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
	private static function getAttributeToCache($id){
		$ckey = Util_CacheKey::GAME_ALL_ATTRIBUTES;
		$attribute = self::_getCache()->hGet($ckey, $id);
		if(!empty($attribute)){
			return $attribute;
		}

		$allAttributes = array();
		$allAttributes = Resource_Service_Attribute::getsBy(array('status'=>1));
		if($allAttributes){
			self::saveDataToCache($ckey, $allAttributes);
			$allAttributes = self::jsonData($allAttributes);
			return $allAttributes[$id];
		}

		return array();
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
		return Cache_Factory::getCache();
	}
}