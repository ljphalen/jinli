<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_TaskBrowserOnline extends Common_Dao_Base {
	protected $_name = "user_task_browseronline";
	protected $_primary = "id";

    public function getTaskBrowserOnlineTotalPeople($sDate,$eDate) {
        $sql= sprintf('SELECT cur_date as `date`,cur_stage as `stage`,COUNT(DISTINCT uid) AS val FROM  %s WHERE cur_date >= \'%s\' AND cur_date <= \'%s\'  GROUP BY cur_date,cur_stage',$this->getTableName(), $sDate,$eDate);
        return Db_Adapter_Pdo::fetchAll($sql);
    }
}