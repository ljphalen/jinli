<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅 -- 猜你喜欢
 * Client_Dao_Guess
 * @author lichanghua
 *
 */
class Client_Dao_Guess extends Common_Dao_Base{
	protected $_name = 'dlv_game_recomend_imei_0';
	protected $_primary = 'imei';
	
	public function getTableName() {
		return 'dlv_game_recomend_imei_' . intval($this->_getReadTable());
	}
	
	private function _getReadTable() {
		$cache = Cache_Factory::getCache();
		$ckey = Util_CacheKey::SUFFIX_OF_RECOMMEND_TABLE;
		$guess_table = $cache->get($ckey);
		return $guess_table;
	}
	
	public function getLoadTable() {
		$tn = intval($this->_getReadTable());
		return 'dlv_game_recomend_imei_' . ($tn ? 0 : 1);
	}
	
	/**
	 *
	 * @return string
	 */
	public function loadInsertData($path) {
		$sql = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE %s",$path ,$this->getLoadTable());
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function turncateGuess() {
		$sql = sprintf('TRUNCATE %s',$this->getLoadTable());
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
