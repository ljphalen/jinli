<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Dao_Blacklist
 * @author lichanghau
 *
 */
class Freedl_Dao_Blacklist extends Common_Dao_Base{
	protected $_name = 'game_client_freedl_blacklist';
	protected $_primary = 'id';
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['name']) {
			$sql .= " AND (imsi like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%' or uname like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%' or imei like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%')";
		}
		unset($params['name']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
