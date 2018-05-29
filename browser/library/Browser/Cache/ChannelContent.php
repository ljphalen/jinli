<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_ChannelContent
 * @author tiansh
 *
 */
class Browser_Cache_ChannelContent extends Cache_Base{
	public $expire = 60;
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public function getListByChannelIds($channelids) {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
}