<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Db_Adapter_Pdo {
	
	protected $_name = '';
	protected $_primary = '';
	
	/**
	 * 
	 * 设置默认的dbadapter
	 * @param PDO $adapter
	 */
	public static function setDefaultAdapter($adapter) {
		if (!$adapter instanceof PDO) {
			throw new Exception("The Adapter must be instanceof PDO.");
		}
		self::_registryAdapter('defaultAdapter', $adapter);
		self::setAdapter('defaultAdapter');
		return $adapter;
	}
	
	/**
	 * 
	 * 注册一个dbAdapter
	 * @param string $name
	 * @param PDO $adapter
	 */
	public static function rigistryAdapter($name, $adapter) {
		if (!$adapter instanceof PDO) {
			throw new Exception("The Adapter ".$name." is not instanceof PDO.");
		}
		return self::_registryAdapter($name . 'Adapter', $adapter);
	}
	
	/**
	 * 
	 * 设置dbAdapter
	 * @param string $name
	 */
	public static function setAdapter($name) {
		if (!is_string($name)) {
			return false;
		}
		if (!Yaf_Registry::get($name)) {
			throw new Exception("The adapter ".$name." not rigistry");
		}
		Yaf_Registry::set('dbAdapter', $name);
		return $name;
	}
	
	/**
	 * 
	 * 获取注册的dbAdapterName
	 */
	public static function getAdaterName() {
		return Yaf_Registry::get('dbAdapter');
	}
	
	/**
	 * 
	 * 查询一条结果集
	 * @param string $sql
	 * @param array $params
	 * @param Int $fetch_style
	 */
	public static function fetch($sql, $params = array(), $fetch_style = PDO::FETCH_ASSOC) {
		$stmt = self::getStatement($sql, $params);
		return $stmt->fetch($fetch_style);
	}
	
	/**
	 * 
	 * 查询column列结果
	 * @param string $sql
	 * @param array $cloum
	 */
	public static function fetchCloum($sql, $column_number = null, $params = array()) {
		$stmt = self::getStatement($sql, $params);
		return $stmt->fetchColumn($column_number);
	}
	
	/**
	 * 
	 * 查询所有结果集
	 * @param string $sql
	 * @param array $params
	 * @param int $fetch_style
	 */
	public static function fetchAll($sql, $params = array(), $fetch_style = PDO::FETCH_ASSOC) {
		$stmt = self::getStatement($sql, $params);
		return $stmt->fetchAll($fetch_style);
	}
	
	/**
	 * 
	 * 执行sql并返回影响行数
	 * @param array $params
	 * @param bool $rowCount
	 */
	public static function execute($sql, $params = array(), $rowCount = false) {
		$stmt = self::getAdapter()->prepare($sql);
		$stmt->execute($params);
		return $rowCount ? $stmt->rowCount() : true;
		;
	}
	
	/**
	 * 
	 * 获取PDOStatement
	 * @param string $sql
	 * @param array $params
	 */
	public static function getStatement($sql, $params = array()) {
		$stmt = self::getAdapter()->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}
	
	/**
	 * 获取后插入的id
	 */
	public static function getLastInsertId() {
		return self::getAdapter()->lastInsertId();
	}
	
	/**
	 * 
	 * 批量绑定参数
	 * @param PDOStament $stmt
	 * @param array $params
	 */
	public static function bindValues($stmt, $params) {
		if (!is_array($params)) throw new Exception('Error unexpected paraments type' . gettype($params));
		$keied = (array_keys($params) !== range(0, sizeof($params) - 1));
		foreach ($params as $key => $value) {
			if (!$keied) $key = $key + 1;
			$stmt->bindValue($key, $value, self::_getDataType($value));
		}
	}
	
	/**
	 * 
	 * 字符串过滤
	 * @param string $string
	 * @param int $parameter_type
	 */
	public static function quote($string, $parameter_type = null) {
		return self::getAdapter()->quote($string, $parameter_type);
	}
	
	/**
	 * 
	 * 解析多个占位符
	 * @param string $text
	 * @param array $value
	 * @param int $type
	 * @param int $count
	 */
	public static function quoteInto($text, $value, $type = null, $count = null) {
		if ($count === null) {
			return str_replace('?', self::quote($value, $type), $text);
		} else {
			while ($count > 0) {
				if (strpos($text, '?') !== false) {
					$text = substr_replace($text, self::quote($value, $type), strpos($text, '?'), 1);
				}
				--$count;
			}
			return $text;
		}
	}
	
	/**
	 * 
	 * 过滤数组转换成sql字符串
	 * @param array $params
	 */
	
	public static function quoteArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = self::quote($value);
		}
		return '(' . implode(', ', $_returns) . ')';
	}
	
	/**
	 * 
	 * 过滤二维数组将数组变量转换为多组的sql字符串
	 * @param array $var
	 */
	public static function quoteMultiArray($var) {
		if (empty($var) || !is_array($var)) return '';
		$_returns = array();
		foreach ($var as $val) {
			if (!empty($val) && is_array($val)) {
				$_returns[] = self::quoteArray($val);
			}
		}
		return implode(', ', $_returns);
	}
	
	/**
	 * 
	 * 组装单条 key=value 形式的SQL查询语句值
	 * @param array $array
	 */
	public static function sqlSingle($array) {
		if (!is_array($array)) return '';
		$str = array();
		foreach ($array as $key => $val) {
			$str[] = self::fieldMeta($key) . '=' . self::quote($val);
		}
		return $str ? implode(',', $str) : '';
	}
	
	/**
	 * 
	 * @param unknown_type $array
	 * @return string
	 */
	public static function sqlWhere($array) {
		if (!is_array($array)) return '';
		$str = array();
		foreach ($array as $key => $val) {
			$str[] = self::fieldMeta($key) . '=' . self::quote($val);
		}
		return $str ? implode(' AND ', $str) : '';
	}
	
	/**
	 * 
	 * sql关键字段过滤
	 * @param array $data
	 */
	public static function fieldMeta($data) {
		$data = str_replace(array('`',' '), '', $data);
		return ' `' . $data . '` ';
	}
	
	/**
	 * 
	 * @return PDO
	 */
	public static function getAdapter() {
		$adapterName = self::getAdaterName();
		return Yaf_Registry::get($adapterName);
	}
	
	/**
	 * 
	 * 注册dbAdapter
	 * @param string $name
	 * @param PDO $adapter
	 */
	private static function _registryAdapter($name, $adapter) {
		if ($adapter === null) {
			return false;
		}
		Yaf_Registry::set($name, $adapter);
		return $adapter;
	}
	
	/**
	 * 获得绑定参数的类型
	 * 
	 * @param string $variable
	 * @return int
	 */
	private static function _getDataType($var) {
		$types = array('boolean' => PDO::PARAM_BOOL,'integer' => PDO::PARAM_INT,'string' => PDO::PARAM_STR,
					'NULL' => PDO::PARAM_NULL);
		return isset($types[gettype($var)]) ? $types[gettype($var)] : PDO::PARAM_STR;
	}
}