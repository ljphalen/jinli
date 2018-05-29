<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Searchwords
 * @author rainkid
 *
 */
class Gionee_Dao_Searchwords extends Common_Dao_Base {
	protected $_name = '3g_search_words';
	
	
	public function getSearchHot($start,$end,$params,$group,$order){
		$where 		=  	Db_Adapter_Pdo::sqlWhere($params);
		$groupBy 	= 	Db_Adapter_Pdo::sqlGroup($group);
		$orderBy 	= 	Db_Adapter_Pdo::sqlSort($order);
		$sql = sprintf('SELECT sum(number) as total , content FROM %s WHERE %s %s %s LIMIT %s,%s',$this->getTableName(),$where,$groupBy,$orderBy,intval($start),intval($end));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}