<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_RecommendDay
 * @author wupeng
 */
class Game_Dao_RecommendDay extends Common_Dao_Base{
	protected $_name = 'game_recommend_day';
	protected $_primary = 'id';
	
	
	public function getDailyGamesCounts($startTime, $endTime, $editId) {
	    if($editId) {
	        $params['id']=array('!=', $editId);
	    }
	    $params['status'] = Game_Service_RecommendDay::STATUS_OPEN;
	    $params['game_status']=Resource_Service_Games::STATE_ONLINE;
	    
	    $params1['start_time']=array('<=', $startTime);
	    $params1['end_time']=array('>', $startTime);
	    
	    $params2['start_time']=array('<', $endTime);
	    $params2['end_time']=array('>=', $endTime);
	    
	    $params3['start_time']=array('<=', $startTime);
	    $params3['end_time']=array('>=', $endTime);

	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $where1 = Db_Adapter_Pdo::sqlWhere($params1);
        $where2 = Db_Adapter_Pdo::sqlWhere($params2);
        $where3 = Db_Adapter_Pdo::sqlWhere($params3);
	    $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s AND ((%s) OR (%s) OR (%s)) ', $this->getTableName(), $where, $where1, $where2, $where3);
	    return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}