<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desClickiption here ...
 * @author tiansh
 *
 */
class Browser_Service_Click{
	

	/**
	 *
	 * Enter desClickiption here ...
	 */
	public static function getAllClick() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter desClickiption here ...
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
		$ret = self::_getClickDao()->getClickList($start, $limit, $data);
		$total = self::_getClickDao()->getCount($data);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public static function searchClickList($params) {
		return self::_getClickDao()->searchClickList($params);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public static function getTotalByTime($sDate, $eDate) {
		return self::_getClickDao()->getTotalByTime($sDate, $eDate);
	}
	
	/**
	 *
	 * @param unknown_type $Date
	 * @return multitype:
	 */
	public static function getClickCount($sDate, $eDate) {
		return self::_getClickDao()->getCountByTime($sDate, $eDate);
	}
		
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function incrementClick($Click) {
		if(is_array($Click)){
			foreach ( $Click as $key => $val ) {
				if ($val['type_id']) {
					$ret = self::_getClickDao()->where(array('dateline'=>date('Y-m-d'), 'type_id'=>$key));
					if ($ret) {
						self::_getClickDao()->updateByTypeAndDate(array('click'=>$ret['click'] + $val['click'], 'type_id'=>$val['type_id']), $val['type_id'], $ret['dateline']);
					} else {
						self::_getClickDao()->insert(array('click'=>$val['click'], 'type_id'=>$val['type_id'],'dateline'=>date('Y-m-d')));
					}
				}
			}
			return true;
		}
	}


	/**
	 *
	 * Enter desClickiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['click'])) $tmp['click'] = intval($data['click']);
		if(isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		if(isset($data['dateline'])) $tmp['dateline'] = $data['dateline'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Browser_Dao_Click
	 */
	private static function _getClickDao() {
		return Common::getDao("Browser_Dao_Click");
	}
}
