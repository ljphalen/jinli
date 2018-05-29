<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_Cr{
	

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllCr() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$data = self::_cookData($params);
		$data['sdate'] = $params['sdate'];
		$data['edate'] = $params['edate'];
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getCrDao()->getCrList($start, $limit, $data);
		$total = self::_getCrDao()->getCount($data);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public static function searchCrList($params) {
		return self::_getCrDao()->searchCrList($params);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public static function getTotalByTime($sDate, $eDate) {
		return self::_getCrDao()->getTotalByTime($sDate, $eDate);
	}
	
	/**
	 *
	 * @param unknown_type $Date
	 * @return multitype:
	 */
	public static function getCrCount($sDate, $eDate) {
		return self::_getCrDao()->getCountByTime($sDate, $eDate);
	}
		
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function incrementCr($cr) {
		if(is_array($cr)){
			foreach ( $cr as $key => $val ) {
				if ($val['url']) {
					$ret = self::_getCrDao()->get(array('dateline'=>date('Y-m-d'), 'md5_url'=>$key));
					if ($ret) {
						self::_getCrDao()->updateByUrlAndDate(array('click'=>$ret['click'] + $val['click'], 'category_id'=>$val['category_id']), $ret['md5_url'], $ret['dateline']);
					} else {
						self::_getCrDao()->insert(array('url'=>$val['url'],'md5_url'=>$key,'click'=>$val['click'], 'category_id'=>$val['category_id'],'dateline'=>date('Y-m-d')));
					}
				}
			}
			return true;
		}
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['click'])) $tmp['click'] = intval($data['click']);
		if(isset($data['md5_url'])) $tmp['md5_url'] = $data['md5_url'];
		if(isset($data['dateline'])) $tmp['dateline'] = $data['dateline'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Theme_Dao_Cr
	 */
	private static function _getCrDao() {
		return Common::getDao("Theme_Dao_Cr");
	}
}
