<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Cs_Dao_Feedback
 * @author ryan
 *
 */
class Cs_Dao_Feedback extends Common_Dao_Base{
	protected $_name = 'cs_feedback_qa';
	protected $_primary = 'id';

    public function getListByGroup($start = 0, $limit = 20, array $params = array(), array $orderBy = array(),$groupBy) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $groupBy = sprintf("GROUP BY %s" ,$groupBy);
        $sql = sprintf('SELECT uid FROM %s WHERE %s %s %s LIMIT %d,%d', $this->getTableName(), $where, $groupBy, $sort,  intval($start), intval($limit));
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getsByGroup(array $params = array(), array $orderBy = array(),$groupBy) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sort = Db_Adapter_Pdo::sqlSort($orderBy);
        $groupBy = sprintf("GROUP BY %s" ,$groupBy);
        $sql = sprintf('SELECT uid FROM %s WHERE %s %s %s', $this->getTableName(), $where, $groupBy, $sort);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function countByGroup($params = array(), $groupBy='') {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = sprintf("GROUP BY %s" ,$groupBy);
        $sql = sprintf('SELECT type,COUNT(*) as num FROM %s WHERE %s %s', $this->getTableName(), $where, $groupBy);
        $ret =  Db_Adapter_Pdo::fetchAll($sql);
        return $ret;
    }

    /**
     * 获取每条分类的问题数
     * @param $items
     * @return array|bool
     */
    public function getFeedbackCount($items){
        if(!is_array($items) || empty($items)) return false;

        $items = Db_Adapter_Pdo::quoteArray($items);
        $sql = sprintf("SELECT `cat_id`, COUNT(*) AS total FROM %s WHERE `cat_id` IN %s AND `type` = 0 GROUP BY `cat_id`", $this->_name, $items);
        return $this->fetcthAll($sql);
    }
}
