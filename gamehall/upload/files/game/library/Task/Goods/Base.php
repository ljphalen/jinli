<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-物品基类
 * @author fanch
 *
 */
abstract class Task_Goods_Base extends Util_LogForClass {

    protected  $mGood = array();

    public function __construct($good) {
        parent::__construct("task.log", get_class($this));
        $this->mGood = $good;
    }

	/**
	 * 物品赠送主体方法
	 * @param array $data
	 */
	abstract public function onSend();

}
