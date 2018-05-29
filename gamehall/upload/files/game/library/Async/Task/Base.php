<?php

abstract class Async_Task_Base {
    protected $logger;
    
    public function __construct() {
    	$this->logger = Logger_Game::getLogger(get_class($this));
    }
	
    /**
     * 同一时刻只有一个任务被执行（类名+方法+参数）
     */
	public function singleExecute() {
	    return false;
	}
    
}