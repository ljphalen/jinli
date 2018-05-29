<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Keywords
 * @author tiansh
 *
 */
class Client_Dao_Keywords extends Common_Dao_Base{
	protected $_name = 'client_keywords';
	protected $_primary = 'id';
	
	/**
	 * 获取分页列表数据rand
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 * @param array $orderBy
	 * @return array
	 */
	public function getRandList($start = 0, $limit = 20, array $params = array()) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY RAND() LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
	    return Db_Adapter_Pdo::fetchAll($sql);
	}
}
