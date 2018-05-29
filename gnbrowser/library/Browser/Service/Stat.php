<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_Stat{
	
	/**
	 * 
	 */
	public static function incrementPv($pv, $type) {
		$ret = self::_getPvDao()->where(array('dateline'=>date('Y-m-d'), 'tj_type'=>intval($type)));
		if ($ret) {
			self::_getPvDao()->update(array('pv'=>$ret['pv'] + $pv), $ret['id']);
			return true;
		} else {
			self::_getPvDao()->insert(array('pv'=>$pv, 'tj_type'=>intval($type), 'dateline'=>date('Y-m-d')));
			return true;
		}
	}
	
	/**
	 * 
	 */
	public static function incrementIp($arr_ip) {
		if (is_array($arr_ip)){
			$arr = array();
			foreach ( $arr_ip as $key => $val) {
				$ret = self::_getIpDao()->where(array('dateline'=>date('Y-m-d'), 'ip'=>$val));
				if (!$ret) {
					$arr[$key]['id'] = '';
					$arr[$key]['ip'] = $val;
					$arr[$key]['dateline'] = date('Y-m-d');
				}				
			}
			if ($arr) {
				sort($arr);
				self::_getIpDao()->mutiInsert($arr);
				return true;
			}
			
		}
	}
	
	/**
	 * 
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getPvLineData($sDate, $eDate) {
		if (!$data = self::getPvList($sDate, $eDate, array('tj_type'=>0))) return array();
		$pvData = array();
		$field = array(
				array('pv', '全站PV'),
		);
		foreach($data as $k => $v) {
			foreach($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$pvData[$key]['name'] = $value[1];
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
	public static function getIndexPvLineData($sDate, $eDate){
		if (!$data = self::getPvList($sDate, $eDate, array('tj_type'=>1))) return array();
		$pvData = array();
		$field = array(
				array('pv', '首页PV'),
		);
		foreach($data as $k => $v) {
			foreach($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$pvData[$key]['name'] = $value[1];
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
	public static function getIpLineData($sDate, $eDate) {
		if (!$list = self::getIpList($sDate, $eDate)) return array();
		$dataIp = self::_resetArray($list);
				
		$data = self::_resetArray($list);
		$lineData = array();
		$field = array(
				array('total', '访问量'),
		);
		foreach($data as $k => $v) {
			foreach($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$lineData[$key]['name'] = $value[1];
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
		foreach ($list as $key=>$value) {
			if ($temp[$value['dateline']]) {
				$temp[$value['dateline']]['total'] = $temp[$value['dateline']]['total'] + 1;
			} else {
				$temp[$value['dateline']]['dateline'] = $value['dateline'];
				$temp[$value['dateline']]['total'] = 1;
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
	 * @return Gionee_Dao_Ip
	 */
	private static function _getIpDao() {
		return Common::getDao("Gionee_Dao_Ip");
	}
	
	/**
	 *
	 * @return Gionee_Dao_Pv
	 */
	private static function _getPvDao() {
		return Common::getDao("Gionee_Dao_Pv");
	}
}
