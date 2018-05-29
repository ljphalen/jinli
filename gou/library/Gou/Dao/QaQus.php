<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Dao_QaQus
 * @author terry
 *
 */
class Gou_Dao_QaQus extends Common_Dao_Base {

    protected $_name = 'gou_qa_qus';
    protected $_primary = 'id';

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealCusUvStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = strtotime($sDate);
        $eDate = strtotime($eDate);
        $eDate_temp = $sDate + 86400;
        $result = array();
        do{
            $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `is_admin` = 0 AND `create_time` >= %s AND `create_time` <= %s GROUP BY `uid` ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, Db_Adapter_Pdo::quote($sDate), Db_Adapter_Pdo::quote($eDate_temp));
            $result = array_merge($result, $this->fetcthAll($sql));
            $sDate += 86400;
            $eDate_temp += 86400;
        }while($eDate_temp <= $eDate);
        return $result;
    }

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getVirQusStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote(strtotime($sDate));
        $eDate = Db_Adapter_Pdo::quote(strtotime($eDate));

        $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `is_admin` = 1 AND  `create_time` >= %s AND `create_time` <= %s ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealQusStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote(strtotime($sDate));
        $eDate = Db_Adapter_Pdo::quote(strtotime($eDate));

        $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `is_admin` = 0 AND  `create_time` >= %s AND `create_time` <= %s ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealQusCusUvStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = strtotime($sDate);
        $eDate = strtotime($eDate);
        $eDate_temp = $sDate + 86400;
        $result = array();
        do{
            $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `is_admin` = 0 AND `create_time` >= %s AND `create_time` <= %s GROUP BY `uid` ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, Db_Adapter_Pdo::quote($sDate), Db_Adapter_Pdo::quote($eDate_temp));
            $result = array_merge($result, $this->fetcthAll($sql));
            $sDate += 86400;
            $eDate_temp += 86400;
        }while($eDate_temp <= $eDate);
        return $result;
    }

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealPassQusStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = Db_Adapter_Pdo::quote(strtotime($sDate));
        $eDate = Db_Adapter_Pdo::quote(strtotime($eDate));

        $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `is_admin` = 0 AND  `status` = 2 AND  `create_time` >= %s AND `create_time` <= %s ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }

    /**
     *
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public function getRealPassQusCusUvStat($sDate, $eDate){
        if(empty($sDate) || empty($eDate)) return false;
        $sDate = strtotime($sDate);
        $eDate = strtotime($eDate);
        $eDate_temp = $sDate + 86400;
        $result = array();
        do{
            $sql = sprintf("SELECT FROM_UNIXTIME(A.create_time, '%%Y-%%m-%%d') AS dateline, COUNT(*) AS stat FROM (SELECT * FROM %s WHERE `is_admin` = 0 AND  `status` = 2 AND `create_time` >= %s AND `create_time` <= %s GROUP BY `uid` ORDER BY `create_time` ASC) AS A GROUP BY `dateline`", $this->_name, Db_Adapter_Pdo::quote($sDate), Db_Adapter_Pdo::quote($eDate_temp));
            $result = array_merge($result, $this->fetcthAll($sql));
            $sDate += 86400;
            $eDate_temp += 86400;
        }while($eDate_temp <= $eDate);
        return $result;
    }

}