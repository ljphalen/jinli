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
}
