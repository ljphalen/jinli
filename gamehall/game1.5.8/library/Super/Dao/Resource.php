<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Super_Dao_Resource extends Common_Dao_Base{
	protected $_name = 'game_super_resource';
	protected $_primary = 'id';
	
	public function _cookSql($params) {
		$sql = '1';
		if ($params['name']){
			$sql .= sprintf(" AND name LIKE '%%%s%%'", $params['name']);
		}
		unset($params['name']);
		$sql .=" AND " . Db_Adapter_Pdo::sqlWhere($params);
		return $sql;
	}
}
