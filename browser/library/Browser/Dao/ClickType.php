<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Browser_Dao_ClickType extends Common_Dao_Base{
	protected $_name = 'tj_click_type';
	protected $_primary = 'id';
		
	/**
	 *
	 * @param unknown_type $url
	 */
	public function getParentList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = 0 ORDER BY order_id DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public function getListByParentID($parent_id) {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = %s ', $this->_name, $parent_id);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public function getListByParentIds($pids) {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id IN %s ORDER BY order_id DESC, id DESC', $this->_name, Db_Adapter_Pdo::quoteArray($pids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}	
}