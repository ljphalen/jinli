<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_Area extends Common_Dao_Base {
	protected $_name = '3g_area';
	protected $_primary = 'id';

	/**
	 *
	 * 省列表
	 */
	public function getProvinceList() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = 0 ORDER BY sort DESC, id ASC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype: 
	 */
	public function getListByParentID($parent_id) {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id = %s  ORDER BY sort DESC, id ASC', $this->_name, $parent_id);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public function getAllCity() {
		$sql = sprintf('SELECT * FROM %s WHERE parent_id != 0  ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * 获取单条数据
	 * @param int $value
	 */
	public function getByName($name) {
		$sql = sprintf('SELECT * FROM %s WHERE name = %s', $this->getTableName(), Db_Adapter_Pdo::quote($name));
		return Db_Adapter_Pdo::fetch($sql);
	}
}