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
	
	/**
	 *
	 * @param unknown_type $method
	 * @param unknown_type $args
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (!method_exists($this, $method)) {
			return $this->_cacheData($method, $args, $this->expire);
		}
		return call_user_func_array(array($this, $method), $args);
	}
	
	/**
	 *
	 * 插入数据
	 * @param array $data
	 */
	public function insert($data) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 *
	 * 插入多条数据
	 * @param array $data
	 */
	public function mutiInsert($data) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
		
	/**
	 *
	 * 更新数据并返回影响行数
	 * @param array $data
	 * @param int $value
	 */
	public function update($data, $value) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 * 批量更新并返回执行结果
	 * @param unknown_type $field
	 * @param unknown_type $values
	 * @return boolean|Ambigous <boolean, number>
	 */
	public function updates($field, $values, $data) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 * 指量更新
	 * @param array $data
	 * @param array $params
	 * @return boolean
	 */
	public function updateBy($data, $params) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 * 替换插入操作
	 * @param unknown_type $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public function replace($data) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 * 
	 * @param array $field
	 * @param array $params
	 * @param int $step
	 * @return mixed
	 */
	public function increment($field, $params, $step = 1) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 *
	 * 删除数据并返回影响行数
	 * @param int $value
	 */
	public function delete($value) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 *
	 * 删除多条数据并返回执行结果
	 * @param int $value
	 */
	public function deletes($field, $values) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	
	/**
	 * 指定删除
	 * @param array $params
	 * @return boolean
	 */
	public function deleteBy($params) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 * 
	 * @return unknown
	 */
	public function _getDao() {
		$daoName = str_replace("_Cache_", "_Dao_", get_class($this));
		return new $daoName;
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