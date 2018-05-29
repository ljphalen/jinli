<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Category
 * @author lichanghua
 *
 */
class Client_Dao_Category extends Common_Dao_Base{
	protected $_name = 'game_client_category';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseCategorysByIds($start, $limit, $params) {
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND id IN %s ORDER BY FIELD %s LIMIT %d,%d',$this->getTableName(), Db_Adapter_Pdo::quoteArray($params['id']), $this->quoteInArray($params['id']), $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseCategoryByIdsCount($params) {
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND id IN %s ORDER BY FIELD %s',$this->getTableName(), Db_Adapter_Pdo::quoteArray($params['id']), $this->quoteInArray($params['id']));
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'id'.','. implode(', ', $_returns) . ')';
	}
}
