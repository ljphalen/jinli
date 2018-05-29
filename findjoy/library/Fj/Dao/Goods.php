<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Fj_Dao_Goods
 * @author tiansh
 *
 */
class Fj_Dao_Goods extends Common_Dao_Base{
	protected $_name = 'fj_goods';
	protected $_primary = 'id';
	
	public function updateQuantity($num, $id) {
	    $sql = sprintf('UPDATE %s SET stock_num = stock_num - %d WHERE id = %d AND stock_num >= %d',$this->_name, intval($num), $id ,abs($num));
	    return Db_Adapter_Pdo::execute($sql, array(), true);
	}
}
