<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Cut_Dao_Shops
 * @author ryan
 *
 */
class Cut_Dao_Store extends Common_Dao_Base{
	protected $_name = 'cut_goods_store';
	protected $_primary = 'id';

	public function getCountByType(){
		$sql = sprintf('SELECT type_id,COUNT(*) AS num FROM %s GROUP BY type_id', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}

}
