<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Gionee_Dao_Pv extends Common_Dao_Base{
	protected $_name = 'tj_pv';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 */
	public function getListByTime($sDate, $eDate, $params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE %s AND dateline BETWEEN "%s" AND "%s"', $this->getTableName(), $where, $sDate, $eDate);
		return $this->fetcthAll($sql);
	}
	

	/**
	 *
	 * @param unknown_type $params
	 * @return multitype:
	 */
	public function searchPvList($params) {
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT sum(pv) as total FROM %s WHERE %s ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return Ambigous <number, string>
	 */
	private function _cookSearch($params) {
		$where = 1;
		if($params['sdate']) $where .= ' AND dateline >= ' . Db_Adapter_Pdo::quote($params['sdate']);
		if($params['edate']) $where .= ' AND dateline <= '. Db_Adapter_Pdo::quote($params['edate']);
		if(isset($params['tj_type'])) $where .= ' AND tj_type = '. Db_Adapter_Pdo::quote($params['tj_type']);
		return $where;
	}
}