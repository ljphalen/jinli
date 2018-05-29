<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Feedback
 * @author tiger
 *
 */
class Gionee_Dao_Feedback extends Common_Dao_Base {
	protected $_name = '3g_topic_feedback';
	protected $_primary = 'id';

	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseAds($start, $limit, $params) {
		$time  = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC, id DESC LIMIT %d,%d', $this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}

	/**
	 *
	 * @param unknown_type $params
	 */
	public function getCanUseAdCount($params) {
		$time  = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s', $this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 得到所有符合条件的统计数据
	 */
	public function getDataByParams($elements,$where,$group,$order,$limitArr){
		$words = '';
		$words .=' '.implode(' ,', $elements);
		$where  = Db_Adapter_Pdo::sqlWhere($where);
		$orderBy = Db_Adapter_Pdo::sqlSort($order);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$limit = ' ';
		if($limitArr){
			$limit .=' LIMIT  '.implode(',', $limitArr);
		}
		$sql = sprintf("SELECT %s FROM %s WHERE %s  %s %s %s",$words,$this->getTableName(),$where,$groupBy,$orderBy,$limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 获取指定专题的反馈分布
	 */
	public function getTopicFeedbackStat($topicId , $statopts = array()) {
		$topicId = intval($topicId);
		$opts = '';
		if(!empty($statopts)) {
			$opts .= ' and option_num in ('.implode(',',$statopts).') ';
		}
		$sql = sprintf('SELECT sum(num) FROM %s WHERE topic_id=%d %s',$this->_name,$topicId,$opts);
		$sum = Db_Adapter_Pdo::fetchCloum($sql,0);
		$sql = sprintf('SELECT `option_num`,sum(num) num FROM %s WHERE topic_id=%d %s group by option_num order by option_num asc',$this->_name,$topicId,$opts);
		//var_dump($sql);exit;
		$res = $this->fetcthAll($sql);
		return array($sum,$res);
	}
}