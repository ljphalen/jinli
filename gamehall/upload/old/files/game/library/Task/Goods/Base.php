<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-物品基类
 * @author fanch
 *
 */
abstract class Task_Goods_Base extends Util_LogForClass {
    
    public function __construct() {
        parent::__construct("task.log", get_class($this));
    }
    
	protected  $mGood = array();

	/**
	 * 设置需要赠送的物品
	 * @param array $good
	 */
	public function setGood($good){
		$this->mGood = $good;
	}
	
	/**
	 * 物品赠送主体方法
	 * @param array $data
	 */
	abstract public function onSend();

}
