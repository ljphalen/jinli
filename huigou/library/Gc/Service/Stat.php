<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gc_Service_Stat{
	
	/**
	 * 
	 */
	public static function incrementPv($pv) {
		$ret = self::_getPvDao()->getBy(array('dateline'=>date('Y-m-d')));
		if ($ret) {
			self::_getPvDao()->update(array('pv'=>$ret['pv'] + $pv), $ret['id']);
		} else {
			self::_getPvDao()->insert(array('pv'=>$pv,'dateline'=>date('Y-m-d')));
		}
		return true;
	}
	
	/**
	 * 
	 */
	public static function incrementIp($ip) {
		$ret = self::_getIpDao()->getBy(array('dateline'=>date('Y-m-d'), 'ip'=>$ip));
		if (!$ret) {
			self::_getIpDao()->insert(array('ip'=>$ip,'dateline'=>date('Y-m-d')));
		}
	}
	
	/**
	 * 
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getPvLineData($sDate, $eDate) {
		if (!$data = self::getPvList($sDate, $eDate)) return array();
		$lineData = array();
		$field = array(
				array('pv', '访问量'),
		);
		foreach($data as $k => $v) {
			foreach($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$lineData[$key]['name'] = $value[1];
					$lineData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $lineData);
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
				$temp[$value['dateline']]['total'] + 1;
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
	public function getPvList($sDate, $eDate) {
		return self::_getPvDao()->getListByTime($sDate, $eDate);
	}
	
	/**
	 * 
	 * @return Browser_Dao_Ip
	 */
	private static function _getIpDao() {
		return Common::getDao("Gc_Dao_Ip");
	}
	
	/**
	 *
	 * @return Browser_Dao_Pv
	 */
	private static function _getPvDao() {
		return Common::getDao("Gc_Dao_Pv");
	}
}
