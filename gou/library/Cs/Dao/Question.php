<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Cs_Dao_Question
 * @author ryan
 *
 */
class Cs_Dao_Question extends Common_Dao_Base{
	protected $_name = 'cs_faq_qa';
	protected $_primary = 'id';
    public function countByGroup($params = array(), $groupBy='') {
        $where = Db_Adapter_Pdo::sqlWhere($params);
        $groupBy = sprintf("GROUP BY %s" ,$groupBy);
        $sql = sprintf('SELECT cat_id,COUNT(*) AS num FROM %s WHERE %s %s', $this->getTableName(), $where, $groupBy);
        $ret =  Db_Adapter_Pdo::fetchAll($sql);
        return $ret;
    }
}
