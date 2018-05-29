<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_ForumImg
 * @author tiansh
 *
 */
class Gou_Dao_ForumImg extends Common_Dao_Base{
	protected $_name = 'forum_img';
	protected $_primary = 'id';
	
	public function getImagesByForumIds ($forumids) {
		$sql = sprintf('SELECT * FROM %s WHERE forum_id in %s ORDER BY id DESC',$this->getTableName(), Db_Adapter_Pdo::quoteArray($forumids));
		return $this->fetcthAll($sql);
	}
	
	/**
	 * delete by product_id
	 */
	public function deleteByForumId ($forum_id) {
		$sql = sprintf('DELETE FROM %s WHERE forum_id = %d ', $this->getTableName(), intval($forum_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 * delete by product_id
	 */
	public function getImagesByForumId ($forum_id) {
		$sql = sprintf('SELECT * FROM %s WHERE forum_id = %d ORDER BY id ASC', $this->getTableName(), intval($forum_id));
		return $this->fetcthAll($sql);
	}
}