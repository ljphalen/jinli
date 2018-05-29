<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Common_Dao_Base {
    private $_cache = 0;
    /**
     * 默认db
     * @var string
     */
    protected $_adapter = 'default';

    /**
     * 构造函数
     */
    public function __construct() {
        $this->initAdapter();

        if (stristr(ENV, 'test') && DEFAULT_MODULE == 'Front') {
            $this->_cache = 1;
        }
    }

    /**
     *
     */
    public function initAdapter() {
        $adapter = $this->_adapter . 'Adapter';
        if ($adapter != Db_Adapter_Pdo::getAdaterName()) {
            Db_Adapter_Pdo::setAdapter($adapter);
        }
    }

    /**
     * 获取单条数据
     *
     * @param int $value
     */
    public function get($value) {
        $_sTime = microtime(true);
        if (empty($value)) return false;
        $sql = sprintf('SELECT * FROM %s WHERE %s = %s', $this->getTableName(), $this->_primary, $value);
        $ret = Db_Adapter_Pdo::fetch($sql);
        $this->_log('get', $sql, $_sTime);
        return $ret;
    }

    /**
     * 获取多条数据
     *
     * @param int $values
     */
    public function gets($field, $values) {
        $_sTime = microtime(true);
        $sql    = sprintf('SELECT * FROM %s WHERE %s IN %s', $this->getTableName(), $field, Db_Adapter_Pdo::quoteArray($values));
        $ret    = Db_Adapter_Pdo::fetchAll($sql);
        $this->_log('gets', $sql, $_sTime);
        return $ret;
    }

    /**
     * 根据sql查询
     *
     * @param string $sql
     */
    public function query($sql, $params = array()) {
        $_sTime = microtime(true);
        $ret    = Db_Adapter_Pdo::fetch($sql, $params);
        $this->_log('query', $sql, $_sTime);
        return $ret;
    }

    /**
     * 根据sql查询
     *
     * @param string $sql
     */
    public function fetcthAll($sql, $params = array()) {
        $_sTime = microtime(true);
        $ret    = Db_Adapter_Pdo::fetchAll($sql, $params);
        $this->_log('fetcthAll', $sql, $_sTime);
        return $ret;
    }

    /**
     * 查询所有数据
     */
    public function getAll($orderBy = array()) {
        $_sTime = microtime(true);
        $sort   = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql    = sprintf('SELECT * FROM %s %s', $this->getTableName(), $sort);
        $ret    = Db_Adapter_Pdo::fetchAll($sql);
        $this->_log('getAll', $sql, $_sTime);
        return $ret;
    }

    /**
     * 查询所有数据
     */
    public function max($field = "", $params, $params = array()) {
        $_sTime = microtime(true);
        if ($field == "") $field = $this->_primary;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf('SELECT max(%s) AS num FROM %s WHERE %s ', $field, $this->getTableName(), $where);
        $ret   = Db_Adapter_Pdo::fetchCloum($sql, 0);
        //$this->_log('max', $sql, $_sTime);
        return $ret;
    }

    /**
     * 查询所有数据
     */
    public function min($field = "", $params = array()) {
        $_sTime = microtime(true);
        if ($field == "") $field = $this->_primary;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf('SELECT min(%s) AS num FROM %s WHERE %s ', $field, $this->getTableName(), $where);
        $ret   = Db_Adapter_Pdo::fetchCloum($sql, 0);
        //$this->_log('min', $sql, $_sTime);
        return $ret;
    }

    /**
     * 获取分页列表数据
     *
     * @param array $params
     * @param int   $page
     * @param int   $limit
     */
    public function getList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
        $_sTime = microtime(true);
        $where  = Db_Adapter_Pdo::sqlWhere($params);
        $sort   = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql    = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
        $ret    = Db_Adapter_Pdo::fetchAll($sql);
        $this->_log('getList', $sql, $_sTime);
        return $ret;
    }

    /**
     * 根据条件查询
     *
     * @param array $were
     */
    public function getBy($params, array $orderBy = array()) {
        $_sTime = microtime(true);
        if (!is_array($params)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort  = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql   = sprintf('SELECT * FROM %s WHERE %s %s LIMIT 1', $this->getTableName(), $where, $sort);
        $ret   = Db_Adapter_Pdo::fetch($sql);
        $this->_log('getBy', $sql, $_sTime);
        return $ret;
    }

    /**
     * @param array $params
     * @param array $orderBy
     *
     * @return boolean mixed
     */
    public function getsBy($params = array(), $orderBy = array(), $groupBy = array()) {
        $_sTime = microtime(true);
        if (!is_array($params) || !is_array($orderBy)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort  = Db_Adapter_Pdo::sqlSort($orderBy);
        $group = Db_Adapter_Pdo::sqlGroup($groupBy);
        $sql   = sprintf('SELECT * FROM %s WHERE %s %s %s', $this->getTableName(), $where, $group, $sort);
        $ret   = Db_Adapter_Pdo::fetchAll($sql);
        $this->_log('getsBy', $sql, $_sTime);
        return $ret;
    }

    /**
     * 根据参数统计总数
     *
     * @param array $params
     * @param array $group
     */
    public function count($params = array(), $group = array()) {
        $_sTime  = microtime(true);
        $where   = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $sql     = sprintf('SELECT COUNT(*) FROM %s WHERE %s  %s', $this->getTableName(), $where, $groupBy);
        $ret     = Db_Adapter_Pdo::fetchCloum($sql, 0);
        //$this->_log('count', $sql, $_sTime);
        return $ret;
    }

    /**
     * 统计某个参数总及查询相关字段
     *
     * @param string $var 要求各和的字段
     * @param array  $w   where 条件
     */
    public function sum($var, $w = array()) {
        $_sTime = microtime(true);
        $where  = Db_Adapter_Pdo::sqlWhere($w);
        $sql    = sprintf('SELECT SUM(%s) FROM %s WHERE %s ', $var, $this->getTableName(), $where);
        $ret    = Db_Adapter_Pdo::fetchCloum($sql, 0);
        //$this->_log('sum', $sql, $_sTime);
        return $ret;
    }

    /**
     *
     * @param string $sqlWhere
     */
    public function searchBy($start, $limit, $sqlWhere = 1, array $orderBy = array()) {
        $_sTime = microtime(true);
        $sort   = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql    = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $sqlWhere, $sort, $start, $limit);
        $ret    = $this->fetcthAll($sql);
        //$this->_log('searchBy', $sql, $_sTime);
        return $ret;
    }

    /**
     *
     * @param string $sqlWhere
     *
     * @return string
     */
    public function searchCount($sqlWhere) {
        $_sTime = microtime(true);
        $sql    = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $sqlWhere);
        $ret    = Db_Adapter_Pdo::fetchCloum($sql, 0);
        //$this->_log('searchCount', $sql, $_sTime);
        return $ret;
    }

    /**
     *
     *
     * 插入数据
     *
     * @param array $data
     */
    public function insert($data) {
        $_sTime = microtime(true);
        if (!is_array($data)) return false;
        $sql = sprintf('INSERT INTO %s SET %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data));
        return Db_Adapter_Pdo::execute($sql);

    }

    /**
     * 插入数据
     *
     * @param array $data
     */
    public function mutiInsert($data) {
        if (!is_array($data)) return false;
        $sql = sprintf('INSERT INTO %s VALUES %s', $this->getTableName(), Db_Adapter_Pdo::quoteMultiArray($data));
        return   Db_Adapter_Pdo::execute($sql);
    }

    /**
     * 更新数据并返回影响行数
     *
     * @param array $data
     * @param int   $value
     */
    public function update($data, $value) {
        $_sTime = microtime(true);
        if (!is_array($data)) return false;
        $sql = sprintf('UPDATE %s SET %s WHERE %s = %d', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), $this->_primary, intval($value));
        $ret = Db_Adapter_Pdo::execute($sql, array(), false);
        $this->_log('update', $sql, $_sTime);
        return $ret;
    }

    /**
     * 批量更新并返回执行结果
     *
     * @param string $field
     * @param array  $values
     *
     * @return boolean
     */
    public function updates($field, $values, $data) {
        $_sTime = microtime(true);
        if (!$field || !is_array($values)) return false;
        $sql = sprintf('UPDATE %s SET %s WHERE %s IN %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), $field, Db_Adapter_Pdo::quoteArray($values));
        $ret = Db_Adapter_Pdo::execute($sql, array(), false);
        $this->_log('updates', $sql, $_sTime);
        return $ret;
    }

    /**
     * 指量更新
     *
     * @param array $data
     * @param array $params
     *
     * @return boolean
     */
    public function updateBy($data, $params) {
        $_sTime = microtime(true);
        if (!is_array($data) || !is_array($params)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf('UPDATE %s SET %s WHERE %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), $where);
        $ret   = Db_Adapter_Pdo::execute($sql, array(), false);
        $this->_log('updateBy', $sql, $_sTime);
        return $ret;
    }

    /**
     *
     * @param array $data
     *
     * @return boolean
     */
    public function replace($data) {
        $_sTime = microtime(true);
        if (!is_array($data)) return false;
        $sql = sprintf('REPLACE %s SET %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data));
        return Db_Adapter_Pdo::execute($sql, array(), false);

    }

    /**
     * increment an field by params
     *
     * @param string $field
     * @param array  $where
     */
    public function increment($field, $params, $step = 1) {
        $_sTime = microtime(true);
        if (!$field || !$params) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf('UPDATE %s SET %s=%s+%d WHERE %s ', $this->getTableName(), $field, $field, $step, $where);
        return Db_Adapter_Pdo::execute($sql, array(), false);
    }

    /**
     * 删除数据并返回影响行数
     *
     * @param int $value
     */
    public function delete($value) {
        $_sTime = microtime(true);
        if (!$value) return false;
        $sql = sprintf('DELETE FROM %s WHERE %s = %d', $this->getTableName(), $this->_primary, intval($value));
        return Db_Adapter_Pdo::execute($sql, array(), true);
    }

    /**
     * 删除多条数据并返回执行结果
     *
     * @param int $value
     */
    public function deletes($field, $values) {
        $_sTime = microtime(true);
        if (!$field || !is_array($values)) return false;
        $sql = sprintf('DELETE FROM %s WHERE %s IN %s', $this->getTableName(), $field, Db_Adapter_Pdo::quoteArray($values));
        $ret = Db_Adapter_Pdo::execute($sql, array(), false);
        $this->_log('deletes', $sql, $_sTime);
        return $ret;
    }

    /**
     *
     * @param array $params
     *
     * @return boolean
     */
    public function deleteBy($params) {
        $_sTime = microtime(true);
        if (!is_array($params)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf('DELETE FROM %s WHERE %s', $this->getTableName(), $where);
        $ret   = Db_Adapter_Pdo::execute($sql, array(), true);
        $this->_log('deleteBy', $sql, $_sTime);
        return $ret;
    }

    /**
     * 获取最后插入的ID
     */
    public function getLastInsertId() {
        return Db_Adapter_Pdo::getLastInsertId();
    }

    /**
     * 获取表名
     */
    public function getTableName() {
        return $this->_name;
    }

    private function _log($name, $sql, $sTime = 0) {
        if ($this->_cache && $this->_name != 'tj_log') {
            $t    = sprintf("%.2f", microtime(true) - $sTime);
            $path = '/data/3g_log/mysql/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $str = sprintf("%s (%s)%s:%s\n", date('H:i:s'), $t, $name, $sql);
            error_log($str, 3, $path . date('Ymd'));
        }
    }
}
