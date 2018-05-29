<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Mall_Dao_Goods extends Common_Dao_Base{
	protected $_name = 'gou_mall_goods';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNormalMallGoods($start, $limit, $params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNormalMallGoodsCount($params) {
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
	public function getMallGoodsByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param int $guideTypes
	 * @return multitype:
	 */
	public function getByCategorys($start, $limit, $categorys) {
		$time = Common::getTime();
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND category_id IN %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time,Db_Adapter_Pdo::quoteArray($categorys), $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getByCategorysCount($categorys) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND category_id IN %s ORDER BY sort DESC',$this->getTableName(), $time, $time,Db_Adapter_Pdo::quoteArray($categorys));
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}
