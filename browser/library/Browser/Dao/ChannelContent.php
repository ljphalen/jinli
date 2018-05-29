<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_ChannelContent
 * @author tiansh
 *
 */
class Browser_Dao_ChannelContent extends Common_Dao_Base{
	protected $_name = 'browser_channel_content';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public function getListByChannelIds($channelids) {
		$time = Common::getTime();
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time <= %d AND end_time > %d AND channel_id IN %s ORDER BY sort DESC, id DESC', $this->_name, $time, $time, Db_Adapter_Pdo::quoteArray($channelids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}