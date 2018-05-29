<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Activity_Cache_UserData
 * @author zhengzw
 *
 */
/*
 * 目前我们的缓存更新粒度为“表”，而这个表的缓存粒度应该控制在一条数据才有效，
 * 这说明我们的redis缓存依然太粗糙了，需要改进。
 */ 
/* 
class Activity_Cache_UserData extends Cache_Base{
	public $expire = 3600;
}
*/
