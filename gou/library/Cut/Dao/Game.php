<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Cut_Dao_Game
 * @author terry
 *
 */
class Cut_Dao_Game extends Common_Dao_Base{
    protected $_name = 'cut_game_log';
    protected $_primary = 'id';

    /**
     * 获取每天砍价真实人数
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealCutUvStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = strtotime($sDate);
        $eDate = strtotime($eDate);
        $eDate_temp = $sDate + 86400;
        $result = array();
        do{
            $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `create_time` >= %s AND `create_time` <= %s GROUP BY `uid` ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, Db_Adapter_Pdo::quote($sDate), Db_Adapter_Pdo::quote($eDate_temp));
            $result = array_merge($result, $this->fetcthAll($sql));
            $sDate += 86400;
            $eDate_temp += 86400;
        }while($eDate_temp <= $eDate);
        return $result;
    }

    /**
     * 获取每天砍价次数
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealCutPvStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote(strtotime($sDate));
        $eDate = Db_Adapter_Pdo::quote(strtotime($eDate));

        $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `create_time` >= %s AND `create_time` <= %s ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }
}
