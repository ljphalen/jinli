<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author rainkid
 *
 */
class Db_Pdo {
	static $db = null;

	/**
	 *
	 * @param array $config
	 * @throws PDOException
	 */
	static public function factory($config) {

		$key = md5(json_encode($config));

		if (self::$db[$key]) return self::$db[$key];

		if (!is_array($config)) {
			throw new PDOException('Db parameters must be in an array.');
		}
		/**
		 * username and password checked
		 */
		if (!$config['username'] || !$config['password']) {
			throw new PDOException("PDO connect access username or passwd.");
		}
		/**
		 *
		 */
		$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config['host'], $config['dbname']);
		try {
			self::$db[$key] = new Pdo($dsn, $config['username'], $config['password']);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}

		if ($config['displayError']) {
			self::$db[$key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		self::$db[$key]->exec("SET CHARACTER SET UTF8");
		return self::$db[$key];
	}
}
