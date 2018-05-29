<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_Inbuilt extends Common_Dao_Base
{
	protected $_name = '3g_bm_inbuilt';
	protected $_primary = 'id';

	public function getCate() {
		$sql = sprintf('SELECT DISTINCT `cate` FROM %s ORDER BY `cate` ASC' , $this->getTableName());
		return $this->fetcthAll($sql);
	}
}