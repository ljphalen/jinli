<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Dao_News
 * @author fanch
 *
 */
class Gou_Dao_Story extends Common_Dao_Base {
	
	protected $_name = 'gou_story';
	protected $_primary = 'id';


    public function getCountByCat(){
        $sql = sprintf("SELECT category_id AS k,count(id) AS v FROM %s GROUP BY category_id ",$this->_name);
        return $this->fetcthAll($sql);
    }

}