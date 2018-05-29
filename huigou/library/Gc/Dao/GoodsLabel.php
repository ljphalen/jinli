<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Gc_Dao_GoodsLabel extends Common_Dao_Base{
	protected $_name = 'gc_goods_label';
	protected $_primary = 'id';
		
	/**
	 *
	 * 取父标签
	 */
	public function getParentList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = 0 ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * 取子标签
	 */
	public function getChildList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id != 0 ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
	
	/**
	 *
	 * 取父标签下所有的子标签
	 */
	public function getListByParentId($parent_id) {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = %s ORDER BY sort DESC, id DESC',$this->getTableName(), Db_Adapter_Pdo::quote($parent_id));
		return $this->fetcthAll($sql);
	}
	
	/**
	 * get count by parent_id
	 * @param int $parent_id
	 */
	public function getCountByParentId($parent_id) {
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE parent_id = %s',$this->getTableName(), Db_Adapter_Pdo::quote($parent_id));
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public function getListByParentIds($pids) {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id IN %s ORDER BY sort DESC, id DESC', $this->_name, Db_Adapter_Pdo::quoteArray($pids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseLabels($start, $limit, $params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY sort DESC, id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseLabelsCount($params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}