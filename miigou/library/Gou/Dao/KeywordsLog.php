<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Keywords
 * @author tiansh
 *
 */
class Gou_Dao_KeywordsLog extends Common_Dao_Base{
	protected $_name = 'miigou_keywords_log';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $sqlWhere
	 */
	public function searchBy($start, $limit, $sqlWhere = 1 ) {
		$sql = sprintf('SELECT  keyword, count(id) as num FROM %s WHERE %s GROUP BY keyword_md5 ORDER BY num DESC LIMIT %d,%d',$this->getTableName(), $sqlWhere, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param string $sqlWhere
	 * @return string
	 */
	public function searchCount($sqlWhere) {
		$sql = sprintf('SELECT COUNT(*) FROM (SELECT COUNT(*) FROM %s WHERE %s GROUP BY keyword_md5) as c', $this->getTableName(), $sqlWhere);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND create_time > %d', $params['start_time']);
			unset($params['start_time']);
		}
		if ($params['end_time']) {
			$sql.= sprintf(' AND create_time < %d', $params['end_time']);
			unset($params['end_time']);
		}
		if($params['keyword']) {
			$where .= " AND keyword like '%" . Db_Adapter_Pdo::_filterLike($params['keyword']) . "%'";
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
