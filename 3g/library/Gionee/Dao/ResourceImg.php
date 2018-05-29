<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_ResourceImg
 * @author tiansh
 *
 */
class Gionee_Dao_ResourceImg extends Common_Dao_Base {
	protected $_name = '3g_resource_img';
	protected $_primary = 'id';

	/**
	 * delete by resource_id
	 */
	public function deleteByResourceId($resource_id) {
		$sql = sprintf('DELETE FROM %s WHERE rid = %d', $this->getTableName(), intval($resource_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
}