<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_Pgroup
 * @author lichanghua
 *
 */
class Resource_Dao_Pgroup extends Common_Dao_Base{
	protected $_name = 'game_resource_pgroup';
	protected $_primary = 'id';
	
	
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return multitype:
	 */
	public function getSortPgroup($start, $limit, $params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $model
	 * @return multitype:
	 */
	public function getPgroupBymodel($model) {
		$sql = sprintf("SELECT * FROM %s WHERE LOCATE('%s', p_title) > 0 ORDER BY create_time DESC LIMIT 1",$this->getTableName(), $model);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 *
	 * @param unknown_type $model
	 * @return multitype:
	 */
	public function getPgroupBymodels($model) {
		$sql = sprintf("SELECT * FROM %s WHERE LOCATE('%s', p_title) > 0 ORDER BY create_time DESC",$this->getTableName(), $model);
		return $this->fetcthAll($sql);
	}
	
	
	/**
	 *
	 * @param unknown_type $model
	 * @return multitype:
	 */
	public function getPgroupBymodelId($model_id) {
		$sql = sprintf("SELECT * FROM %s WHERE LOCATE('%s', p_id) > 0 ORDER BY create_time DESC",$this->getTableName(), $model_id);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function countSortPgroup($params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE  %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	
	public function updateApiPgroupStatus($data,$statu) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id IN %s', $this->getTableName(), $statu, Db_Adapter_Pdo::quoteArray($data));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if (is_array($params['ids']) && count($params['ids'])) {
			$sql .= " AND out_id IN " . Db_Adapter_Pdo::quoteArray($params['ids']);
		}
		if ($params['title']) {
			$sql .= " AND p_title like '%" . Db_Adapter_Pdo::filterLike($params['title']) . "%'";
		}
		if ($params['st'] == '1') {
			$sql .= " AND id = 0 ";
		}
		unset($params['ids']);
		unset($params['title']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
	
	/**
	 *
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'id'.','. implode(', ', $_returns) . ')';
	}
}
