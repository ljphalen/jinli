<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author lichanghau
 *
 */
class Resource_Dao_Label extends Common_Dao_Base{
	protected $_name = 'game_resource_label';
	protected $_primary = 'id';
	
	public function getSortAll() {
		$sql = sprintf('SELECT *  FROM  %s WHERE status = 1 ORDER BY id DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return multitype:
	 */
	public function getUseLabel($start, $limit, $params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function getUseLabelcount($params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE  %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['title']) {
			$sql .= " AND title like '%" . Db_Adapter_Pdo::filterLike($params['title']) . "%'";
		}
		unset($params['title']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}