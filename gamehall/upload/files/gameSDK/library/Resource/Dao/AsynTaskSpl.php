<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 异步观察者
 * Resource_Dao_AsynTaskSpl
 * @author wupeng
 */
class Resource_Dao_AsynTaskSpl extends Common_Dao_Base {
	protected $_name = 'asyn_task_spl';
	protected $_primary = 'id';
}