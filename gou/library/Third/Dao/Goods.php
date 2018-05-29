<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Third_Dao_Good
 * @author Terry
 *
 */
class Third_Dao_Goods extends Common_Dao_Base{
	protected $_name = 'gou_third_goods';
	protected $_primary = 'id';
	public $goods_id;

	public function getTableName() {
		return $this->_name."_".(($this->goods_id % 10) + 1);
	}
}
