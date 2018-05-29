<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */
class Gou_Dao_ResourceImg extends Common_Dao_Base {
	protected $_name = 'gou_resource_img';
	protected $_primary = 'id';
	
	/**
	 * delete by $resource_id
	 */
	public function deleteByResourceId ($resource_id) {
		$sql = sprintf('DELETE FROM %s WHERE resource_id = %d', $this->getTableName(), intval($resource_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 * delete by $resource_id
	 */
	public function getImagesByResourceId ($resource_id) {
		$sql = sprintf('SELECT * FROM %s WHERE resource_id = %d ORDER BY id ASC', $this->getTableName(), intval($resource_id));
		return $this->fetcthAll($sql);
	}
}