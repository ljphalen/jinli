<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Admin_Dao_Weixinuser
 * @author wupeng
 *
 */
class Admin_Dao_Weixinuser extends Common_Dao_Base {
	protected $_name = 'weixin_user';
	
	/**
	 * 获取没有下载头像的用户列表
	 */
	public function getUnloadImgList($start = 0, $limit = 20, array $orderBy = array()) {
	    $where = 'headimgurl LIKE "%/0"';
	    $sort = Db_Adapter_Pdo::sqlSort($orderBy);
	    $sql = sprintf('SELECT id,headimgurl FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
	    return Db_Adapter_Pdo::fetchAll($sql);
	}
}