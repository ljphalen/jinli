<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 异步任务
 * Resource_Dao_AsynTask
 * @author wupeng
 */
class Resource_Dao_AsynTask extends Common_Dao_Base {
	protected $_name = 'asyn_task';
	protected $_primary = 'id';
}