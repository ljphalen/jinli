<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Gou_Dao_Pv extends Common_Dao_Base{
	protected $_name = 'tj_pv';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 */
	public function getListByTime($sDate, $eDate) {
		$sql = sprintf('SELECT * FROM %s WHERE dateline BETWEEN "%s" AND "%s"', $this->getTableName(), $sDate, $eDate);
		return $this->fetcthAll($sql);
	}
}