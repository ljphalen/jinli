<?php
/**
 * mysql数据库基本操作
 * @author jaingxinyu
 *
 */
require_once 'DB_Base.php';

class DB_Mysqli extends DB_Base {
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
    public  function connect(){
		$this->conn = mysqli_connect($this->_config['hostname'],$this->_config['username'],$this->_config['password'],$this->_config['database'],$this->_config['port']) or die("Sorry,can not connect to database");
		//$this->selectDb($this->_config['database']);
		$this->query("set names '".$this->_config['charset']."'");
	}
	
	/**
	 * 选择数据库
	 * @param string $database
	 */
	public function selectDb($database = '') {
		if(!empty($database) && is_string($database)) {
			mysqli_select_db($this->conn, $database);
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
    	$result = mysqli_query($this->conn, $sql);  
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
			return mysqli_num_rows($this->query);
		} else {
			return mysqli_num_rows($query);
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
			return mysqli_fetch_assoc($query);
		} elseif ($result_type=='MYSQL_NUM') {
			return mysqli_fetch_row($query);
		} else {
			return mysqli_fetch_array($query);
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
		return mysqli_escape_string($this->conn, $str);
	}
	
	/**
	 * 释放结果集
	 * @return boolean
	 */
	public function freeResult($query = null) {
		return mysqli_free_result($query);
	}
	
	/**
	 * 取得结果集中字段的数目
	 * @param object $query
	 * @return int
	 */
	public function numFields($query = null) {
		if($query == null) {
			return mysqli_num_fields($this->query);
		} else {
			return mysqli_num_fields($query);
		}
	}
	
	/**
	 * 返回插入数据的当前id
	 * @return int
	 */
	public function insertId() {
		return mysqli_insert_id($this->conn);
	}
	
	/**
	 * 返回受影响行数
	 * @return int
	 */
	public function affectedRows() {
		return mysqli_affected_rows($this->conn);
	}
	
	/**
	 * 开始事务
	 */
	public function startCommit() {
		$this->query("start transaction;");		
	}
	
	/**
	 * 提交事务
	 */
	public function commit(){
		$this->query("commit;");
	}
	
	/**
	 * 回滚事务
	 */
	public function rollback(){
		$this->query("rollback;");
	}
	
	/**
	 * 执行存储过程，返回多个结果
	 * @param string $sql
	 * @return boolean|multitype:multitype:
	 */
	public function execProc($sql){
		$rows = array ();	
		if( $this->conn->real_query( $sql ) ){
			$i=0;
			do{
				if($result = $this->conn->store_result()){
					$rows[$i] = array();
					while( $row = $result->fetch_assoc() ){
						array_push($rows[$i], $row);
					}
					$result->close();
				}
				$i++;
			}while(@$this->conn->next_result());
			 
			if(count($rows)==0){
				return true;
			}
			return $rows;
		}else{
			return false;
		}	
	}
	
	/**
	 * 执行简单存储过程
	 * @param string $proName
	 * @param array  $params
	 * @param mix $returnval 返回值（如需返回值请设置此参数，请勿放入$params，存储过程会报错）
	 */
	public function simpleCall ($proName, $params = '', $returnval = ''){
		$sql = 'CALL ' . $proName . '(';
		if (! $proName) {
			return - 1; //返回-1 ，没有传入存储过程名
		}
		if (is_array($params)) {
			//array_walk($params, array($this,"db_escape_filter")); //字符自动加入单引号
			if(is_array($params)){
				foreach($params as $k=>$v){
					$params[$k]="'".$v."'";
				}
			}
			$sql .= implode(',', $params);
		}
		if (is_array($returnval)) {
			$sql .= implode(',', $returnval);
		} elseif ($returnval != '') {
			$sql .= ',' . $returnval;
		}
		$sql .= ')';
		//echo $sql;exit;
		return $this->execProc($sql);
	}
	
	/**
	 * 关闭连接
	 * @see DB_Base::close()
	 */
	public function close() {
			@mysqli_close($this->conn);
	}

	/**
	 * 输出错误信息
	 * @param string $msg
	 * @param string $error
	 * @param int $errorno
	 */
	public function errorMsg($msg = '', $error = '', $errorno = ''){
		$error     = !empty($error) ? $error : mysqli_error($this->conn);
		$errorno = !empty($errorno) ? $errorno : mysqli_errno($this->conn);
		//输出错误信息
		$str = __CLASS__." {$msg}<br>
		<b>SQL</b>: {$this->sql}<br>
		<b>错误详情</b>: {$error}<br>
		<b>错误代码</b>:{$errorno}<br>";
		echo $str;
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		if($this->conn) {
			$this->close();
		}
	}
}

?>