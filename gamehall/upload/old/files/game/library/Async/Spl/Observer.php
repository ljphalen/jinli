<?php

abstract class Async_Spl_Observer implements SplObserver {
    protected $logger;
    
    public function __construct() {
    	$this->logger = Logger_Game::getLogger(get_class($this));
    }
    
    public function update(SplSubject $subject) {
        $args = func_get_args();
        $spl = $args[0];
        if(! $spl instanceof Async_Task_Message_Spl) {
            return ;
        }
        $this->doUpdate($subject, $spl);
    }
    
    abstract public function doUpdate(SplSubject $subject, Async_Task_Message_Spl $spl);
    
    abstract public function getSubjectList();
    
}

