<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author lichanghua
 *
 */
class Gou_Dao_LocalGoods extends Common_Dao_Base{
	protected $_name = 'gou_local_goods';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param array $ids
	 * @return multitype:
	 */
	public function getLocalGoodsByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getCanUseLocalGoods($start, $limit, $params,array $orderBy = array()) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE start_time < %d AND end_time > %d  AND %s %s LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $sort, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getCanUseLocalGoodsCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalLocalGoods($start, $limit, $params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalLocalGoodsCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getAfterLocalGoods($start, $limit, $params,array $orderBy = array()) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE start_time > %d AND %s %s LIMIT %d,%d',$this->getTableName(), $time, $where, $sort,  $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getAfterLocalGoodsCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE start_time > %d AND %s',$this->getTableName(), $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
     * update goods PurchaseNum
     * @param unknown_type $goods_id
     * @param unknown_type $num
     */
    public function updatePurchaseNum($goods_id, $num) {
        if (!$goods_id || !$num) return false;
        $sql = sprintf('UPDATE %s SET purchase_num=purchase_num+%d WHERE id = %d', $this->getTableName(), intval($num), intval($goods_id));
        return Db_Adapter_Pdo::execute($sql, array(), false);
    }
    
    /**
     * update goods stock
     * @param unknown_type $goods_id
     * @param unknown_type $num
     */
    public function minusStock($goods_id, $num) {
        if (!$goods_id || !$num) return false;
        $sql = sprintf('UPDATE %s SET stock_num=stock_num-%d WHERE stock_num >= %d and id = %d', $this->getTableName(), intval($num), intval($num), intval($goods_id));
        return Db_Adapter_Pdo::execute($sql, array(), false);
    }
    
    /**
     * update goods stock
     * @param unknown_type $goods_id
     * @param unknown_type $num
     */
    public function addStock($goods_id, $num) {
        if (!$goods_id || !$num) return false;
        $sql = sprintf('UPDATE %s SET stock_num=stock_num+%d WHERE id = %d', $this->getTableName(), intval($num), intval($goods_id));
        return Db_Adapter_Pdo::execute($sql, array(), false);
    }
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['doing']) {
			$time = Common::getTime();
			if($params['doing'] == 1) {
				$sql.= sprintf(' AND start_time < %d AND end_time > %d', $time, $time);
			} elseif($params['doing'] == 2) {
				$sql.= sprintf(' AND start_time > %d ', $time);
			} else {
				$sql.= sprintf(' AND end_time < %d ', $time);
			}
			unset($params['doing']);
		}
		if ($params['title']) {
			$where .= " AND title like '%" . Db_Adapter_Pdo::_filterLike($params['title']) . "%'";
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
