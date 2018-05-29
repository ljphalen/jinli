<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_IdxGameResourcePackage
 * @author lichanghua
 *
 */
class Resource_Dao_IdxGameResourcePackage extends Common_Dao_Base{
	protected $_name = 'idx_game_diff_package';
	protected $_primary = 'id';
	
	public function getCountResourceDiff() {
		$sql = sprintf('SELECT version_id,game_id,count(version_id) AS num FROM %s GROUP BY version_id',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	public function _cookParams($params) {
		$sql = ' ';
		if (is_array($params['id']) && count($params['id'])) {
			$sql .= " AND game_id IN " . Db_Adapter_Pdo::quoteArray($params['id']);
		}
		unset($params['id']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
