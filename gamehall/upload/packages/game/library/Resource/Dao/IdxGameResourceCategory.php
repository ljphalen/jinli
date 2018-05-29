<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_IdxGameResourceCategory
 * @author lichanghua
 *
 */
class Resource_Dao_IdxGameResourceCategory extends Common_Dao_Base{
	protected $_name = 'idx_game_resource_category';
	protected $_primary = 'id';
	
	/**
	 * 根据一级分类获取该分类下对应的数据，禁止条件为空
	 * @param array $params 
	 * @param array $orderBy
	 * @return multitype:
	 */
	public function getAllByMainCategoryId($start, $limit, $params, $orderBy = array()){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM (SELECT * FROM %s WHERE %s GROUP BY game_id) AS T %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 根据一级分类统计游戏数量，禁止条件为空
	 * @param array $params
	 */
	public function getCountByParentId($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(*) FROM (SELECT * FROM %s WHERE %s GROUP BY game_id) AS T', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
