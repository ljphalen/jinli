<?php 
/**
 * 缓存类，输出缓存和文件数据缓存
 * @author jiangxinyu
 *
 * 调用示例代码：
 * 例如1：数据缓存 
		if (!$data = DataCache::get('test', '2012-03-22')) {
			$data ='this is a testing!';
			DataCache::put('test', '2012-03-22', $data, 2);
		}
     例如2：输出缓存
	     if (!OutputCache::start('group', 'unique id', 600)) { 
	   		// ... Output
	    	OutputCache::end();
		 }
 */

class Cache {
	/**
	 * 是否启用
	 * @var boolean
	 */
	public static $enabled = true;
	
	/**
	 * 文件名是否需要MD5处理
	 * @var boolean
	 */
	public static $ismd5   = false;
	
	/**
	 * 是否设置过期时间，false不启用， true启用
	 * @var boolean
	 */
	public static $isttl = false;
	
	/**
	 * 缓存文件存储路径
	 * @var string
	 */
	protected static $store = './';
	
	/**
	 * 缓存文件名前缀
	 * @var string
	 */
	protected static $prefix = 'cache_';
	
	/**
	 * 存储数据
	 * @param string $group    分组存储在这个组下
	 * @param string $id          文件id是唯一，可以是字符串或数字
	 * @param int $ttl              设置文件过期时间，以秒为单位
	 * @param unknown_type $data   需要缓存的数据
	 */
	protected static function write($group, $id, $data, $ttl = 0) {
		$filename = self::getFilename($group, $id);		
		if ($fp = @fopen($filename, 'xb')) {		
			if (flock($fp, LOCK_EX)) {
				fwrite($fp, $data);
			}
			fclose($fp);		
			touch($filename, time() + $ttl);   //设置文件过期时间
		}		
	}
	
	/**
	 * 读取缓存数据
	 * @param string $group   分组存储在这个组下
	 * @param string $id		 文件id是唯一，可以是字符串或数字
	 * @return string
	 */
	protected static function read($group, $id) {
		$filename = self::getFilename($group, $id);
		return file_get_contents($filename);
	}
	
	/**
	 * 判断缓存文件是否存在，过期的文件会删除掉
	 * @param string $group  分组存储在这个组下
	 * @param string $id        文件id是唯一，可以是字符串或数字
	 * @return boolean
	 */
	protected static function isCached($group, $id) {
		$filename =self::getFilename($group, $id);
		if (self::$enabled && file_exists($filename)) {
			if(!self::$isttl) {
				return true;
			} elseif(filemtime($filename) > time()) {
				return true;
			}	
		}
		//是否启用缓存时间
		if(self::$isttl) {
			echo $filename;
			@unlink($filename);
		}
		return false;
	}
	
	/**
	 * 获取文件名
	 * @param string $group   分组存储在这个组下
	 * @param string $id         文件id是唯一，可以是字符串或数字
	 * @return string
	 */
	protected static function getFilename($group, $id) {
		if(self::$ismd5) {
			$id = md5($id);	
		}	
		return self::$store . self::$prefix . "{$group}_{$id}";
	}
	
	/**
	 * 设置缓存文件前缀
	 * @param string $prefix
	 */
	public static function setPrefix($prefix) {
		self::$prefix = $prefix;
	}
	
	/**
	 * 设置换文件的存储路径
	 * @param string $store
	 */
	public static function setStore($store) {
		self::$store = $store;
	}
	
	/**
	 * 设置是否启用缓存时间，false的时候不启用缓存时间,默认是启用的啊
	 * @param unknown_type $isttl
	 */
	public static function setIsttl($isttl) {
		self::$isttl = $isttl;
	}
}

/**
 * 数据缓存类， 把数据写入文件缓存
 * @author jiangxinyu
 * 调用示例代码：
 * 例如1：数据缓存 
		if (!$data = DataCache::get('test', '2012-03-22')) {
			$data ='this is a testing!';
			DataCache::put('test', '2012-03-22', $data, 2);
		}
		
 */
class DataCache extends Cache {
	/**
	 * 获取数据缓存
	 * @param string $group  分组存储在这个组下
	 * @param string $id        文件id是唯一，可以是字符串或数字
	 * @return mixed
	 */
	public static function get($group, $id) {	
		if (self::isCached($group, $id)) {
			$data = self::read($group, $id);
			return @unserialize(self::read($group, $id));
		}
		return null;	
	}
	
	/**
	 * 存储数据库缓存
	 * @param string $group 分组存储在这个组下
	 * @param string $id       文件id是唯一，可以是字符串或数字
	 * @param unknown_type $data       缓存数据
	 * @param int $ttl           设置文件过期时间，以秒为单位
	 */
	public static function put($group, $id, $data,$ttl = 0) {
		self::write($group, $id, serialize($data), $ttl);
	}
}

/**
 * 文件输出缓存
 * @author jiangxinyu
 * 例如2：输出缓存
	     if (!OutputCache::start('group', 'unique id', 600)) { 
	   		// ... Output
	    	OutputCache::end();
		 }
 */
class OutputCache extends Cache {
	private static $group;
	private static $id;
	private static $ttl;	
	/**
	 * 缓存是否存在。如果缓存返回true，阻止输出。假如果没有缓存和启动输出缓冲。
	 * @param string $group   分组存储在这个组下
	 * @param string $id         文件id是唯一，可以是字符串或数字
	 * @param int $ttl             设置文件过期时间，以秒为单位
	 * @return boolean
	 */
	public static function start($group, $id, $ttl = 0) {
		if (self::isCached($group, $id)) {
			echo self::read($group, $id);
			return true;
		} else {	
			ob_start();	
			self::$group = $group;
			self::$id    = $id;
			self::$ttl   = $ttl;
			return false;
		}	
	}
	
	/**
	 * 把缓存写入磁盘
	 */
	public static function end() {
		$data = ob_get_contents();
		ob_end_flush();
		self::write(self::$group, self::$id, self::$ttl, $data);
	}
}
?>