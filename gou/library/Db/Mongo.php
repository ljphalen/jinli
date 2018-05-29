<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Mongo工厂
 * @author Terry
 *
 */
class Db_Mongo {
	static $instances = NULL;
	/**
	 * 
	 * @param array $config
	 * @throws MongoException
	 */
	static public function factory($config){
		if (!is_array($config)) {
			throw new MongoException('Db parameters must be in an array.');
		}

        $account = '';
        if($config['isauth']) $account = sprintf('%s:%s@', $config['username'], $config['password']);

		$key = md5(sprintf('%s%s/%s', $account, implode(',', $config['hosts']), $config['db']));

		if (isset(self::$instances[$key])) return self::$instances[$key];

		try{
			self::$instances[$key] = new Db_Adapter_Mongo($key, $config);
		} catch (MongoConnectionException $e) {
			throw new MongoConnectionException($e->getMessage());
		}

		return self::$instances[$key];
	}
}
