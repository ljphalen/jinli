<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * User_Dao_CoinLog
 * @author tiansh
 *
 */
class User_Dao_CoinLog extends Common_Dao_Base{
	protected $_name = 'u_coin_log';
	protected $_primary = 'id';
	
	/**
	 * get want logs by goods_id
	 * @param int $goods_id
	 * @param array $params
	 */
	public function getCoinLogsByUid($uid) {
		$sql = sprintf('SELECT * FROM %s WHERE out_uid = %s ',$this->getTableName(), $uid);
		return $this->fetcthAll($sql);
	}
}
