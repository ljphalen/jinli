<?php
/**
 * 日志文件写入类
 * @author jiangxinyu
 *
 * 调用示例代码： 
	 try { 
 		$log = Log::getInstance(array('logfile'=>'test.txt', 'loglevel'=>Log::DEBUG));
		$log->logMessage("this is a testing！", Log::DEBUG, 'myTest');
		$log->logFile('this is a testing!', 'text.txt'); 
	 } catch (Exception $e) { 
		echo $e->getMessage(); 
	 } 
 */

class Log{
	/**
	 * @var object 对象单例
	 */
	static private $_instance = null;
	
	/**
	 * @var array 日志文件写入配置数组
	 */
	private $_config = array(); 
	
	/**
	 * @var sourse  打开文件的资源
	 */
    private $_logfile;
    
    /**
     * @var int 写入日志类型
     */
    private $_loglevel;
	
    /**
     * @var int 调试类型
     */
    const DEBUG  = 100;
    
    /**
     * @var int 信息类型
     */
    const INFO   = 75;
    
    /**
     * @var int 提示错误类型
     */
    const NOTICE = 50;
    
    /**
     * @var int 警告错误类型
     */
    const WARNING =25;
    
    /**
     * @var int 错误类型
     */
    const ERROR   = 10;
    
    /**
     * @var int 危险的类型
     */
    const CRITICAL = 5;
    
    /**
     * 保证对象不被克隆
     */
    private function __clone(){}
    
	/**
	 * 构造函数
	 * @param array $config 日志配置文件， 格式为：array('logfile'=>'xxx', 'loglevel'=>xxx), 【logfile】为带路径的日志文件名， 【loglevel】为写入的等级
	 * @throws Exception
	 */
    private function __construct($config = array()){ 
    	$this->_config = $config;
        $this->_loglevel = isset($this->_config['loglevel']) ? $this->_config['loglevel'] : LOG::INFO;
        if(!isset($this->_config['logfile']) && strlen($this->_config['loglevel'])){
            throw new Exception('can\'t set file to empty');
        }
        $this->_logfile = @fopen($this->_config['logfile'], 'a+');        
        if(!is_resource($this->_logfile)){
            throw new Exception('invalid file Stream');
        }     
    }

    /**
     * 获得单例对象
     * @param array $config 日志文件配置
     * @return object
     */
    public static function getInstance($config = array()) {
    	if(!(self::$_instance instanceof self)) {
    		self::$_instance = new self( $config);
    	}
    	return self::$_instance;
    }
	
    /**
     * 按规定的格式写入日志消息
     * @param string $msg 日志的内容
     * @param int $loglevel 日志等级水平
     * @param string $module 写入的模块
     * @return boolean
     */
    public function logMessage($msg = '', $loglevel = Log::INFO, $module = null){       
        if($loglevel > $this->_loglevel){
            return false ;
        }
        @date_default_timezone_set('Asian/shanghai');
        $time = strftime('%x  %X',time());
        $msg = str_replace("\t",'',$msg);
        $msg = str_replace("\n",'',$msg);        
        $str_loglevel = $this->levelToString($loglevel);        
        if(isset($module)){
            $module = str_replace(array("\n", "\t"), array("", ""), $module);
        }
        $logline = "$time\t$msg\t$str_loglevel\t$module\r\n";                    //写入的格式   
        if ($this->_logfile) {
        	flock($this->_logfile, LOCK_EX);
        	fwrite($this->_logfile, $logline);
        	flock($this->_logfile, LOCK_UN);
        	fclose($this->_logfile);
        	return true;
        } else {
        	return false;
        }
    }
    
    /**
     * 自定义写日志文件， 格式自己在$msg定义
     * @param string $msg  内容消息
     * @param string $file    带路径的文件名
     * @return boolean
     */
    public function logFile($msg = '', $file = null) {   	
    	if(isset($file)) {
    		$fp = @fopen($file, 'a+');
    	} else {
    		$fp = $this->_logfile;
    	}
    	if ($fp) {
    		flock($fp, LOCK_EX);
    		fwrite($fp, $msg);
    		flock($fp, LOCK_UN);
    		fclose($fp);
    		return true;
    	} else {
    		return false;
    	}
    }
    	
    /**
     * 日志的等级类型转化成字符串
     * @param int $loglevel
     * @return string
     */
    private function levelToString($loglevel){
         $ret = '[unknow]';
         switch ($loglevel){
                case LOG::DEBUG:
                     $ret = 'LOG::DEBUG';
                     break;
                case LOG::INFO:
                     $ret = 'LOG::INFO';
                     break;
                case LOG::NOTICE:
                     $ret = 'LOG::NOTICE';
                     break;
                case LOG::WARNING:
                     $ret = 'LOG::WARNING';
                     break;
                case LOG::ERROR:
                     $ret = 'LOG::ERROR';
                     break;
                case LOG::CRITICAL:
                     $ret = 'LOG::CRITICAL';
                     break;
                default:
                	break;
         }
         return $ret;
    }
}
?>
