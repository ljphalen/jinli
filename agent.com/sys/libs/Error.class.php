<?php
//cp异常和错误处理
if(DEBUG){	
	 //默认异常处理函数
	function cp_exception_handler(Exception $e) {  
		 new Error( $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine() );
	}

	//默认错误处理函数
	function cp_error_handler($errorCode, $errorMessage, $errorFile, $errorLine) {  
		 new Error($errorMessage, $errorCode, $errorFile, $errorLine);
	}
	
	set_exception_handler('cp_exception_handler');//注册默认异常处理函数
	set_error_handler('cp_error_handler', E_ALL ^ E_NOTICE);//注册默认错误处理函数
}	

//cp错误类
class Error extends Exception {

    private $errorMessage = '';
    private $errorFile = '';
    private $errorLine = 0;
    private $errorCode = '';
	private $errorLevel = '';
 	private $trace = '';
    /**
     * 构造函数
     * @param string $errorMessage 提示信息
     * @param int $errorCode 提示代号
     * @param string $errorFile 出错的文件名
     * @param int $errorLine 出错的行号
     */
    public function __construct($errorMessage, $errorCode = 0, $errorFile = '', $errorLine = 0) {
        parent::__construct($errorMessage, $errorCode);
        $this->errorMessage = $errorMessage;
		$this->errorCode = $errorCode == 0?$this->getCode() : $errorCode;
        $this->errorFile = $errorFile == ''?$this->getFile() : $errorFile;
        $this->errorLine = $errorLine == 0?$this->getLine() : $errorLine;
		
      	$this->errorLevel = $this->getLevel();
 	    $this->trace = $this->trace();
        $this->showError();
    }
	
	//获取trace信息
	public function trace() {
        $trace = $this->getTrace();

        $traceInfo='';
        $time = date("Y-m-d H:i:s");
        foreach($trace as $t) {
        	$class = !empty($t['class'])?$t['class']:'';
        	$type = !empty($t['type'])?$t['type']:'';
            $traceInfo .= '['.$time.'] ' . $t['file'] . ' (' . $t['line'] . ') ';
            $traceInfo .= $class . $type . $t['function'] . '(';
           // $traceInfo .= implode(', ', $t['args']);
            $traceInfo .= ")<br />\r\n";

        }
		return $traceInfo ;
    }
	
	//错误等级
	public function getLevel() {
	  $Level_array = array(	1=> '致命错误(E_ERROR)',
			2 => '警告(E_WARNING)',
			4 => '语法解析错误(E_PARSE)',  
			8 => '提示(E_NOTICE)',  
			16 => 'E_CORE_ERROR',  
			32 => 'E_CORE_WARNING',  
			64 => '编译错误(E_COMPILE_ERROR)', 
			128 => '编译警告(E_COMPILE_WARNING)',  
			256 => '致命错误(E_USER_ERROR)',  
			512 => '警告(E_USER_WARNING)', 
			1024 => '提示(E_USER_NOTICE)',  
			2047 => 'E_ALL', 
			2048 => 'E_STRICT'
		 );
		return isset( $Level_array[$this->errorCode] ) ? $Level_array[$this->errorCode] : $this->errorCode;
	}
	
	//抛出错误信息，用于外部调用
     static public function show($message="") {
		 new Error($message);
    }
		
	//记录错误信息
	static public function write($message){		
		
		$log_path = LOG_PATH;
		//检查日志记录目录是否存在
		 if( !is_dir($log_path) ) {
			//创建日志记录目录
			@mkdir($log_path,0777,true);
		 }
		 $time=date('Y-m-d H:i:s');
		 $ip= function_exists('get_client_ip') ? get_client_ip() : $_SERVER['REMOTE_ADDR'];
		 $destination =$log_path  . date("Y-m-d") . ".log";
		 //写入文件，记录错误信息
       	 @error_log("{$time} | {$ip} | {$_SERVER['PHP_SELF']} |{$message}\r\n", 3,$destination);
	}
	
	//输出错误信息
     public function showError(){
		//如果开启了日志记录，则写入日志
		if( LOG_ON ) {
			self::write($this->message);
		}
		$error_url = ERROR_URL;
		//错误页面重定向
		if($error_url != '')
		{
		 echo '<script language="javascript">
			   if(self!=top){
				  parent.location.href="'.$error_url.'";
		      } else {
			 	 window.location.href="'.$error_url.'";
			  }
			  </script>';
			exit;
		}
		
		if( !defined('__APP__') ){	
			define( '__APP__' , '/');
		}
		echo 
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统错误提示!</title>
</head>
<body>
	<div style="border:5px solid #ddd; margin:20px auto; width:800px;">
	<div style=" padding:15px; background:#f5f5f5;">
	<div style="border-bottom:1px #ddd solid; font-size:26px;font-family: "Microsoft Yahei", Verdana, arial, sans-serif; line-height:40px; height:40px; font-weight:bold">系统提示</div>
	<div style="height:20px; border-top:1px solid #fff"></div>
	<div style="border:1px dotted #ccc; border-left:6px solid #ddd; padding:15px; background:#f3f3f3">
		'.$this->message.'
	</div>';
	
	//开启调试模式之后，显示详细信息
	if( $this->errorCode && cpConfig::get('DEBUG') ) {
	 echo  '<div style="border:1px dotted #ccc; border-left:6px solid #ddd; padding:15px; background:#f3f3f3">
			出错文件：'.$this->errorFile.'
		</div>
		<div style="border:1px dotted #ccc; border-left:6px solid #ddd; padding:15px; background:#f3f3f3">
			错误行：'.$this->errorLine.'
		</div>
		<div style="border:1px dotted #ccc; border-left:6px solid #ddd; padding:15px; background:#f3f3f3">
			错误级别：'.$this->errorLevel.'
		</div>
		<div style="border:1px dotted #ccc; border-left:6px solid #ddd; padding:15px; background:#f3f3f3;line-height:20px;">
			Trace信息：<br>'.$this->trace.'
		</div>';
	}
	
echo '<div style="height:20px;"></div>
<div style=" font-size:15px;">您可以选择 &nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'" title="重试">重试</a> &nbsp;&nbsp;<a href="javascript:history.back()" title="返回">返回</a>  或者  &nbsp;&nbsp;<a href="index.php" title="回到首页">回到首页</a></div>
</div>
</div>
</body>
</html>';
		exit;
    }
}
?>