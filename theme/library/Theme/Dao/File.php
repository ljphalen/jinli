<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_File
 * @author tiansh
 *
 */
class Theme_Dao_File extends Common_Dao_Base {

    protected $_name = 'theme_file';
    protected $_primary = 'id';

    /**
     * @return multitype:
     */
    public function getAllFile($ids) {
        $where = '1 AND status = 4 ';
        if ($ids && is_array($ids)) $where .= ' AND id not in ' . Db_Adapter_Pdo::quoteArray($ids);
        $sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY id DESC', $this->getTableName(), $where);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * 主题的id号;
     * @param type $num
     * @return type
     */
    public function getThemeIdsDao($num, $sort = "sort", $type = 2) {
        if ($type == 3) {
            $sql = sprintf("select id,sort,since,down from %s where status=4 and package_type >=2 "
                    . " order by $sort DESC limit 0,%s", $this->_name, $num
            );
        } else {
            $sql = sprintf("select id,sort,since,down from %s where status=4 and package_type=2 "
                    . " order by $sort DESC limit 0,%s", $this->_name, $num
            );
        }
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getThemeIdsDao_order($num, $sort = "sort DESC", $type = 2) {
        if ($type == 3) {
            $sql = sprintf("select id,sort,since,down,price from %s where status=4 and package_type >=2 "
                    . " order by $sort  limit 0,%s", $this->_name, $num
            );
        } else {
            $sql = sprintf("select id,sort,since,down,price from %s where status=4 and package_type=2 "
                    . " order by $sort  limit 0,%s", $this->_name, $num
            );
        }


        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     *
     * @return multitype:
     */
    public function getIndexFile($ids, $order = '') {
        $where = 1;
        if ($ids && is_array($ids)) $where .= ' AND id in ' . Db_Adapter_Pdo::quoteArray($ids);
        $sql = sprintf('SELECT * FROM %s WHERE %s %s', $this->getTableName(), $where, $order);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     *
     * 获取分页列表数据
     * @param array $params
     * @param int $page
     * @param int $limit
     */
    public function getList($start = 0, $limit = 20, $params = array(), $order_by = '', $sort = 'DESC') {
        $where = 1;
        if (!$order_by) $order_by = $this->_primary;
        if (!$sort) $sort = 'DESC';

        if ($params['package_type'] == 3) {
            $where .= " and (package_type=2 or package_type=3) ";
            unset($params['package_type']);
        }
        $where .= $this->_cookSearch($params);

        $sql = sprintf('SELECT * FROM %s WHERE %s order by %s %s  LIMIT %d, %d', $this->getTableName(), $where, $order_by, $sort, intval($start), intval($limit));


        return Db_Adapter_Pdo::fetchAll($sql, $params);
    }

    public function get_list_filesid($params, $sort = 'DESC', $order_by = '') {
        $where = 1;
        $where .= $this->_cookSearch($params);
        if (!$order_by) $order_by = $this->_primary;

        $sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY %s %s ', $this->getTableName(), $where, $order_by, $sort, intval($start), intval($limit));

        return Db_Adapter_Pdo::fetchAll($sql, $params);
    }

    /**
     *
     * @param array $params
     * @return string
     */
    public function getCount($params) {
        $where = 1;
        if ($params['package_type'] == 3) {
            $where .= " and (package_type=2 or package_type=3) ";
            unset($params['package_type']);
        }
        $where .= $this->_cookSearch($params);
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->_name, $where);

        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }

    /**
     *
     * @param array $ids
     * @return multitype:
     */
    public function getByFileIds($ids) {

        $sql = sprintf('SELECT id, title FROM %s WHERE id in %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getByFileIds_orderyDao($ids) {
        $sql = sprintf('SELECT id, title FROM %s WHERE id in (%s) order by field(id,%s)', $this->_name, $ids, $ids);


        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getByFilsids_type($ids, $order = '') {
        $sql = sprintf('SELECT * FROM %s WHERE status = 4 and package_type=2 and id in %s %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids), $order);

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getByFilsids_type_v3($ids, $order = '') {
        $sql = sprintf('SELECT * FROM %s WHERE status = 4 and package_type >1 and id in %s %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids), $order);

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function updateWhereDao($data, $where) {
        $sql = sprintf("UPDATE %s SET %s where %s", $this->_name, $data, $where);

        return Db_Adapter_Pdo::execute($sql);
    }

    /**
     * getPre
     */
    public function getPre($id) {
        $sql = sprintf('SELECT id FROM %s WHERE id < %s AND status = 4 ORDER BY id DESC LIMIT 0, 1', $this->getTableName(), Db_Adapter_Pdo::quote($id));
        return Db_Adapter_Pdo::fetch($sql);
    }

    public function addLikes($fileds, $id) {
        $sql = sprintf('UPDATE %s SET %s=%s+1 WHERE %s = %s ', $this->getTableName(), $fileds, $fileds, $this->_primary, $id);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function getByWheres($fileds = "*", $where = 1) {
        $sql = sprintf('SELECT %s FROM %s WHERE %s ', $fileds, $this->_name, $where);

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     * getPre
     */
    public function getNext($id) {
        $sql = sprintf('SELECT id FROM %s WHERE id > %s AND status = 4 ORDER BY id ASC LIMIT 0, 1', $this->getTableName(), Db_Adapter_Pdo::quote($id));
        return Db_Adapter_Pdo::fetch($sql);
    }

    /**
     *
     * 获取分页列表数据
     * @param array $params
     * @param int $page
     * @param int $limit
     */
    public function getCanuseFiles($start = 0, $limit = 20, $in_ids = array(), $not_in_ids = array(), $params = array(), array $orderBy = array(), $group = '') {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        if ($not_in_ids) $where .= sprintf(' AND id not in %s', Db_Adapter_Pdo::quoteArray($not_in_ids));
        if ($in_ids) $where .= sprintf(' AND id in %s', Db_Adapter_Pdo::quoteArray($in_ids));

        $sql = sprintf('SELECT *'
                . 'FROM %s WHERE %s %s LIMIT %d, %d', $this->getTableName(), $where, $sort, intval($start), intval($limit));


        return Db_Adapter_Pdo::fetchAll($sql, $params);
    }

    /**
     *
     * @param array $params
     * @return string
     */
    public function getCanuseCount($in_ids = array(), $not_in_ids = array(), $params) {
        if ($params['package_type'] == 3) {
            $params["package_type"] = array(array(">=", 2), array("<=", 3));
        }
        $where = Db_Adapter_Pdo::sqlWhere($params);

        if ($not_in_ids) $where .= sprintf(' AND id not in %s', Db_Adapter_Pdo::quoteArray($not_in_ids));
        if ($in_ids) $where .= sprintf(' AND id in %s', Db_Adapter_Pdo::quoteArray($in_ids));
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->_name, $where);
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }

    /**
     * getPre
     */
    public function getResolution() {
        $sql = sprintf('SELECT resulution FROM %s GROUP BY resulution', $this->getTableName());
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     *
     * @param unknown_type $params
     * @return Ambigous <number, string>
     */
    private function _cookSearch($params) {
        if ($params['title']) $where .= " AND title like '%" . Db_Adapter_Pdo::filterLike($params['title']) . "%'";
        if ($params['file_type']) $where .= ' AND id in (SELECT file_id FROM idx_file_type WHERE type_id = ' . Db_Adapter_Pdo::quote($params['file_type']) . ')';
        if ($params['status']) $where .= ' AND status = ' . Db_Adapter_Pdo::quote($params['status']);
        if ($params['user_id']) $where .= ' AND user_id = ' . Db_Adapter_Pdo::quote($params['user_id']);
        if ($params['resulution']) $where .= ' AND resulution = ' . Db_Adapter_Pdo::quote($params['resulution']);
        if ($params['file_ids']) $where .= ' AND id in ' . Db_Adapter_Pdo::quoteArray($params['file_ids']);
        if ($params['package_type']) $where .= ' AND package_type = ' . $params['package_type'];
        return $where;
    }

}
