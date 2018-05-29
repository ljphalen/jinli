<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_Stat{
	
	/**
	 * 
	 * @param int $pv
	 * @return bool
	 */
	public static function incrementPv($pv) {
		$ret = self::_getPvDao()->getBy(array('dateline'=>date('Y-m-d')));
		return self::_getPvDao()->replace(array('pv'=>$ret['pv'] + $pv, 'dateline'=>date('Y-m-d')));
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
	public function getPvList($sDate, $eDate) {
		return self::_getPvDao()->getListByTime($sDate, $eDate);
	}
	
	
	/**
	 *
	 * @return Gou_Dao_Pv
	 */
	private static function _getPvDao() {
		return Common::getDao("Gou_Dao_Pv");
	}
}
