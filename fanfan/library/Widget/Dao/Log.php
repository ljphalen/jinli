<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Widget_Dao_Log extends Common_Dao_Base {
	protected $_name = 'tj_log';
	protected $_primary = 'id';


	public function getLastIdByType($type, $limit) {
		$sql = sprintf("SELECT DISTINCT `key` FROM %s WHERE `type` = '%s' and `date` = '%s' GROUP BY `key` ORDER BY `val` LIMIT %d", $this->getTableName(), $type, date('Ymd'), $limit);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function getNumByTypeOfKey($type, $key) {
		$sql = sprintf("SELECT SUM(val) AS num FROM %s WHERE `type`='%s' AND `key`='%s'", $this->getTableName(), $type, $key);
		$ret = Db_Adapter_Pdo::fetch($sql);
		return !empty($ret['num'])?intval($ret['num']):0;
	}

	public function getTotalByKey($params) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT *, SUM(`val`) as num FROM %s WHERE %s GROUP BY `date`,`key` ORDER BY `val`', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);


	}
}