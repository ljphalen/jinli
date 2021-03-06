<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_RecommendNew
 * @author wupeng
 */
class Game_Dao_H5RecommendNew extends Common_Dao_Base{
	protected $_name = 'game_h5_recommend_tmp';
	protected $_primary = 'id';

	public function getRecommendList($start = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC', 'create_time'=>'DESC')){
		if($params){
			$where = Db_Adapter_Pdo::sqlWhere($params);
		}
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE %s  %s  LIMIT %d, %d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return  $this->fetcthAll($sql);
	}
	
	public function getRecommendListCount($params = array()){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s  ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}