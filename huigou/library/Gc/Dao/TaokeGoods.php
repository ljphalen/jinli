<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gc_Dao_Ad
 * @author rainkid
 *
 */
class Gc_Dao_TaokeGoods extends Common_Dao_Base{
	protected $_name = 'gc_taoke_goods';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalGoods($start, $limit, $params) {
		$time = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
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
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
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
	 * @param array $ids
	 * @return multitype:
	 */
	public function getGoodsByNum_iids($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE num_iid IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
   /**
	 * 
	 * @param array $params
	 */
	public function getCountBySubjectId() {
		$time = Common::getTime();
		$sql = sprintf('select count(*) as count,subject_id FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d group by subject_id',$this->getTableName(), $time, $time);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
}
