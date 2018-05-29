<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Goods
 * @author tiansh
 *
 */
class Client_Dao_Goods extends Common_Dao_Base{
	protected $_name = 'client_goods';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalGoods($start, $limit, $params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC, id DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalGoodsCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return multitype:
	 */
	public function getGoodsByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param int $guideTypes
	 * @return multitype:
	 */
	public function getByCategorys($start, $limit, $categorys, $cache_time) {
		$time = Common::getTime();
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND category_id IN %s AND start_time = %d ORDER BY sort DESC, id DESC LIMIT %d,%d',$this->getTableName(), $time, $time,Db_Adapter_Pdo::quoteArray($categorys), $cache_time, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getByCategorysCount($categorys, $cache_time) {
		$time = Common::getTime();
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND category_id IN %s AND start_time = %d ',$this->getTableName(), $time, $time,Db_Adapter_Pdo::quoteArray($categorys), $cache_time);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}
