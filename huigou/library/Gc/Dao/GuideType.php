<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Gc_Dao_GuideType extends Common_Dao_Base{
	protected $_name = 'gc_guide_type';
	protected $_primary = 'id';
	
	
	/**
	 *
	 * 
     *所有导购分类列表
	 */
	public function getAllGuideTypeSort() {
		$sql = sprintf('SELECT * FROM %s ORDER BY sort DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 取出所有导购分类列表数量
	 * 
	 */
	public function getAllGuideTypeSortCount() {
		$sql = sprintf('SELECT COUNT(*) FROM %s ORDER BY sort DESC',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
