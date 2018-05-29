<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅 -- 游戏推荐
 * Client_Dao_Recommend
 * @author lichanghua
 *
 */
class Client_Dao_Recommend extends Common_Dao_Base{
	protected $_name = 'dlv_game_recomend';
	protected $_primary = 'id';
	
	public function turncateRecommend() {
		$sql = sprintf('TRUNCATE %s',$this->getTableName());
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
