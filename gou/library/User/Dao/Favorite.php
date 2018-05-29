<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * User_Dao_Favorite
 * @author tiansh
 *
 */
class User_Dao_Favorite extends Common_Dao_Base{
	protected $_name = 'gou_user_favorite';
	protected $_primary = 'id';

	public function getCountByDay($start_day='',$end_day=''){
		if(!$end_day)  $end_day=$day = strtotime(date( "Y-m-d "));
		if(!$start_day)$start_day=strtotime(date('Y-m-d',strtotime('-30day')));
		$query = "SELECT count(1) as count,DATE(FROM_UNIXTIME(`create_time`)) AS day ,COUNT(distinct uid) as num FROM {$this->_name} WHERE create_time>=$start_day AND create_time<=$end_day GROUP BY day";
		$ret =  $this->fetcthAll($query);
		return $ret;
	}

	public function getFields($start, $limit, $fields = array(), $params = array(), $orderBy = array()){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$query = sprintf("SELECT %s  FROM %s WHERE %s %s LIMIT %d,%d ",$fields,$this->getTableName(),$where,$sort,$start,$limit);
		$ret =  $this->fetcthAll($query);
		return $ret;
	}

	public function countByFields($fields=array(),$params=array()){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$query = sprintf("SELECT count(%s) as num FROM %s WHERE %s ",$fields,$this->getTableName(),$where);
		$ret =  Db_Adapter_Pdo::fetchCloum($query, 0);
		return $ret;
	}


    /**
     * 获取分页列表数据
     * @param int $start
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array
     */
	public function getUniqueList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sort = Db_Adapter_Pdo::sqlSort($orderBy);
	    $sql = sprintf('SELECT * ,COUNT(*) AS total FROM %s WHERE %s GROUP BY item_id %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
	    return Db_Adapter_Pdo::fetchAll($sql);
	}

    /**
     * 根据参数统计总数
     * @param array $params
     * @return string
     */
	public function uniqueCount($params = array()) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sql = sprintf('SELECT COUNT(distinct item_id) FROM %s WHERE %s ', $this->getTableName(), $where);
	    return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}
