<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Cod_Dao_Type extends Common_Dao_Base{
	protected $_name = 'cod_type';
	protected $_primary = 'id';
	
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseGuideTypes($start, $limit, $params) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status =1 AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseGuideTypesCount($params) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE status =1 AND %s',$this->getTableName(),  $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
