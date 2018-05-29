<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Topic
 * @author tiger
 *
 */
class Gionee_Dao_Topic extends Common_Dao_Base {
	protected $_name = '3g_topic';
	protected $_primary = 'id';

	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$time  = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql   = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d', $this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}

	/**
	 * 
	 * @param unknown $id
	 * @return boolean
	 */
	public function addLike($id) {
		$sql = sprintf('UPDATE %s SET like_num = like_num + 1 WHERE id = %d', $this->getTableName(), $id);
		return Db_Adapter_Pdo::execute($sql);
	}
	
	/**
	 * 获得指定元素
	 */
	public function getElements($elements =array(),$params=array(),$orders=array(),$arrLimit= array()){
		$words = '';
		foreach ($elements as $k=>$v){
			$elements[$k] = Db_Adapter_Pdo::fieldMeta($v);
		}
		$words .=' '.implode(' ,', $elements);
		$where  = Db_Adapter_Pdo::sqlWhere($params);
		$orderBy = Db_Adapter_Pdo::sqlSort($orders);
		$limit = ' ';
		if($arrLimit){
			$limit .=' LIMIT  '.implode(',', $arrLimit);
		}
		$sql = sprintf("SELECT %s FROM %s WHERE %s  %s %s",$words,$this->getTableName(),$where,$orderBy,$limit);
		return $this->fetcthAll($sql);
	}
}