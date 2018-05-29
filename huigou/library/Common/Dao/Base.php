<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Common_Dao_Base {
	
	/**
	 * 
	 * 默认db
	 * @var string
	 */
	public $adapter = 'default';
	
	/**
	 * 
	 * 构造函数
	 */
	public function __construct() {
		$adapter = $this->adapter . 'Adapter';
		if ($adapter != Db_Adapter_Pdo::getAdaterName()) {
			Db_Adapter_Pdo::setAdapter($adapter);
		}
	}
	
	/**
	 * 
	 * 获取单条数据
	 * @param int $value
	 */
	public function get($value) {
		$sql = sprintf('SELECT * FROM %s WHERE %s = %s', $this->getTableName(), $this->_primary, $value);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 *
	 * 根据sql查询
	 * @param string $sql
	 */
	public function query($sql, $params = array()) {
		return Db_Adapter_Pdo::fetch($sql, $params);
	}
	
	/**
	 *
	 * 根据sql查询
	 * @param string $sql
	 */
	public function fetcthAll($sql, $params = array()) {
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}
	
	/**
	 * 
	 * 查询所有数据
	 */
	public function getAll() {
		$sql = sprintf('SELECT * FROM %s ', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * 根据条件查询
	 * @param array $were
	 */ 
	public function getBy($params){
		if (!is_array($params)) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return boolean|mixed
	 */
	public function getsBy($params) {
		if (!is_array($params)) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * 根据参数统计总数
	 * @param array $params
	 */
	public function count($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * 插入数据 
	 * @param array $data
	 */
	public function insert($data) {
		if (!is_array($data)) return false;
		$sql = sprintf('INSERT INTO %s SET %s',$this->getTableName(), Db_Adapter_Pdo::sqlSingle($data));
		return Db_Adapter_Pdo::execute($sql);
	}
	
	/**
	 *
	 * 插入数据
	 * @param array $data
	 */
	public function mutiInsert($data) {
		if (!is_array($data)) return false;
		$sql = sprintf('INSERT INTO %s VALUES %s',$this->getTableName(), Db_Adapter_Pdo::quoteMultiArray($data));
		return Db_Adapter_Pdo::execute($sql);
	}
	
	/**
	 * 
	 * 更新数据并返回影响行数
	 * @param array $data
	 * @param int $value
	 */
	public function update($data, $value) {
		if (!is_array($data)) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE %s = %d', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), $this->_primary, intval($value));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 指量更新
	 * @param array $data
	 * @param array $params
	 * @return boolean
	 */
	public function updateBy($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('UPDATE %s SET %s WHERE %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public function replace($data) {
		if (!is_array($data)) return false;
		$sql = sprintf('REPLACE %s SET %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * increment an field by params
	 * @param string $field
	 * @param array $where
	 */
	public function increment($field, $params, $step = 1) {
		if (!$field || !$params) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('UPDATE %s SET %s=%s+%d WHERE %s ', $this->getTableName(), $field, $field, $step, $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 
	 * 删除数据并返回影响行数
	 * @param int $value
	 */
	public function delete($value) {
		$sql = sprintf('DELETE FROM %s WHERE %s = %d', $this->getTableName(), $this->_primary, intval($value));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 * 
	 * @param array $params
	 * @return boolean
	 */
	public function deleteBy($params) {
		if (!is_array($params)) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('DELETE FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 * 获取最后插入的ID
	 */
	public function getLastInsertId() {
		return Db_Adapter_Pdo::getLastInsertId();
	}
	
	/**
	 * 
	 * 获取表名
	 */
	public function getTableName() {
		return $this->_name;
	}
}
