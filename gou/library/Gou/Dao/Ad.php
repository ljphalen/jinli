<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Gou_Dao_Ad extends Common_Dao_Base{
	protected $_name = 'gou_ad';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getAllAdCount($params) {
		$time = Common::getTime();
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d',$this->getTableName(), $time, $time);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param int $guideTypes
	 * @return multitype:
	 */
	public function getByGuideTypes($guideTypes) {
		$time = Common::getTime();
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND ad_type IN %s ORDER BY sort DESC',$this->getTableName(), $time, $time,Db_Adapter_Pdo::quoteArray($guideTypes));
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseAds($start, $limit, $params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseAdCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
