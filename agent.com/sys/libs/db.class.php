<?php
 /** 
 * DB功能工厂方法类 ,实现mysql，mysqli，mssql, sqlsrv 等php扩展操作支持
 * @author jaingxinyu 
 * 调用示例代码： 
		 $db = DB::factory($config); 
	$config格式为：
		 $config = array(
			'hostname'=>'localhost',
			'username'=>'root',
			'password'=>'123456,
			'database'=>'test',
			'charset'=>'utf8',
			'type' =>'mysql'
	); 
 */
 
class  db {	
	/**
	 * 数据类型
	 * @var array
	 */
	private static  $_type = array(
				'mysql'  =>'DB_Mysql',
				'mysqli' => 'DB_Mysqli',
				'mssql'  => 'DB_Mssql',
				'sqlsrv'  => 'DB_Sqlsrv'
			); 
	   
    /** 
	 * 保证对象不被clone 
	 */
    private function __clone() {
    }
    
    /** 
	 * 构造函数 
	 */
	private function __construct() {
	}

    /**
     * DB工厂操作方法
     * @param string $db_dirver 操作数据库引擎
     * @param array $config 数据库连接配置参数          
     */         
    public static function factory($config = array()) {
        $path = dirname(__FILE__);   //绝对路径
        if(array_key_exists($config['type'], self::$_type)) {
	        $classname = self::$_type[$config['type']];
	        if(isset($classname) && file_exists($path.'/db_driver/'.$classname.'.php')) { 	        	
		        require $path.'/db_driver/'.$classname.'.php';
		        $db_obj = $classname::getInstance($config);
		        return $db_obj;
	        }
        }
        echo 'Error:'.__CLASS__.'database access type '.$classname.' is not support ';        
    }
}
?>