<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Gou_Dao_ReadCoin
 * @author tiansh
 *
 */
class Gou_Dao_ReadCoin extends Common_Dao_Base{
	protected $_name = 'gou_read_coin';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $params
	 * @return boolean|mixed
	 */
	public function getCanUseReadcoin($goods_id, $limit) {
		$sql = sprintf('SELECT * FROM %s WHERE goods_id = %d AND order_id = "" ORDER BY id DESC LIMIT 0,%d', $this->getTableName(), $goods_id, $limit);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return boolean|mixed
	 */
	public function getCanUseReadcoinCount($goods_id) {
		$sql = sprintf('SELECT count(*) FROM %s WHERE goods_id = %d AND order_id = "" ', $this->getTableName(), $goods_id);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 指量更新
	 * @param array $data
	 * @param array $params
	 * @return boolean
	 */
	public function updateByIds($data, $ids) {
		if (!is_array($data) || !is_array($ids)) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE id in %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
}