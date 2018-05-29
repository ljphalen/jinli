<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Ng
 *
 */
class Gionee_Dao_Ng extends Common_Dao_Base {
	protected $_name = '3g_ng';
	protected $_primary = 'id';

	/**
	 * getsByStyle
	 * @param array $params
	 * @param array $orderBy
	 * @param int $limit
	 */
	public function getsByStyle($params, $orderBy, $limit) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort  = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql   = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d', $this->getTableName(), $where, $sort, $limit);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 获得单个元素的字
	 * @param unknown $var
	 * @param unknown $where
	 * @param unknown $orderBy
	 * @param unknown $groupBy
	 * @return multitype:
	 */
	public function getElements($elements,$where,$orderBy,$groupBy){
		if(!is_array($elements)||!is_array($where) || !is_array($groupBy)) return false;
		$str = ' ';
		$str .=implode(' , ', $elements);
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$sort  = Db_Adapter_Pdo::sqlSort($orderBy);
		$group = Db_Adapter_Pdo::sqlGroup($groupBy);
		$sql   = sprintf("SELECT %s FROM %s WHERE %s %s %s",$str,$this->getTableName(),$where,$group,$sort);
		return  $this->fetcthAll($sql);
	}
}