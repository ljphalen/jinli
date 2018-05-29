<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_WxFeedback extends Common_Dao_Base {
	protected $_name = '3g_wx_feedback';
	protected $_primary = 'id';
	
	
	public function getRewardsData($params,$orderBy,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$order  = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = " SELECT COUNT(openid) as total, DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m-%d') as get_date  FROM  {$this->getTableName()} WHERE {$where} GROUP BY  get_date  {$order}  LIMIT {$page},{$pageSize}";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}