<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Stat_Dao_Log extends Common_Dao_Base{
	protected $_name = 'gou_click_log';
	protected $_primary = 'id';
    public $adapter = 'BI';

    /**
     * 根据版本查询
     * @param $start
     * @param $end
     */
    public function getsByParams($params) {
        $column = array('version_id', 'module_id', 'channel_id', 'item_id');
        $fields = array_merge(array('dateline', 'name', 'count(*) as pv', 'count(DISTINCT uid) as uv', 'count(DISTINCT imei) as imei'), array_slice($column, 0, count($params)));
        $field_str = (implode(',', $fields));

        $table = $this->_name . date('_Y_m_d', strtotime($params['dateline']));
        if (!$this->checkTableExist($table)) return array();

        $where = Db_Adapter_Pdo::sqlWhere($params);
        $group_by = $column[count($params)-1] ? ('GROUP BY ' . $column[count($params)-1]) : '';
        $sql = sprintf("SELECT %s from %s WHERE %s %s", $field_str, $table, $where, $group_by);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * @param $params
     * @return array
     */
    public function getsChannelCodeByParams($params) {
        $table = $this->_name . date('_Y_m_d', strtotime($params['dateline']));
        if (!$this->checkTableExist($table)) return array();
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf("SELECT DISTINCT channel_code FROM %s WHERE %s", $table, $where);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * @param $params
     * @return array
     */
    public function getByHost($params) {
        $table = $this->_name . date('_Y_m_d', strtotime($params['dateline']));
        if (!$this->checkTableExist($table)) return array();
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf("SELECT COUNT(id) AS total, host, host_id FROM %s WHERE %s GROUP BY host_id", $table, $where);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * @param $params
     * @return array
     */
    public function getsGroupByChannelCode($params) {
        $table = $this->_name . date('_Y_m_d', strtotime($params['dateline']));
        if (!$this->checkTableExist($table)) return array();
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf("SELECT dateline, name, version_id, module_id, channel_id, channel_code , name, count(*) as pv,count(DISTINCT uid) as uv, count(DISTINCT imei) as imei from %s WHERE  %s GROUP BY channel_code", $table, $where);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * @param $params
     * @return array
     */
    public function getByProvince($params) {
        $table = $this->_name . date('_Y_m_d', strtotime($params['dateline']));
        if (!$this->checkTableExist($table)) return array();
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf("SELECT COUNT(id) AS total, province, province_id FROM %s WHERE %s GROUP BY province_id", $table, $where);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * check table exists
     * @param $tableName
     * @return bool|int
     */
    public static function checkTableExist($tableName) {
        $sql = sprintf("SHOW TABLES LIKE '%s'", $tableName);
        return Db_Adapter_Pdo::execute($sql, array(), true);
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTableName() {
        $tableName = $this->_name.date('_Y_m_d');
        if(!self::checkTableExist($tableName)) {
            self::createTable($tableName);
        }
        return $tableName;
    }

    /**
     * 创建统计日志表
     * @param $tableName
     * @return bool|int
     */
    public static function createTable($tableName) {
        $sql =
<<<EOD
                CREATE TABLE `%s` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `hash` bigint(20) NOT NULL DEFAULT 0,
                `version_id` int(10) NOT NULL DEFAULT 0,
                `channel_id` int(10) NOT NULL DEFAULT 0,
                `module_id` int(10) NOT NULL DEFAULT 0,
                `item_id` int(10) NOT NULL DEFAULT 0,
                `url` varchar(255) NOT NULL DEFAULT '',
                `host` varchar(255) NOT NULL DEFAULT '',
                `host_id` bigint(20) NOT NULL DEFAULT 0,
                `province` varchar(255) NOT NULL DEFAULT '',
                `province_id` bigint(20) NOT NULL DEFAULT 0,
                `city` varchar(255) NOT NULL DEFAULT '',
                `city_id` bigint(20) NOT NULL DEFAULT 0,
                `uid` bigint(20) NOT NULL DEFAULT 0,
                `imei` varchar(64) NOT NULL DEFAULT '',
				`name` varchar(100) NOT NULL DEFAULT '',
				`ip` varchar(64) NOT NULL DEFAULT '',
                `channel_code` varchar(100) NOT NULL DEFAULT '',
                `create_time` int(10) NOT NULL DEFAULT 0,
                `dateline` date NOT NULL,
                key `idx_version_id` (`version_id`),
                key `idx_channel_id` (`channel_id`),
                key `idx_module_id` (`module_id`),
                key `idx_hash` (`hash`),
                PRIMARY KEY (`id`)
            ) ENGINE=INNODB DEFAULT CHARSET=utf8;
EOD;
        return Db_Adapter_Pdo::execute(sprintf($sql, $tableName));
    }
}
