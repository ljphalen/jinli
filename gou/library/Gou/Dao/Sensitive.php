<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Dao_Sensitive
 * @author ryan
 *
 */
class Gou_Dao_Sensitive extends Common_Dao_Base {
	
	protected $_name = 'gou_sensitive';
	protected $_primary = 'id';

    public function getSum($col){
        $sql = sprintf('SELECT SUM(%s) FROM %s',$col, $this->getTableName());
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }
}