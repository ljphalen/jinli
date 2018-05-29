<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)获取临时消息消息明细
 * Client_Dao_BIPushMsgDetail
 * @author lichanghua
 *
 */
class Client_Dao_BIPushMsgDetail extends Common_Dao_Base{
	protected $_name = 'dlv_game_push_daily';
	protected $_primary = '';
	public $adapter = 'BI';
	
	
    public function delByBeforDay($date) {
		$sql = sprintf('DELETE FROM %s WHERE day_id <= %s',$this->getTableName(), $date);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
