<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Browser_Dao_Product extends Common_Dao_Base{
	protected $_name = 'browser_product';
	protected $_primary = 'id';
	
	/**
	 *
	 */
	public function getNewProduct() {
		$sql = sprintf('SELECT * FROM %s WHERE is_new = 1 ORDER BY sort DESC, id DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
}
