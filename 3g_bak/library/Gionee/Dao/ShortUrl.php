<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_ShortURL
 * @author huwei
 *
 */
class Gionee_Dao_ShortUrl extends Common_Dao_Base {
	protected $_name = '3g_short_url';
	protected $_primary = 'id';
	
	public function getUserIndexIndex($start,$limit,$url) {
				
		$sql = 'select * from '.$this->getTableName().' where url like "%'.$url.'" order by id desc limit '.$start.','.$limit; 
				
		$res = Db_Adapter_Pdo::fetchAll($sql);
		
		return $res;		
		
	}
	
	
	
}