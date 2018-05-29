<?php
/**
 * mysql数据库基本操作
 * @author jaingxinyu
 *
 */
require_once 'DB_Base.php';

class DB_Mssql extends DB_Base {
    /**
     * @var object 对象单例
     */         
    static private $_instance = null;
    
    /**
     * @var array 数据库连接配置参数
     */         
    private $_config  = array();
    
    /**
     * @var object 数据库连接对象
     */
    public $conn = null; 
    
    /**
     * @var string 执行的sql语句
     */
    private $sql = '';
    
    /**
     * @var object 执行query返回的对象
     */
    private $query = null;
    
    /** 
	 * 保证对象不被clone 
	 */
    private function __clone() {}
    
	/**
	 * 构造函数
	 * @param array $config  数据库连接配置
	 */
    private function  __construct($config = array()) {
        $this->_config = $config;
        $this->connect();
    }
    
    /**
     * 获得单例对象
     * @param array $config 数据库连接配置
     * @return object
     */
    public static function getInstance($config = array()) {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self( $config);
        }
        return self::$_instance;
    }
    
	/**
	 * 数据库连接
	 */       
    public function connect(){
		$this->conn = mssql_connect($this->_config['hostname'],$this->_config['username'],$this->_config['password']) or die("Sorry,can not connect to database");
		$this->selectDb($this->_config['database']);
		$this->query("set names '".$this->_config['charset']."'");
	}
	
	/**
	 * 选择数据库
	 * @param string $database
	 */
	public function selectDb($database = '') {
		if(!empty($database) && is_string($database)) {
			mssql_select_db($database, $this->conn);
			$this->_config['database'] = $database;
		} else {
			$this->errorMsg('database name is valid');
		}
	}
    
	/**
	 * 执行sql语句
	 * @param string $sql
	 * @return object|boolean
	 */          
    public function query($sql = ''){
    	$this->sql = $sql;	 
    	$result = mssql_query($sql, $this->conn);  
		if($result) {
			$this->query = $result;
		    return $result;
		} else {
			//这里是显示SQL语句的错误信息，主要是设计阶段用于提示。正式运行阶段可将下面这句注释掉。
			$this->errorMsg('SQL is valid');
			return false;
		}
	}
	
	/**
	 * 功能和query是一样的， 只是换一下名字而已
	 * @param string $sql
	 * @return Ambigous <boolean, resource>
	 */
	public function execute($sql = '') {
		return $this->query($sql);
	}
	
	/**
	 * 返回查询的条数
	 * @param object $query
	 * @return number
	 */
	public function numRows($query = null){		
		if($query == null) {
			return mssql_num_rows($this->query);
		} else {
			return mssql_num_rows($query);
		}
	}
	
	/**
	 * 返回查询的条数, 功能和query是一样的，只是根据习惯换一下名字
	 * @param object $query
	 * @return number
	 */
	public function recordCount($query = null) {
		return $this->numRows($query);
	}
	
	/**
	 * 获取记录
	 * @param object $query
	 * @param string $result_type
	 * @return multitype:
	 */
	public function fetchArray($query = null, $result_type = '') {	
		if($query == null) {
			$query = $this->query;
		}	
		if ($result_type=='MYSQL_ASSOC') {
			return mssql_fetch_assoc($query);
		} elseif ($result_type=='MYSQL_NUM') {
			return mssql_fetch_row($query);
		} else {
			return mssql_fetch_array($query);
		}	
	}
 
	/**
	 * 获取查询的结果集
	 * @param object $query
	 * @return multitype:Ambigous <multitype:, multitype:>
	 */
	public function getArray($query = null){
		$data = array();
		while($row = $this->fetchArray($query)){
			$data[] = $row;
		}
		return $data;
	}
		
	/**
	 * 返回一条查询结果
	 * @param object $query
	 * @return Ambigous <multitype:, multitype:>
	 */
	public function fetchRow($query = null){
		return $this->fetchArray($query);
	}
	
	/**
	 * 过滤查询字符串， 用户数据库安全
	 * @param string $str
	 * @return string
	 */
	public function escape($str = '') {
		//return mysql_escape_string($str);
	}
	
	/**
	 * 释放结果集
	 * @return boolean
	 */
	public function freeResult() {
		foreach (func_get_args() as $rs) {
			if (is_resource($rs) && get_resource_type($rs) === 'mysql result'){
				return mssql_free_result($rs);
			}
		}
	}
	
	/**
	 * 取得结果集中字段的数目
	 * @param object $query
	 * @return int
	 */
	public function numFields($query = null) {
		if($query == null) {
			return mssql_num_fields($this->query);
		} else {
			return mssql_num_fields($query);
		}
	}
	
	/**
	 * 返回插入数据的当前id
	 * @return int
	 */
	public function insertId() {
		$id = "";
		$rs = $this->query("SELECT @@identity AS id");
		if ($row = mssql_fetch_row($rs)) {
			$id = trim($row[0]);
		}
		$this->freeResult($rs);
		return $id;	
	}
	
	/**
	 * 返回受影响行数
	 * @return int
	 */
	public function affectedRows() {
		return mssql_rows_affected();
	}
	
	/**
	 * 开始事务
	 */
	public function startCommit() {
		$this->query("BEGIN TRANSACTION");		
	}
	
	/**
	 * 提交事务
	 */
	public function commit(){
		$this->query("COMMIT");
	}
	
	/**
	 * 回滚事务
	 */
	public function rollback(){
		$this->query("ROLLBACK");
	}
	
	/**
	 * 关闭连接
	 * @see DB_Base::close()
	 */
	public function close() {
			@mssql_close($this->conn);
	}

	/**
	 * 输出错误信息
	 * @param string $msg
	 * @param string $error
	 * @param int $errorno
	 */
	public function errorMsg($msg = ''){
		//输出错误信息
		$str = __CLASS__." {$msg}<br>
		<b>SQL</b>: {$this->sql}<br>";
		echo $str;
	}
	
	/**
	 * 回收资源
	 */
	public function __destruct() {
		if($this->conn) {
			$this->close();
		}
	}
}

?>