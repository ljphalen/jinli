<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Gionee_Dao_Crcategory extends Common_Dao_Base{
	protected $_name = 'tj_cr_category';
	protected $_primary = 'id';
		
	/**
	 *
	 * @param unknown_type $url
	 */
	public function getParentList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = 0 ', $this->_name);
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
		$sql = sprintf('SELECT * FROM %s WHERE parent_id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($pids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $url
	 * @return mixed
	 */
	public function getDataByUrl($url) {
		$sql = sprintf('SELECT * FROM %s WHERE md5_url = %s ', $this->_name, Db_Adapter_Pdo::quote($url));
		return Db_Adapter_Pdo::fetch($sql);
	}
}