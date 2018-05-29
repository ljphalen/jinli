<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Msg
 *
 */
class Game_Dao_Msg extends Common_Dao_Base{
	protected $_name = 'game_msg';
	protected $_primary = 'id';

	public function checkMap($msgid,$uid,$imei) {
		$sql = 'select * from game_msg_map where msgid='.intval($msgid).' and (uid=? or imei=?) limit 1';
		$map = Db_Adapter_Pdo::fetch($sql , array($uid,$imei));
		return $map;
	}
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['ids']) {
			$sql .= ' OR id IN ('.implode(',',$params['ids'][1]).')';
		}
		unset($params['ids']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
	
}
