<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Blacklist
 * @author lichanghau
 *
 */
class Client_Dao_Blacklist extends Common_Dao_Base{
	protected $_name = 'game_client_blacklist';
	protected $_primary = 'id';
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['name']) {
			$sql .= " AND (name like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%' or imei like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%')";
		}
		unset($params['name']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
