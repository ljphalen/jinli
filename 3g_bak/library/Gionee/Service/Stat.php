<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_Stat {
	/**
	 *
	 * @param int $pv
	 * @return bool
	 */
	public static function incrementPv($pv, $type) {
		$ret = self::_getPvDao()->getBy(array('dateline' => date('Y-m-d'), 'tj_type' => intval($type)));
		if ($ret) return self::_getPvDao()->update(array('pv' => $pv + $ret['pv']), $ret['id']);
		return self::_getPvDao()->insert(array('pv' => $pv, 'tj_type' => intval($type), 'dateline' => date('Y-m-d')));
	}

	/**
	 *
	 * @param int $pv
	 * @return bool
	 */
	public static function incrementUv($uv) {
		$ret = self::_getUvDao()->getBy(array('dateline' => date('Y-m-d')));
		return self::_getUvDao()->replace(array('uv' => $ret['uv'] + $uv, 'dateline' => date('Y-m-d')));
	}

	public static function getPvLine($type, $sDate, $eDate, $name) {
		if (!$data = self::getPvList($sDate, $eDate, array('tj_type' => $type))) return array();
		$pvData = array();
		$field  = array(
			array('pv', $name),
		);
		foreach ($data as $k => $v) {
			foreach ($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$pvData[$key]['name']   = $value[1];
					$pvData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $pvData);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getPvLineData($sDate, $eDate) {
		if (!$data = self::getPvList($sDate, $eDate, array('tj_type' => 0))) return array();
		$pvData = array();
		$field  = array(
			array('pv', '全站PV'),
		);
		foreach ($data as $k => $v) {
			foreach ($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$pvData[$key]['name']   = $value[1];
					$pvData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $pvData);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|multitype:Ambigous <multitype:, multitype:number NULL > multitype:
	 */
	public static function getIndexPvLineData($sDate, $eDate) {
		if (!$data = self::getPvList($sDate, $eDate, array('tj_type' => 1))) return array();
		$pvData = array();
		$field  = array(
			array('pv', '首页PV'),
		);

		//'新闻PV','导航pv'
		foreach ($data as $k => $v) {
			foreach ($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$pvData[$key]['name']   = $value[1];
					$pvData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $pvData);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getUvLineData($sDate, $eDate) {
		if (!$data = self::getUvList($sDate, $eDate)) return array();
		$lineData = array();
		$field    = array(
			array('uv', '访问量'),
		);
		foreach ($data as $k => $v) {
			foreach ($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$lineData[$key]['name']   = $value[1];
					$lineData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $lineData);
	}

	public static function getUvData($params,$sDate,$eDate,$other=''){
		if(empty($params)) return false;
		$types = array_keys($params);
		$criteria = array();
		is_array($types)?$criteria['type'] = array("IN",$types):'';
		$criteria['date'] = array(array('>=',date('Ymd',strtotime($sDate))),array("<=",date('Ymd',strtotime($eDate))));
		$dataList = Gionee_Service_Log::getsBy($criteria);
		if(empty($dataList)) return array();
		$lineData = array();
		foreach ($dataList as $k=>$v){
			$lineData[$v['type']]['name'] = $params[$v['type']];
			$lineData[$v['type']]['data'][] = array((intval(strtotime($v['date']))* 1000),intval($v['val']));

		}
		return $lineData;
	}
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:
	 */
	public function getUvList($sDate, $eDate) {
		return self::_getUvDao()->getListByTime($sDate, $eDate);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getIpLineData($sDate, $eDate) {
		if (!$list = self::getIpList($sDate, $eDate)) return array();
		$dataIp = self::_resetArray($list);

		$data     = self::_resetArray($list);
		$lineData = array();
		$field    = array(
			array('total', '访问量'),
		);
		foreach ($data as $k => $v) {
			foreach ($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$lineData[$key]['name']   = $value[1];
					$lineData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($dataIp, $lineData);
	}

	/**
	 *
	 */
	public static function _resetArray($list) {
		$temp = array();
		foreach ($list as $key => $value) {
			if ($temp[$value['dateline']]) {
				$temp[$value['dateline']]['total'] = $temp[$value['dateline']]['total'] + 1;
			} else {
				$temp[$value['dateline']]['dateline'] = $value['dateline'];
				$temp[$value['dateline']]['total']    = 1;
			}
		}
		return $temp;
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:
	 */
	public static function getIpList($sDate, $eDate) {
		return self::_getIpDao()->getListByTime($sDate, $eDate);
	}

	/**
	 *
	 * @param unknown_type $Date
	 * @return multitype:
	 */
	public static function getIpCount($sDate, $eDate) {
		return self::_getIpDao()->getCountByTime($sDate, $eDate);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:
	 */
	public function getPvList($sDate, $eDate, $params = array()) {
		return self::_getPvDao()->getListByTime($sDate, $eDate, $params);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:
	 */
	public function searchPvList($params = array()) {
		return self::_getPvDao()->searchPvList($params);
	}

	/**
	 *
	 * @return Gionee_Dao_Uv
	 */
	private static function _getUvDao() {
		return Common::getDao("Gionee_Dao_Uv");
	}

	/**
	 *
	 * @return Gionee_Dao_Pv
	 */
	private static function _getPvDao() {
		return Common::getDao("Gionee_Dao_Pv");
	}
}
