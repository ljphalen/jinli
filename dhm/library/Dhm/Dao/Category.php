<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author tiansh
 *
 */
class Dhm_Dao_Category extends Common_Dao_Base {
	protected $_name = 'dhm_category';
	protected $_primary = 'id';
	
	/**
	 * 取子分类
	 * @param array $params
	 * @param array $orderBy
	 * @return array|bool
	 */
	public function getSubCategory($parent_id) {
	    if (!$parent_id) return false;
	    $sql = sprintf('SELECT * FROM %s WHERE root_id = %d || parent_id = %d', $this->getTableName(), $parent_id, $parent_id);
	    return Db_Adapter_Pdo::fetchAll($sql);
	}
}