<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author rainkid
 *
 */
abstract class Cache_Base {

    public $expire = 30;
    public $version_num = 30;
    public static $interfaces;
    public static $revertMethods = array(
        "insert",
        "mutiInsert",
        "mutiInsertByKeys",
        "update",
        "updates",
        "updateBy",
        "replace",
        "increment",
        "delete",
        "deletes",
        "deleteBy"
    );
	
	/**
	 *
	 * @param unknown_type $method
	 * @param unknown_type $args
	 * @return mixed
	 */
	public function __call($method, $args) {
        //转换为小写
        $revertMethods = self::$revertMethods;
        array_walk($revertMethods, function(&$v){$v = strtolower($v);});

        //判断请求的方法是否在$revertMethods中或是否不属于Common_Dao_Base类中, 如果是, 都不执行缓存处理
		if (in_array(strtolower($method), $revertMethods)) {
            $result = call_user_func_array(array($this->_getDao(), $method), $args);
            $this->revertVersion();
            return $result;
		}

        /*if (!method_exists('Common_Dao_Base', $method)) {
            return call_user_func_array(array($this->_getDao(), $method), $args);
        }*/

		return $this->_cacheData($method, $args, $this->expire);
	}
	
	/**
	 * 
	 * @return unknown
	 */
	public function _getDao() {
		$daoName = str_replace("_Cache_", "_Dao_", get_class($this));
		if (!self::$interfaces[$daoName]) {
			self::$interfaces[$daoName] = new $daoName;
		}
        if (method_exists(self::$interfaces[$daoName], "initAdapter")) {
            self::$interfaces[$daoName]->initAdapter();
        }
		return self::$interfaces[$daoName];
	}
	
	/**
	 * 
	 * 处理高并发
	 * @param string $ckey
	 * @param string $method
	 * @param array $params
	 */
	protected function _cacheData($method, $params = array(), $expire = 30) {
        $ckey = self::_getKey($method, $params). ':' . $this->getVersion();
		$data = self::_getCache()->get($ckey);
		if ($data && ($data['cache_expires_timestamp'] - time() < 10)) {
			if (self::_getCache()->get($ckey . '_expire_lock')) return $data['cached_data'];
			self::_getCache()->set($ckey . '_expire_lock', true, 2);
			unset($data);
		}
		if (!$data) {
			$dbData = call_user_func_array(array($this->_getDao(), $method), $params);
			if (!$dbData) return $dbData == 0 ? 0 : array();
			$data['cache_expires_timestamp'] = time() + $expire;
			$data['cached_data'] = $dbData;
			try {
				self::_getCache()->set($ckey, $data, $expire);
				self::_getCache()->delete($ckey . '_expire_lock');
				for ($i = 0; $i <= $this->version_num; $i++) {
					if ($i != $this->getVersion()) {
						$pkey = self::_getKey($method, $params) . ':' . $i;
						self::_getCache()->delete($pkey);
					}
				}
			} catch(Exception $e) {
				
			}
		}
		return $data['cached_data'];
	}
	
	/**
	 * 
	 * @param unknown_type $method
	 * @param unknown_type $params
	 * @return string
	 */
	protected function _getKey($method, $params) {
		$key = get_class($this) . ':' . $method . ':' . json_encode($params);
		return substr(md5($key), 8, 16);
	}
	
	/**
	 * 
	 */
	public function revertVersion() {
		$key = get_class($this) . ':VERSION';
		if($version = self::_getCache()->get($key)) {
			if ($version == $this->version_num) $version = 0;
			self::_getCache()->set($key, $version + 1, 86400);
		} else {
			self::_getCache()->set($key, 1, 86400);
		}
	}
	
	/**
	 * 
	 */
	public function getVersion() {
		$key = get_class($this) . ':VERSION';
		return intval(self::_getCache()->get($key));
	}
	
	/**
	 * @return Cache_Redis
	 */
	protected static function _getCache() {
		return Common::getCache();
	}
}