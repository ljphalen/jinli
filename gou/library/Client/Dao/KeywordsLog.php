<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Client_Dao_Keywords
 * @author tiansh
 *
 */
class Client_Dao_KeywordsLog extends Common_Dao_Base{
    protected $_name = 'client_keywords_log';
    protected $_primary = 'id';

    /**
     *
     * @param unknown_type $sqlWhere
     */
    public function sBy($start, $limit, $sqlWhere = 1 ) {
        $sql = sprintf('SELECT  `keyword`, count(`id`) as num FROM %s WHERE %s GROUP BY `keyword_md5` ORDER BY num DESC LIMIT %d,%d', $this->getTableName(), $sqlWhere, $start, $limit);
        return $this->fetcthAll($sql);
    }

    /**
     *
     * @param string $sqlWhere
     * @return string
     */
    public function searchCount($sqlWhere) {
        if($sqlWhere == '1 ') {
            $sql = sprintf('SELECT COUNT(distinct keyword_md5) FROM %s WHERE %s ', $this->getTableName(), $sqlWhere);
        }else{
            $sql = sprintf('SELECT COUNT(*) FROM (SELECT COUNT(*) FROM %s WHERE %s GROUP BY keyword_md5) asc', $this->getTableName(), $sqlWhere);
        }
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }

    /**
     *
     * @param unknown_type $params
     * @return string
     */
    public function _cookParams($params){
        $sql = ' ';
        if ($params['start_time']) {
            $sql.= sprintf(' AND dateline >= %s', '"'.$params['start_time'].'"');
            unset($params['start_time']);
        }
        if ($params['end_time']) {
            $sql.= sprintf(' AND dateline <= %s', '"'.$params['end_time'].'"');
            unset($params['end_time']);
        }
        if($params['keyword']) {
            $where .= " AND keyword like '%" . Db_Adapter_Pdo::_filterLike($params['keyword']) . "%'";
        }
        return Db_Adapter_Pdo::sqlWhere($params).$sql;
    }

}
