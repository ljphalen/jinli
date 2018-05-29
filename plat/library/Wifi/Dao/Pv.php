<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author rainkid
 *
 */
class Wifi_Dao_Pv extends Common_Dao_Base{
    protected $_name = 'tj_pv';
    protected $_primary = 'id';

    /**
     *
     * @param unknown_type $sDate
     * @param unknown_type $eDate
     */
    public function getListByTime($sDate, $eDate, $params) {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $sql = sprintf('SELECT * FROM %s WHERE %s AND dateline BETWEEN "%s" AND "%s" ORDER BY dateline DESC', $this->getTableName(), $where, $sDate, $eDate);
        return $this->fetcthAll($sql);
    }
}