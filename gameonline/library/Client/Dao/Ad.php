<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Ad
 * @author lichanghua
 *
 */
class Client_Dao_Ad extends Common_Dao_Base{
	protected $_name = 'game_client_ad';
	protected $_primary = 'id';

	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseAds($start, $limit, $params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getCanUseAdCount($params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT count(*) FROM %s WHERE %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	public static function _cookSql($params) {
		$where = '';
		$tmp = $params;
		unset($tmp['start_time']);
		unset($tmp['end_time']);
		
		$where .= Db_Adapter_Pdo::sqlWhere($tmp);
		
		if ($params['start_time'] && !$params['end_time']) {
			$where .=' AND start_time >= '.intval(strtotime($params['start_time']));
		} else if($params['end_time'] && !$params['start_time']){
			$where .=' AND end_time <= '.intval(strtotime($params['end_time']));
		} else if($params['start_time'] && $params['end_time'] && $params['start_time'] <= $params['end_time']){
			$where .=' AND (start_time <= '.intval(strtotime($params['start_time'])).' AND end_time >= '.intval(strtotime($params['end_time'])).')';
		}
		return $where;
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseNormalAds($start, $limit, $params) {
		$time = strtotime(date('Y-m-d H:i'));
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseNormalAdsCount($params) {
		$time = strtotime(date('Y-m-d H:i'));
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseApiAds($start, $limit, $params) {
		$time = strtotime(date('Y-m-d H:i'));
		$sql = ' ';
		if ($params['not_ids']) {
			$sql .= sprintf(' AND link NOT IN %s ' , Db_Adapter_Pdo::quoteArray($params['not_ids']));
		}
		unset($params['not_ids']);
		$where = Db_Adapter_Pdo::sqlWhere($params).$sql;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseApiAdsCount($params) {
		$time = strtotime(date('Y-m-d H:i'));
		$sql = ' ';
		if ($params['not_ids']) {
			$sql .= sprintf(' AND link NOT IN %s ' , Db_Adapter_Pdo::quoteArray($params['not_ids']));
		}
		unset($params['not_ids']);
		$where = Db_Adapter_Pdo::sqlWhere($params).$sql;
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}