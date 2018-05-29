<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Gc_Dao_Channel extends Common_Dao_Base{
	protected $_name = 'gc_channel';
	protected $_primary = 'id';
		
	/**
	 *
	 * 取顶级分类
	 */
	public function getRootList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = 0 AND root_id = 0 ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * 取二级
	 */
	public function getParentList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = root_id AND root_id != 0 AND parent_id != 0 ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getListBy($params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY sort DESC, id DESC',$this->getTableName(), $where);
		return $this->fetcthAll($sql);
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
}