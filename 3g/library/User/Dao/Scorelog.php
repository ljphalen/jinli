<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//用户积分日志
class User_Dao_Scorelog extends Common_Dao_Base {
    protected $_name    = 'user_score_log';
    protected $_primary = 'id';



    public  function totalRemindScores($where,$group,$orderBy ){
        $where = Db_Adapter_Pdo::sqlWhere($where);
        $group = Db_Adapter_Pdo::sqlGroup($group);
        $order = Db_Adapter_Pdo::sqlSort($orderBy);
        $sql  = sprintf( "SELECT SUM(affected_score) as total_scores ,uid ,`date`,score_type from %s WHERE %s %s %s",$this->getTableName(),$where,$group,$order);
        return Db_Adapter_Pdo::fetchAll($sql);

    }
    //用户当天获得积分总数
    public function  getDayIncreScoresAction($params) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf("SELECT  COUNT('affected_score') FROM %s WHERE %s", $this->getTableName(), $where);
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }

    //检测用户当天完成任务的情况
    public function checkTasks($where, $group) {
        $where   = Db_Adapter_Pdo::sqlWhere($where);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $fields  = ' ';
        $fields .= implode(',', $group);
        $sql = sprintf("SELECT %s FROM %s  WHERE %s %s", $fields, $this->getTableName(), $where, $groupBy);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function batchInsert($keys = array(), $vals = array()) {
        if (!is_array($keys) || !is_array($vals)) return false;
        $sql = sprintf("INSERT INTO %s (%s)  VALUES %s", $this->getTableName(), implode(',', $keys), Db_Adapter_Pdo::quoteMultiArray($vals));
        return Db_Adapter_Pdo::execute($sql);
    }

    public function getDayEarnScoresInfo($page, $pageSize, $where, $group, $order) {
        $where   = Db_Adapter_Pdo::sqlWhere($where);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $sort    = Db_Adapter_Pdo::sqlSort($order);
        $sql     = sprintf("SELECT date, SUM(affected_score) as earned_amount , COUNT(DISTINCT(uid)) AS user_amount    FROM %s WHERE %s %s %s LIMIT %d, %d", $this->getTableName(), $where, $groupBy, $sort, $page, $pageSize);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getEverydayScoreInfo($params, $group) {
        $where   = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $sql     = sprintf("SELECT SUM(affected_score) as quiz_scores , date FROM %s  WHERE  %s %s", $this->getTableName(), $where, $groupBy);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getDoneTasksList($page, $pageSize, $where, $group, $order) {
        $where   = Db_Adapter_Pdo::sqlWhere($where);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $ordrBy  = Db_Adapter_Pdo::sqlSort($order);
        $sql     = sprintf("SELECT date,COUNT(*) as done_tasks , COUNT(DISTINCT(uid)) as user_amount  FROM %s WHERE %s %s %s LIMIT %d , %d ", $this->getTableName(), $where, $groupBy, $ordrBy, $page, $pageSize);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getCountByParams($params, $group) {
        $where   = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $sql     = sprintf("SELECT COUNT(*)  as amount, score_type  FROM %s  WHERE %s %s", $this->getTableName(), $where, $groupBy);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getTotalDoneTasksInfo($where) {
        $where = Db_Adapter_Pdo::sqlWhere($where);
        $sql   = sprintf("SELECT COUNT(*) AS tasks_amount,COUNT(DISTINCT(uid)) as user_amount FROM %s WHERE %s", $this->getTableName(), $where);
        return Db_Adapter_Pdo::fetch($sql);
    }

    public function getLotteryDayData($params, $group, $sort) {
        $where   = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $orderBy = Db_Adapter_Pdo::sqlSort($sort);
        $sql     = sprintf("SELECT COUNT(DISTINCT(uid)) AS total_users , SUM(affected_score) AS total_scores,date,score_type  FROM %s WHERE %s %s %s ", $this->getTableName(), $where, $groupBy, $orderBy);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getDrawingTimesList($params, $group, $sort) {
        $where   = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $orderBy = Db_Adapter_Pdo::sqlSort($sort);
        $sql     = sprintf("SELECT COUNT(*) as drawing_times,date FROM %s WHERE %s %s %s", $this->getTableName(), $where, $groupBy, $orderBy);
        return Db_Adapter_Pdo::fetchAll($sql);
    }


    public function getExchangeChatSumData($params = array()) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql   = sprintf("SELECT COUNT(DISTINCT(uid)) AS  v_exchange_total_user,SUM(affected_score)  AS v_exchange_cost_scores  FROM %s WHERE  %s ", $this->getTableName(), $where);
        return Db_Adapter_Pdo::fetch($sql);
    }


    /**
     * @param unknown $where
     * @param unknown $group
     */
    public function getVoipExchangeAmount($where, $group) {
        $where   = Db_Adapter_Pdo::sqlWhere($where);
        $groupBy = Db_Adapter_Pdo::sqlGroup($group);
        $sql     = sprintf("SELECT COUNT(*) As total ,date FROM %s WHERE %s %s ", $this->getTableName(), $where, $groupBy);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getVoipExchangeDetailData($params, $group) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $group = Db_Adapter_Pdo::sqlGroup($group);
        $sql   = sprintf("SELECT COUNT(DISTINCT(uid)) as total_users, COUNT(*) as total_times,  fk_earn_id,date  FROM %s WHERE %s %s", $this->getTableName(), $where, $group);
        return Db_Adapter_Pdo::fetchAll($sql);
    }


    public function snatchCalucateData($params, $groupBy) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $group = Db_Adapter_Pdo::sqlGroup($groupBy);
        $sql   = sprintf("SELECT  COUNT(DISTINCT(uid)) as total_users , COUNT(*) total_times,SUM(affected_score) as total_cost_scores , date,score_type FROM %s WHERE %s %s", $this->getTableName(), $where, $group);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getTaskBrowserOnlineTotalCoin($sDate, $eDate) {
        $sql = sprintf('SELECT `date`,affected_score as stage,sum(affected_score) AS val FROM  %s WHERE score_type=208 AND `date` >= \'%s\' AND `date` <= \'%s\' GROUP BY `date`,affected_score', $this->getTableName(), $sDate, $eDate);
        return Db_Adapter_Pdo::fetchAll($sql);

    }
}