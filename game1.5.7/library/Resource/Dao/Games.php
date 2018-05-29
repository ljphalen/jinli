<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_Games
 * @author lichanghua
 *
 */
class Resource_Dao_Games extends Common_Dao_Base{
	protected $_name = 'game_resource_games';
	protected $_primary = 'id';
	
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['name']) {
			$sql .= " AND (name like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%' or label like '%" . Db_Adapter_Pdo::filterLike($params['name']) . "%')";
		}
		unset($params['name']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}