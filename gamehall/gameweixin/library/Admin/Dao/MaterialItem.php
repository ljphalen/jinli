<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Admin_Dao_Group
 * @author rainkid
 *
 */
class Admin_Dao_MaterialItem extends Common_Dao_Base {
	protected $_name = 'material_news_item';
	
	public function getNewsIdListByItem($params) {
		if (!is_array($params)) return false;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT distinct(`news_id`)  FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
}