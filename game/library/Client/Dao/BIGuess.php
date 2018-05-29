<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅 -- 猜你喜欢
 * Client_Dao_BIGuess
 * @author lichanghua
 *
 */
class Client_Dao_BIGuess extends Common_Dao_Base{
	protected $_name = 'dlv_game_recomend_imei';
	protected $_primary = '';
	public $adapter = 'BI';
	
	
	public function getTopBIGuess() {
		$sql = sprintf('SELECT * FROM %s LIMIT 100000',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getTopBIGuessCount() {
		$sql = sprintf('SELECT count(*) FROM %s LIMIT 100000',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
