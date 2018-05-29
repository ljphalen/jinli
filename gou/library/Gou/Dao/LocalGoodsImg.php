<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_LocalGoodsImg
 * @author tiansh
 *
 */
class Gou_Dao_LocalGoodsImg extends Common_Dao_Base{
	protected $_name = 'gou_local_goods_img';
	protected $_primary = 'id';
	
	public function getImagesByGoodsIds ($goodsids) {
		$sql = sprintf('SELECT * FROM %s WHERE goods_id in %s ORDER BY id DESC',$this->getTableName(), Db_Adapter_Pdo::quoteArray($goodsids));
		return $this->fetcthAll($sql);
	}
	
	/**
	 * delete by product_id
	 */	
	public function deleteByGoodsId ($goods_id) {
		$sql = sprintf('DELETE FROM %s WHERE goods_id = %d', $this->getTableName(), intval($goods_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 * delete by product_id
	 */
	public function getImagesByGoodsId ($goods_id) {
		$sql = sprintf('SELECT * FROM %s WHERE goods_id = %d ORDER BY id ASC', $this->getTableName(), intval($goods_id));
		return $this->fetcthAll($sql);
	}
}