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
     * 获取多条数据
     * @param int $values
     */
    public function gets($field, $values) {
        $sql = sprintf('SELECT * FROM %s WHERE %s IN %s', $this->getTableName(), $field, Db_Adapter_Pdo::quoteArray($values));
        return Db_Adapter_Pdo::fetchAll($sql);
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
    public function fetchAll($sql, $params = array()) {
        return Db_Adapter_Pdo::fetchAll($sql, $params);
    }

    /**
     *
     * 查询所有数据
     */
    public function getAll($orderBy = array()) {
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT * FROM %s %s', $this->getTableName(), $sort);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     *
     * 查询所有数据
     */
    public function max($field = "", $params, $params = array()) {
        if ($field == "") $field = $this->_primary;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf('SELECT max(%s) AS num FROM %s WHERE %s ', $field, $this->getTableName(), $where);
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }

    /**
     *
     * 查询所有数据
     */
    public function min($field = "", $params = array()) {
        if ($field == "") $field = $this->_primary;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf('SELECT min(%s) AS num FROM %s WHERE %s ', $field, $this->getTableName(), $where);
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
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
    public function getBy($params, array $orderBy = array()) {
        if (!is_array($params)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT * FROM %s WHERE %s %s', $this->getTableName(), $where, $sort);
        $ret = Db_Adapter_Pdo::fetch($sql);

        return $ret;
    }

    /**
     *
     * @param unknown_type $params
     * @return boolean|mixed
     */
    public function getsBy($params, $orderBy = array()) {

        if (!is_array($params) || !is_array($orderBy)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT * FROM %s WHERE %s %s', $this->getTableName(), $where, $sort);

        $ret = Db_Adapter_Pdo::fetchAll($sql);
        return $ret;
    }

    /**
      获取所有手机rid号,id
     * @param unknown_type $params
     * @return boolean|mixed
     */
    public function getsRids($params, $orderBy = array(), $start = 0, $limt = 20) {

        // if (!is_array($params) || !is_array($orderBy)) return false;
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT id,rid FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, $start, $limt);

        $ret = Db_Adapter_Pdo::fetchAll($sql);
        return $ret;
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
     * @param unknown_type $sqlWhere
     */
    public function searchBy($start, $limit, $sqlWhere = 1, array $orderBy = array()) {
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d, %d', $this->getTableName(), $sqlWhere, $sort, $start, $limit);
        return $this->fetchAll($sql);
    }

    /**
     *
     * @param string $sqlWhere
     * @return string
     */
    public function searchCount($sqlWhere) {
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $sqlWhere);
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }

    /**
     *
     * 插入数据
     * @param array $data
     */
    public function insert($data) {
        if (!is_array($data)) return false;
        $sql = sprintf('INSERT INTO %s SET %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data));


        return Db_Adapter_Pdo::execute($sql);
    }

    /**
     *
     * 插入数据
     * @param array $data
     */
    public function mutiInsert($data) {

        if (!is_array($data)) return false;
        $sql = sprintf('INSERT INTO %s VALUES %s', $this->getTableName(), Db_Adapter_Pdo::quoteMultiArray($data));


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
     * 批量更新并返回执行结果
     * @param unknown_type $field
     * @param unknown_type $values
     * @return boolean|Ambigous <boolean, number>
     */
    public function updates($field, $values, $data) {
        if (!$field || !is_array($values)) return false;
        $sql = sprintf('UPDATE %s SET %s WHERE %s IN %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), $field, Db_Adapter_Pdo::quoteArray($values));
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
        $sql = sprintf('UPDATE %s SET %s = %s+%d WHERE %s ', $this->getTableName(), $field, $field, $step, $where);
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
     * 删除多条数据并返回执行结果
     * @param int $value
     */
    public function deletes($field, $values) {
        if (!$field || !is_array($values)) return false;
        $sql = sprintf('DELETE FROM %s WHERE %s IN %s', $this->getTableName(), $field, Db_Adapter_Pdo::quoteArray($values));
        return Db_Adapter_Pdo::execute($sql, array(), false);
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

    public function setDataDao($filedname, $values, $sqlstr = false) {
        $sql = sprintf("insert into %s (%s)values(%s)", $this->_name, $filedname, $values);
        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::execute($sql);
    }

    public function getDataDao($where, $fileds, $sqlstr = false) {
        $sql = sprintf("select %s from %s where %s ", $fileds, $this->_name, $where);

        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function selectedDataDao($where, $fileds, $sqlstr = false) {
        $sql = sprintf("select %s from %s  ", $fileds, $where);
        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function updataDao($data, $where, $sqlstr = FALSE) {
        $sql = sprintf("update %s set %s  where %s", $this->_name, $data, $where);
        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::execute($sql);
    }

    public function delDao($id, $sqlstr = false) {
        $sql = sprintf("delete from %s where %s = %d", $this->_name, $this->_primary, $id);
        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::execute($sql);
    }

    public function mk_sqls($str) {

        $str = str_replace("and", "", $str);
        $str = str_replace("execute", "", $str);
        $str = str_replace("update", "", $str);
        $str = str_replace("chr", "", $str);
        $str = str_replace("mid", "", $str);
        $str = str_replace("master", "", $str);
        $str = str_replace("truncate", "", $str);
        $str = str_replace("char", "", $str);
        $str = str_replace("declare", "", $str);
        $str = str_replace("select", "", $str);
        $str = str_replace("create", "", $str);
        $str = str_replace("delete", "", $str);
        $str = str_replace("insert", "", $str);
        $str = str_replace("'", "", $str);
        // $str = str_replace("\"", "", $str);
        // $str = str_replace(" ", "", $str);
        $str = str_replace("or", "", $str);
        //$str = str_replace(" = ", "", $str);
        $str = str_replace("%20", "", $str);
        //echo $str;
        return $str;
    }

}
