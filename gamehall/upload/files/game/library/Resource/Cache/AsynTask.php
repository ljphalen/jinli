<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 异步任务
 * Resource_Cache_AsynTask
 * @author wupeng
 */
class Resource_Cache_AsynTask extends Cache_Base {
	public $expire = 600;
}
