<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_Log extends Common_Dao_Base {
	protected $_name = 'tj_log';
	protected $_primary = 'id';


	public function getLastIdByType($type, $limit = 10) {
		$sql = sprintf("SELECT DISTINCT `key` FROM %s WHERE `type` = '%s' GROUP BY `key` ORDER BY `val` LIMIT %d", $this->getTableName(), $type, $limit);
		return Db_Adapter_Pdo::fetchAll($sql);
	}


	/**
	 * 统计及查询相关信息
	 */
	public function complexSum($var, $otherWords = array(), $where, $group = array(), $sort = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$group = Db_Adapter_Pdo::sqlGroup($group);
		$sort  = Db_Adapter_Pdo::sqlSort($sort);
		$other = '';
		if ($otherWords) {
			foreach ($otherWords as $k => $v) {
				$otherWords[$k] = Db_Adapter_Pdo::fieldMeta($v);
			}
			$other .= ', ' . implode(', ', $otherWords);
		}
		$sql = sprintf('SELECT SUM(%s) total  %s FROM %s WHERE %s %s %s', $var, $other, $this->getTableName(), $where, $group, $sort);
		return $this->fetcthAll($sql);

	}

	public function getTotalByMonth($type, $m = '') {
		if (empty($m)) {
			$m = date('Ym');
		}
		$where = array(
			'date' => array(array('>=', "{$m}01"), array('<=', "{$m}31")),
			'type' => $type,
		);
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$sql   = sprintf("SELECT  `date`,SUM(`val`) AS total  FROM %s WHERE %s GROUP BY `date` ORDER BY `date` DESC", $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getVoipExChangeMonthData($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT  `date`, SUM(`val`) AS total ,`key` , DATE_FORMAT(`date`,'%Y%m') as `month`   FROM {$this->getTableName()}  WHERE  {$where}  GROUP BY `key` , `month`  ORDER BY `month` DESC"; 
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}