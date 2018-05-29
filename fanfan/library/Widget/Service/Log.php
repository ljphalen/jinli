<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Log {

	static $callKeys = array(
		'index'    => 'v1新聞',
		'version'  => 'v1类别',
		'version2' => 'v2版本',
		'sort'     => 'v2列表',
		'news'     => 'v2新闻',
		'column'   => 'v2栏目',
		'res'      => 'v2资源',
		'down'     => 'v2下载',
	);

	const TYPE_CALL        = 'call';
	const TYPE_NEWS        = 'news';
	const TYPE_CPURL       = 'cpurl';
	const TYPE_RES         = 'res';
	const TYPE_DOWN        = 'down';
	const TYPE_NEWS_DETAIL = 'news_detail';
	const TYPE_TOPIC       = 'topic';

	static $types = array(
		self::TYPE_CALL,
		self::TYPE_NEWS,
		self::TYPE_CPURL,
		self::TYPE_RES,
		self::TYPE_DOWN,
		self::TYPE_NEWS_DETAIL,
		self::TYPE_TOPIC,
	);

	public static function incrBy($type, $key, $num = 1) {
		$nowDay = date('Ymd');
		Common::getCache()->hIncrBy($type . '_' . $nowDay, $key, $num);
	}

	public static function sync2DB($type) {
		$nowDay = date('Ymd');
		$key    = $type . '_' . $nowDay;
		$list   = Common::getCache()->hGetAll($key);
		Common::getCache()->delete($key);
		foreach ($list as $key => $num) {

			$tmpKey = explode(':', $key);
			$params = array(
				'date' => $nowDay,
				'type' => $type,
				'key'  => isset($tmpKey[0]) ? $tmpKey[0] : '',
			);

			if (!empty($tmpKey[1])) { //版本
				$params['ver'] = $tmpKey[1];
			}

			$row = Widget_Service_Log::getBy($params);

			if (!empty($row['id'])) {
				$params['val'] = $row['val'] + $num;
				Widget_Service_Log::set($params, $row['id']);
			} else {
				$params['val'] = $num;
				Widget_Service_Log::add($params);
			}
		}

	}

	public static function getNumByNewsKey($val) {
		$total = self::_getDao()->getNumByTypeOfKey(self::TYPE_NEWS, $val);
		return $total;
	}


	public static function getLastIdByType($type, $limit) {
		$ret  = array();
		$list = self::_getDao()->getLastIdByType($type, $limit);
		foreach ($list as $v) {
			$ret[] = $v['key'];
		}
		return $ret;
	}

	/**
	 * @param string $type
	 * @param array $keys
	 * @param string $sDate
	 * @param string $eDate
	 * @return array
	 */
	public static function getStatData($type, $keys, $sDate, $eDate) {
		if (empty($type) || empty($keys) || !is_array($keys)) {
			return false;
		}
		$params = array(
			'type' => $type,
			'key'  => array('IN', $keys),
			'date' => array(array('>=', $sDate), array('<=', $eDate))
		);
		//$list   = self::_getDao()->getsBy($params);
		$list   = self::_getDao()->getTotalByKey($params);
		$pvData = array();
		foreach ($list as $v) {
			$d                  = date('Y-m-d', strtotime($v['date']));
			$num                = $v['num'];
			$k                  = $v['key'];
			$kName              = self::keyName($type, $k);
			$pvData[$kName][$d] = $num;
		}

		return $pvData;
	}

	public static function keyName($type, $k, &$urlId = 0) {
		$name = $k;
		if ($type == 'down' || $type == 'res') {
			$name = Widget_Service_Cp::$CpCate[$k][0];
		} else if ($type == 'call') {
			$name = self::$callKeys[$k];
		} else if ($type == 'cpurl') {
			$info = Widget_Service_Cp::get($k);
			$name = $info['title'];
		} else if ($type == 'news') {
			$info  = Widget_Service_Source::get($k);
			$name  = $info['title'];
			$urlId = $info['url_id'];
		}
		return $name;
	}

	/**
	 *
	 * @param string $data
	 */
	public static function get($date) {
		return self::_getDao()->get($date);
	}

	public static function getBy($param) {
		if (!is_array($param)) return false;
		return self::_getDao()->getBy($param);
	}

	public static function getsBy($param, $orderBy = array()) {
		if (!is_array($param)) return false;
		return self::_getDao()->getsBy($param, $orderBy);
	}

	/**
	 *
	 * @param array $data
	 * @param string $date
	 */
	public static function set($data, $date) {
		if (!is_array($data)) return false;
		return self::_getDao()->update($data, $date);
	}

	/**
	 *
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * @return Widget_Dao_Vist
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Log");
	}
}
