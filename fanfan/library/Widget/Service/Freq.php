<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Freq {

	public static function calcPerNum($times, $num) {
		$times = explode(',', $times);
		$perNum = floor($num/count($times));
		return $perNum;
	}

	public static function filterSort() {
		$tmpList = self::_getDao()->getsBy(array('status'=>1), array('sort'=>'DESC'));
		foreach($tmpList as $val) {
			$ret[$val['url_id']] = $val;
		}
		return $ret;
	}

	public static function existUrlId($urlId, $id=0) {
		$where = array('url_id'=>$urlId);
		if ($id) {
			$where['id'] = array('!=', $id);
		}
		$info = self::_getDao()->getBy($where);
		if (!empty($info['id'])) {
			return true;
		}
		return false;
	}

	static public function run() {
		$t          = date('Y-m-d H:i:s');
		$out        = "[{$t}] Start => ";
		$list       = Widget_Service_Freq::all();
		foreach ($list as $val) {
			$times    = explode(',', $val['time']);
			$nowMin   = date('H:i');
			$totalNum = $val['num'];
			$perNum   = floor($val['num'] / count($times));
			$nowNum  = Widget_Service_Source::calcNumByNowDay($val['url_id']);
			$out .= " url_id:{$val['url_id']},({$totalNum}-{$nowNum}|{$perNum}) - ({$nowMin}=>{$val['time']}) ";
			if (in_array($nowMin, $times)) {
				$diffNum = $totalNum - $nowNum;
				$now     = time();
				if ($diffNum > 0) {
					$needNum = min($diffNum, $perNum);
					$out .= " (needNum:{$needNum}) ";
					$tmpList = Widget_Service_Source::getHistoryList($val['url_id'], $needNum, $val['history_day']);

					$ids = array();
					foreach ($tmpList as $tmpVal) {
						$ids[]                 = $tmpVal['id'];
						$tmpVal['create_time'] = time();
						Widget_Service_Source::update(array('create_time' => $now), $tmpVal['id']);
					}
					$out .= implode(',', $ids);
				}
			}
			$out .= "; ";
		}
		$out .= "End\n";
		return $out;
	}

	/**
	 * @param string $data
	 */
	public static function get($id) {
		return self::_getDao()->get($id);
	}

	public static function getBy($param) {
		if (!is_array($param)) {
			return false;
		}
		return self::_getDao()->getBy($param);
	}

	public static function all($orderBy=array()) {
		$list = self::_getDao()->getAll($orderBy);
		return $list;
	}
	/**
	 *
	 * @param array $data
	 * @param string $date
	 */
	public static function set($data, $id) {
		if (!is_array($data)) return false;
		return self::_getDao()->update($data, $id);
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}

	public static function del($id) {
		if (!$id) return false;
		return self::_getDao()->delete($id);
	}

	/**
	 *
	 * @return Widget_Dao_Freq
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Freq");
	}
}
