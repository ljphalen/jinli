<?php
class CliBaseAction extends Action
{
    function _initialize()
    {
    	set_time_limit(0);
    	ini_set('memory_limit', '-1');
    	
    	if(PHP_SAPI !== 'cli')
    	{
    		header('Location: '.U("@www"));
    		exit;
    	}
    	
    }
    
    //Cli 清屏处理
    protected function clear($out = TRUE)
    {
    	$clearscreen = chr ( 27 ) . "[H" . chr ( 27 ) . "[2J";
    	if ($out)
    		print $clearscreen;
    	else
    		return $clearscreen;
    }
    
    function printf()
    {
    	$args = func_get_args();
    	if(count($args) == 1){
    		$msg = $args[0].PHP_EOL;
    	}else{
    		$args[0] = $args[0].PHP_EOL;
    		$msg = call_user_func_array("sprintf", $args);
    	}
    	
    	echo $msg;
    	Log::write(trim($msg), Log::EMERG);
    }
}