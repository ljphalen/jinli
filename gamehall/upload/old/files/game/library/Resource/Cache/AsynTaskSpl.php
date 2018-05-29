<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 异步观察者
 * Resource_Cache_AsynTaskSpl
 * @author wupeng
 */
class Resource_Cache_AsynTaskSpl extends Cache_Base {
	public $expire = 600;
}
