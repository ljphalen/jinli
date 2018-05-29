<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gc_Dao_Ad
 * @author lichanghua
 *
 */
class Gc_Dao_LocalGoods extends Common_Dao_Base{
	protected $_name = 'gc_local_goods';
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
	public function getCanUseLocalGoods($start, $limit, $params) {
		$time = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1  AND isrecommend = 1 AND stock_num > 0 AND start_time < %d AND end_time > %d  AND stock_num > 0 AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
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
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND isrecommend = 1 AND start_time < %d AND end_time > %d AND stock_num > 0 AND %s ORDER BY sort DESC',$this->getTableName(), $time, $time, $where);
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
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC, isrecommend LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
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
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getAfterLocalGoods($start, $limit, $params) {
		$time = Common::getTime();
		$sql = sprintf('SELECT * FROM %s WHERE status = 1  AND start_time > %d  ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time,  $start, $limit);
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
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time > %d ',$this->getTableName(), $time);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * update goods PurchaseNum
	 * @param unknown_type $goods_id
	 * @param unknown_type $num
	 */
	public function updatePurchaseNum($goods_id, $num) {
		if (!$goods_id || !$num) return false;
		$sql = sprintf('UPDATE %s SET stock_num=stock_num-%d,purchase_num=purchase_num+%d WHERE stock_num>=%d AND id = %d', $this->getTableName(), intval($num), intval($num), intval($num), intval($goods_id));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
