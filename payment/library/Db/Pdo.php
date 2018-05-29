<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Db_Pdo {
	static $instances = NULL;
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $config
	 * @throws PDOException
	 */
	static public function factory($config){
		if (!is_array($config)) {
			throw new PDOException('Db parameters must be in an array.');
		}
		if (!$config['username'] || !$config['password']) {
			throw new PDOException("PDO connect access username or passwd.");
		}
		$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config['host'], $config['dbname']);
		$key = md5($dsn);
		if (isset(self::$instances[$key])) return self::$instances[$key];

		try{
			self::$instances[$key] = new Pdo($dsn, $config['username'], $config['password'], array(PDO::MYSQL_ATTR_LOCAL_INFILE => TRUE));
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}
		
		if ($config['displayError']) {
			self::$instances[$key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
 		self::$instances[$key]->exec("SET CHARACTER SET UTF8");
		return self::$instances[$key];
	}
}
