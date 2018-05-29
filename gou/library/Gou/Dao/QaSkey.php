<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Dao_QaSkey
 * @author terry
 *
 */
class Gou_Dao_QaSkey extends Common_Dao_Base {

    protected $_name = 'gou_qa_skey';
    protected $_primary = 'id';

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getEmptyStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote($sDate);
        $eDate = Db_Adapter_Pdo::quote($eDate);
        $sql = sprintf("SELECT `dateline`, SUM(`s_empty_count`) AS `stat` FROM %s WHERE `dateline` >= %s AND `dateline` <= %s GROUP BY `dateline` ORDER BY `dateline` ASC ", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getHasStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote($sDate);
        $eDate = Db_Adapter_Pdo::quote($eDate);
        $sql = sprintf("SELECT `dateline`, SUM(`s_has_count`) AS `stat` FROM %s WHERE `dateline` >= %s AND `dateline` <= %s GROUP BY `dateline` ORDER BY `dateline` ASC ", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getCountStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote($sDate);
        $eDate = Db_Adapter_Pdo::quote($eDate);
        $sql = sprintf("SELECT `dateline`, SUM(`count`) AS `stat` FROM %s WHERE `dateline` >= %s AND `dateline` <= %s GROUP BY `dateline` ORDER BY `dateline` ASC ", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }

}