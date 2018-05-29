<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据) 游戏大厅首页分类点击统计数
 * Client_Dao_CategoryClickStatistics
 * @author liyf
 *
 */
class Client_Dao_CategoryClickStatistics extends Client_Dao_SplitBase {
    
    public $adapter = 'STATISTICS';
    
    /**
     * 根据imei获取统计数据
     * @param string $imei
     * @return array(tableField => value)
     */
    public function getByImei($imei, $orderBy = array()) {
        $tableName = $this->getTableName($imei);
        if ($tableName == null) {
            return false;
        }
        $params = array('imei' => $imei);
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT * FROM %s WHERE %s %s LIMIT 1', $tableName, $where, $sort);
        return Db_Adapter_Pdo::fetch($sql);
    }
    
    /**
     * 添加数据
     * @param array(tableField => value) $data
     * @return boolean|id
     */
    public function insert($data) {
        if (!is_array($data)) return false;
        $imei = $data['imei'];
        $tableName = $this->getTableName($imei);
        if ($tableName == null) {
            return false;
        }
        $sql = sprintf('INSERT INTO %s SET %s', $tableName, Db_Adapter_Pdo::sqlSingle($data));
        //var_dump($sql);
        
        $res = Db_Adapter_Pdo::execute($sql);
        //var_dump($res);
        if (!$res) {
            return false;
        }
        return Db_Adapter_Pdo::getLastInsertId();
    }
    
    public function update($data, $imei, $value) {
        if (!is_array($data)) return false;
        $tableName = $this->getTableName($imei);
        if ($tableName == null) {
            return false;
        }
        $sql = sprintf('UPDATE %s SET %s WHERE %s = %d', $tableName, Db_Adapter_Pdo::sqlSingle($data), 'id', intval($value));
        return Db_Adapter_Pdo::execute($sql, array(), false);
    }
	
	/**
	 * 获取分表名称
	 * @param string $imei
	 * @return string
	 */
	public function getTableName($imei) {
	    if (strlen(trim($imei)) < 2) {
	        return null;
	    }
	    if (!preg_match('/^[0-9A-F]+$/i', trim($imei))) {
	    	return null;
	    }
	    $tableCode = substr(trim($imei), -3, 2);
	    //var_dump($tableCode);
	    return 'category_click_statistics_' . strtolower($tableCode);
	}
	
	/**
	 * 创建一个表
	 * @param string $tableName 表名
	 */
	public function createTable($tableName) {
	    $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `imei` char(32) NOT NULL,
              `category_json` text NULL,
              PRIMARY KEY (`id`),
              KEY `imei` (`imei`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8";
	    
	    return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 按规则创建所有分表
	 */
	public function createAllTables() {
	    $nums = array(0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f);
	    
	    foreach ($nums as $num) {
	        foreach ($nums as $num2) {
	            $tableName = 'category_click_statistics_' . $num . $num2;
	            $this->createTable($tableName);
	        }
	    }
	}
}
