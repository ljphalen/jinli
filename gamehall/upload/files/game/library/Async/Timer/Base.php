<?php

abstract class Async_Timer_Base {
    protected $logger;
    
	public function __construct() {
    	$this->logger = Logger_Game::getLogger(get_class($this));
	}

	abstract public function heartbeat($hour, $minute, $second);
    
}