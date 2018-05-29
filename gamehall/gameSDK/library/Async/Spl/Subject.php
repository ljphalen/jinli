<?php

abstract class Async_Spl_Subject implements SplSubject {
    private $observers = array();
    protected $logger;
    
    public function __construct() {
    	$this->logger = Logger_Game::getLogger(get_class($this));
        $this->observers = new SplObjectStorage();
    }
    
    public function attach(SplObserver $observer) {
        $this->observers->attach($observer); 
    }
    
    public function detach(SplObserver $observer) {
        $this->observers->detach($observer);
    }
    
    public function notify() {
        $args = func_get_args();
        $spl = $args[0];
        if(! $spl instanceof Async_Task_Message_Spl) {
            return ;
        }
        $subject = get_class($this);
    	$this->logger->info($spl->getParams());
        foreach ($this->observers as $observer) {
            $result = 0;
            $startTime = Async_Cache::getCurrentTime();
            try {
                $observer->update($this, $spl);
            } catch (Exception $e) {
                $result = -1;
                $this->logger->error(get_class($observer) . " update error", $e);
            }
            $endTime = Async_Cache::getCurrentTime();
            if($spl->getTaskId()) {
                $useTime = $endTime - $startTime;
                Async_Cache::saveTaskSPL($spl->getTaskId(), $subject, get_class($observer), $spl->getParams(), $result, $useTime);
            }
        }
    }
    
    abstract public function getName();

}
