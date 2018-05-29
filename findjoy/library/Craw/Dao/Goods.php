<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Craw_Dao_Goods
 * @author Sam
 *
 */
class Craw_Dao_Goods extends Common_Dao_Base{
    protected $_name = 'craw_goods';
    protected $_primary = 'id';
    public $item_id;

    public function getTableName() {
        return $this->_name."_".(($this->item_id % 10) + 1);
    }
}
