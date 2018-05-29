<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Dao_Spec extends Common_Dao_Base {
	protected $_name = 'widget_spec';
	protected $_primary = 'id';

	public function getTypes() {
		$sql = sprintf('SELECT `type` FROM %s GROUP BY `type`', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}



