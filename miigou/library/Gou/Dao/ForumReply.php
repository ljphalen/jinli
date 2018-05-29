<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_ForumReply
 * @author tiansh
 *
 */
class Gou_Dao_ForumReply extends Common_Dao_Base{
	protected $_name = 'forum_reply';
	protected $_primary = 'id';
	
	/**
	 * delete by product_id
	 */
	public function deleteByForumId ($forum_id) {
		$sql = sprintf('DELETE FROM %s WHERE forum_id = %d', $this->getTableName(), intval($forum_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
}