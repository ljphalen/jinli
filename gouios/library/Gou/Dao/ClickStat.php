<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Gou_Dao_ClickStat extends Common_Dao_Base{
	protected $_name = 'gou_click_stat';
	protected $_primary = 'id';
	
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND dateline >= %s', Db_Adapter_Pdo::quote($params['start_time']));
			unset($params['start_time']);
		}
		if ($params['end_time']) {
			$sql.= sprintf(' AND dateline <= %s', Db_Adapter_Pdo::quote($params['end_time']));
			unset($params['end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
	
	/**
	 *
	 * @param string $sqlWhere
	 * @return string
	 */
	public function sumCount($sqlWhere) {
		$sql = sprintf('SELECT sum(number) FROM %s WHERE %s', $this->getTableName(), $sqlWhere);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}