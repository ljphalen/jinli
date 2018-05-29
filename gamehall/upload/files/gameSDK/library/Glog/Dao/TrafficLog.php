<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Glog_Dao_TrafficLog
 * @author fanch
 *
 */
class Glog_Dao_TrafficLog extends Common_Dao_Base{
	protected $_name = 'freedl_log_';
	protected $_primary = 'id';
	public $adapter = 'GLOG'; //$adapter
	
	/**
	 * 表是否存在检查
	 * @param unknown $tableName
	 * @return Ambigous <boolean, number>
	 */
	public static function checkTableExist($tableName) {
		$sql = sprintf("SHOW TABLES LIKE '%s'", $tableName);
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 * 获取表名,表不存在创建表
	 * @return string
	 */
	public function getTableName() {
		$tableName = $this->_name.date('Ymd');
		if(!self::checkTableExist($tableName)) {
			self::createTable($tableName);
		}
		return $tableName;
	}
	
	/**
	 * 查询特定表的前limit条数据方法
	 * @param string $tableName
	 * @param array $params
	 * @param int $limit
	 * @return array:
	 */
	public function getLimit($tableName, $params, $limit = 10){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s LIMIT %d', $tableName, $where, intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 创建免流量上报日志表
	 * @param $tableName
	 * @return bool|int
	 */
	public static function createTable($tableName) {
		$sql =
		<<<EOD
			CREATE TABLE `%s` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
				`imsi` varchar(255) NOT NULL DEFAULT '',
				`uuid` varchar(255) NOT NULL DEFAULT '',
				`uname` varchar(255) NOT NULL DEFAULT '',
				`nickname` varchar(255) NOT NULL DEFAULT '',
				`imei` varchar(255) NOT NULL DEFAULT '',
				`model`  varchar(255) NOT NULL DEFAULT '',
				`version` varchar(255) NOT NULL DEFAULT '',
				`sys_version` varchar(255) NOT NULL DEFAULT '',
				`client_pkg` varchar(255) NOT NULL DEFAULT '',
				`operator` varchar(255) NOT NULL DEFAULT '',
				`ntype` tinyint(3) NOT NULL DEFAULT 0,
				`game_id` int(10) unsigned NOT NULL DEFAULT '0',
				`game_name` varchar(255) NOT NULL DEFAULT '',
				`game_size` varchar(255) NOT NULL DEFAULT '',
				`task_flag`  varchar(255) NOT NULL DEFAULT '',
				`upload_size` decimal(10,2) NOT NULL DEFAULT 0.00,
				`task_status` tinyint(3) NOT NULL DEFAULT 0,
				`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
				PRIMARY KEY (`id`),
				KEY `idx_activity_id` (`activity_id`),
				KEY `idx_imsi` (`imsi`),
				KEY `idx_task_flag` (`task_flag`)
			) ENGINE=INNODB DEFAULT CHARSET=utf8;
EOD;
		return Db_Adapter_Pdo::execute(sprintf($sql, $tableName));
	}
}
