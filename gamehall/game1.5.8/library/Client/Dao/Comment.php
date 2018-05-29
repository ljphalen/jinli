<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Comment
 * @author lichanghau
 *
 */
class Client_Dao_Comment extends Common_Dao_Base{
    protected $_name = 'game_client_comment';
    protected $_primary = 'id';    
    const SQL_OPERATE_KEY = -1;

    
    public function _cookParams($params) {
    	$sql = ' ';
    	if ($params['ids']) {
    		$sql .= ' OR id IN ('.implode(',',$params['ids']).')';
    	}
    	unset($params['ids']);
    	return Db_Adapter_Pdo::sqlWhere($params).$sql;
    }
    
    private function where(array $search = array()) {
        if (!is_array($search)) {
            return 0;
        }

        $where = null;
        $op = ' ' . $search[self::SQL_OPERATE_KEY] . ' ';

        foreach($search as $key => $value) {
            if ($key == self::SQL_OPERATE_KEY) {
                continue;
            }
            if (is_array($value) && array_key_exists(self::SQL_OPERATE_KEY, $value)) {
                if ($where) {
                    $where = $where . $op . '(' . $this->where($value) . ')';
                } else {
                    $where = '(' . $this->where($value) . ')';
                }
            } else {
                if (is_array($value)) {
                    list($subOp, $subVal) = $value;
                    $sqlElement = Db_Adapter_Pdo::_where($key, strtoupper($subOp), $subVal);
                } else {
                    $sqlElement = Db_Adapter_Pdo::_where($key, "=", $value);
                }

                if ($where) {
                    $where = $where . $op . $sqlElement;
                } else {
                    $where = $sqlElement;
                }
            }
        }

        return $where;
    }

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $search 二维数组，一级数组各元素之间使用OR关系, 二级数组各元素之间使用AND关系
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
    public function getListExt($start = 0, $limit = 20, array $search = array(), array $orderBy = array()) {
        $where = $this->where($search);

        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
        $list = Db_Adapter_Pdo::fetchAll($sql);

        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
        $total = Db_Adapter_Pdo::fetchCloum($sql, 0);

        return array($total, $list);
    }
}