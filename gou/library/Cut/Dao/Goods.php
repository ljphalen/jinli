<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Goods
 * @author ryan
 *
 */
class Cut_Dao_Goods extends Common_Dao_Base{
	protected $_name = 'cut_goods';
	protected $_primary = 'id';
	

	/**
	 * 
	 * @param array $ids
	 * @return multitype:
	 */
	public function getGoodsByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}

    public function updateQuantity($num, $id) {
        $sql = sprintf('UPDATE %s SET quantity = quantity - %d WHERE id = %d AND quantity >= %d',$this->_name, intval($num), $id ,abs($num));
        return Db_Adapter_Pdo::execute($sql, array(), true);
    }
    
    public function getNotCutCount() {
        //$sql = sprintf('SELECT COUNT(*) FROM %s WHERE status=1 AND sale_num<5 AND end_time>%d AND quantity>0',$this->_name, Common::getTime());
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE status=1 AND end_time>%d AND quantity>0',$this->_name, Common::getTime());
        return Db_Adapter_Pdo::fetchCloum($sql, 0);    
    }
    
    public function getWillCutCount() {
        $sql = sprintf("SELECT COUNT(*) from %s WHERE status !=2 AND start_time>%d", $this->_name, Common::getTime());
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }
}
