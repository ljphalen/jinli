<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class W3_Service_StatsData {

	//每日新增用户 created_at
	const TYPE_NEW_USER = 'new_user';
	//每日订阅栏目百分比 column_id  array(栏目ID=>数量)
	const TYPE_COLUMN = 'column';
	//每日网络百分比 net  array(net=>数量)
	const TYPE_NET = 'net';
	//每日订阅分类百分比 url_id array(url_id=>数量)
	const TYPE_URL_ID = 'url_id';
	//每日版本分布 app_ver array(app_ver=>数量)
	const TYPE_APP_VER = 'app_ver';
	//每日活跃用户 last_visit_time
	const TYPE_VISIT_USER = 'visit_user';

	static $types = array(
		self::TYPE_NEW_USER,
		self::TYPE_COLUMN,
		self::TYPE_NET,
		self::TYPE_URL_ID,
		self::TYPE_APP_VER,
		self::TYPE_VISIT_USER,
	);


	/**
	 * @param string $type
	 * @param array $keys
	 * @param string $sDate
	 * @param string $eDate
	 * @return array
	 */
	public static function getData($type, $sDate, $eDate) {
		if (empty($type)) {
			return false;
		}
		$params = array(
			'type' => $type,
			'date' => array(array('>=', $sDate), array('<=', $eDate))
		);
		$list   = self::_getDao()->getsBy($params);
		$pvData = array();
		foreach ($list as $v) {
			$d     = date('Y-m-d', strtotime($v['date']));
			$num   = $v['num'];
			$k     = $v['key'];
			if ($type{0} == 2) {
				$kName = self::getName2($type, $k);
			}  else {
				$kName = self::getName($type, $k);
			}
			$pvData[$kName][$d] = $num;
		}

		return $pvData;
	}

	/**
	 * 2.0版本对应的数据名词
	 * @param $type
	 * @param $v
	 * @return array
	 */
	static public function getName2($type, $v) {
		static $list = array();
		if (isset($list[$type][$v])) {
			return $list[$type][$v];
		}
		if ($type == '2column_id') {
			$info            = Widget_Service_Column::get($v);
			$list[$type][$v] = !empty($info['title'])?$info['title']:$v;
		} else if ($type == '2url_id') {
			$info            = Widget_Service_Cp::get($v);
			$list[$type][$v] = !empty($info['title'])?$info['title']:$v;
		} else if ($type == '2num') {
			$arr = array('2total_num'=>'总用户数','2visit_num'=>'活跃用户数');
			$list[$type][$v] = $arr[$v];
		} else {
			$list[$type][$v] = $v;
		}
		return $list[$type][$v];
	}

	/**
	 * 3.0版本对应的数据名词
	 * @param $type
	 * @param $v
	 * @return array
	 */
	static public function getName($type, $v) {
		static $list = array();
		if (isset($list[$type][$v])) {
			return $list[$type][$v];
		}
		if ($type == 'column_id') {
			$info            = W3_Service_Column::get($v);
			$list[$type][$v] = !empty($info['title'])?$info['title']:$v;
		} else if ($type == 'url_id') {
			$info            = Widget_Service_Cp::get($v);
			$list[$type][$v] = !empty($info['title'])?$info['title']:$v;
		} else if ($type == 'num') {
			$arr = array('total_num'=>'总用户数','visit_num'=>'活跃用户数');
			$list[$type][$v] = $arr[$v];
		} else {
			$list[$type][$v] = $v;
		}
		return $list[$type][$v];
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
		return Common::getDao("W3_Dao_StatsData");
	}
}
