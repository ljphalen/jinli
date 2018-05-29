<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Dao_QaAus
 * @author terry
 *
 */
class Gou_Dao_QaAus extends Common_Dao_Base {

    protected $_name = 'gou_qa_aus';
    protected $_primary = 'id';

    /**
     * 获取问贴最新一条回帖的列表(修改)
     * @param array $items
     * @return array|bool
     */
    public function getLastOneAus($items){
        if(!is_array($items) || empty($items)) return false;
        $items = Db_Adapter_Pdo::quoteArray($items);
        $sql = sprintf("SELECT * FROM (SELECT * FROM %s WHERE `item_id` IN %s AND `status` IN (0, 1, 2) ORDER BY `praise` DESC, `create_time` DESC) AS A GROUP BY A.item_id", $this->_name, $items);
        return $this->fetcthAll($sql);
    }

    /**
     * 获取每条问贴的有效回帖数
     * @param $items
     * @return array|bool
     */
    public function getAusCount($items){
        if(!is_array($items) || empty($items)) return false;

        $items = Db_Adapter_Pdo::quoteArray($items);
        $sql = sprintf("SELECT `item_id`, COUNT(*) AS total FROM %s WHERE `item_id` IN %s AND `status` IN (0, 1, 2) GROUP BY `item_id`", $this->_name, $items);
        return $this->fetcthAll($sql);
    }

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
    public function getVirAusStat($sDate, $eDate){
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
    public function getRealAusStat($sDate, $eDate){
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
    public function getRealAusCusUvStat($sDate, $eDate){
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
    public function getRealPassAusStat($sDate, $eDate){
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
    public function getRealPassAusCusUvStat($sDate, $eDate){
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