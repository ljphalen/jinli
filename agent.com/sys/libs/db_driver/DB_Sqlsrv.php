<?php
/**
 * SQL数据库基本操作, 使用sqlsvr扩展
 * @author jaingxinyu
 */
require_once 'DB_Base.php';

class DB_Sqlsrv extends DB_Base  {
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
	 * 获取单例对象
	 * @param array $config
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
    	//连接信息
    	$connectionInfo = array('UID'=>$this->_config['username'], 'PWD'=>$this->_config['password'], 'Database'=>$this->_config['database'], 'CharacterSet'=>$this->_config['charset'], 'MultipleActiveResultSets'=>true);   	
		$this->conn = sqlsrv_connect($this->_config['hostname'], $connectionInfo);
    }
		
	/**
	 * 选择数据库
	 * @param string $database 数据库名
	 */
	public function selectDb($database = '') {
		$this->_config['database'] = $database;
		$this->conn = null;
		$this->connect();
	}
	
	/**
	 * 执行sql语句, 一般用来执行查询语句
	 * @param string $sql
	 * @param array $params
	 * @param array $options
	 * @return object|boolean
	 */  
	public function query($sql = '', $params = array(), $options = array()){
		$this->sql = $sql;
		if(!empty($params) || !empty($options)) {
			$result =  sqlsrv_query($this->conn, $sql, $params, $options);
		} else {
			$result =  sqlsrv_query($this->conn, $sql);
		}
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
	 * 一般用来执行 insert update delete
	 * @param string $sql
	 * @param array $params
	 * @return Ambigous <object, boolean, unknown>
	 */
	public function execute($sql = '', $params = false) {
		if($params){
			$this->query = sqlsrv_prepare( $this->conn, $sql, $params);
			return sqlsrv_execute($this->query);
		} else {
			return $this->query($sql);
		}
	}
	
	/**
	 * 返回查询的条数
	 * @param object $query
	 * @return number
	 */
	public function numRows($query = null){		
		if($query == null) {
			$params = array();
			$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
			$stmt = $this->query($this->sql , $params, $options );
			$count =  sqlsrv_num_rows($this->query);
		}else{
			$count =  sqlsrv_num_rows($query);
		}
		if($count) {
			return $count;
		} else {
			$this->errorMsg('back is valid!');
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
	 * @return object
	 */
	public function fetchArray($query = null, $result_type = SQLSRV_FETCH_ASSOC) {	
		if($query == null) {
			$query = $this->query;
		}	;
		return sqlsrv_fetch_array($query, $result_type);
	}
 
	/**
	 * 获取查询的结果集
	 * @param object $query
	 * @return array
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
	 * 释放结果集
	 */
	public function freeResult($query = null) {
		if($query) {
			sqlsrv_free_stmt($query);
		} else {
			sqlsrv_free_stmt($this->query);
		}		
	}
	
	/**
	 * 取得结果集中字段的数目
	 * @param object $query
	 * @return int
	 */
	public function numFields($query = null) {
		if($query == null) {
			return sqlsrv_num_fields($this->query);
		} else {
			return sqlsrv_num_fields($query);
		}
	}
	
	/**
	 * 返回插入数据的当前id
	 * @return int
	 */
	public function insertId() {
		$rs = $this->query("SELECT @@identity AS id");
		$result = $this->fetchRow($rs);
		$this->freeResult($rs);
		return $result['id'];	
	}
	
	/**
	 * 下一个结果集
	 * @param object $query
	 */
	function nextResult($query = null){
		if($query) {
			return sqlsrv_next_result($query);
		} else {
			return sqlsrv_next_result($this->query);
		}
	}
	
	/**
	 * 返回受影响行数
	 * @return int
	 */
	public function affectedRows($query = null) {
		if($query) {
			return sqlsrv_rows_affected($query);
		}else {
			return sqlsrv_rows_affected($this->query);
		}
	}
	
	/**
	 * 开始事务
	 */
	public function startCommit() {
		return sqlsrv_begin_transaction($this->conn);		
	}
	
	/**
	 * 提交事务
	 */
	public function commit(){
		return sqlsrv_commit($this->conn);
	}
	
	/**
	 * 回滚事务
	 */
	public function rollback(){
		return sqlsrv_rollback($this->conn);
	}
	
	/*
	 * 自动组装语句并执行
	* @param string $tname 表明
	* @param string $type 语句类型 可为:insert, update, delete
	* @param array $data='' 数据 字段名为键的数组
	* @param string $condition
	*/
	public function autoExecute($tname, $type, $data='', $condition=''){
		if( 'insert'==$type || 'update'==$type ){
			//字段名
			$filed = array_keys($data);
			//字段值
			$values = array_values($data);
			//字段值转换为引用值
			$refVal =array();				
			$len = count($filed);			
			//准备引用值
			for($i=0; $i<$len; $i++){
				$refVal[]= &$values[$i];
			}
			//插入串
			if( 'insert'==$type ){
				$prepareStr = '';
				for($i=0; $i<$len; $i++){
					$prepareStr.='?,';
				}
				$prepareStr = substr($prepareStr, 0 , strlen($prepareStr)-1);
			}
		}
		switch( $type ){
			case 'insert':
				{
					$sql = "INSERT INTO $tname(".implode(',', $filed).") VALUES($prepareStr)";
					return $this->query( $sql, $refVal );
				}
				break;
			case 'update':
				{
					$sql = "UPDATE $tname SET ".implode('=?,', $filed)."=? WHERE $condition";
	
					return $this->query( $sql, $refVal );
				}
				break;
			case 'delete':
				{
					$sql = "DELETE FROM $tname WHERE $condition";
					return $this->query( $sql );
				}
				break;
		}
	}
	
	/**
	 * 公用存储过程调用接口(只适用完全入参，取得返回值)
	 * @param string $sp
	 * @param array $data
	 * @param int $type
	 * @return Ambigous <string, multitype:NULL >
	 */
	public function mssqlFetch($sp = '' , $data = array() ,$type = 1){	
		foreach ($data as $key => $var){
			$params[]= array(&$data[$key],SQLSRV_PARAM_IN);
		}
		$res = '';
		switch ($type){
			case 1:
				$res = $this->execSp($sp, $params);
				break;
			case 2:
				$res = $this->execSpGetAllRecord($sp, $params);
				break;
			case 3:
				$res = $this->execSpGetRecord($sp, $params);
				break;
			case 4:
				$res = $this->execSpGetAllRS($sp, $params);
				break;
			default:
				break;
		}
		return $res;
	}
	
	//参数需要以如下数组方式赋值并标明类型，SQLSRV_PARAM_IN是输入类型，SQLSRV_PARAM_OUT是输出类型。
	//注意要按照存储过程定义的顺序赋值
	/**
	 * 执行存储过程,返回执行结果
	 * @param string $sp 存储过程 ,sp_InsertGeneralizeReport(?,?)
	 * @param array $params 参数数组,
	 * 		格式如: $params = array(
		 array(&$ConsumeValue, SQLSRV_PARAM_IN),
		 array(&$ReportDate, SQLSRV_PARAM_IN)
	 );
	 */
	public function execSp($sp = '', $params = array()){
		$callSp = '{call '.$sp.'}';
		$stmt = sqlsrv_query($this->conn, $callSp, $params);
		if($stmt === false){	
			$this->errorMsg("Error in executing statement 1.<br>". $sp."<br>".print_r($params, true));
		}else{
			return $stmt;
		}
	}
	
	/**
	 * 执行存储过程，返回一行数据
	 * @param string $sp
	 * @param array $params
	 */
	public function execSpGetRecord($sp = '', $params = array()){
		$stmt = $this->execSp($sp, $params);
		if($stmt === false){
			$this->errorMsg("Error in executing statement 2.<br>". $sp."<br>".print_r($params, true));
		}else{
			return $this->fetchArray($stmt);
		}
	}
	
	/**
	 * 执行存储过程，返回所有数据
	 * @param string $sp
	 * @param array $params
	 */
	public function execSpGetAllRecord($sp = '', $params = array()){
		$stmt = $this->execSp($sp,$params);
		if($stmt === false){
			$this->errorMsg("Error in executing statement 3.<br>". $sp."<br>".print_r($params, true));
		}else{
			return $this->getArray($stmt);
		}
	}
	
	/**
	 * 执行存储过程，返回所有记录集数据
	 * @param string $sp
	 * @param array $params
	 */
	public function execSpGetAllRS($sp = '', $params = array()){
		$arr = array();
		$stmt = $this->execSp($sp,$params);
		if($stmt === false){
			$this->errorMsg("Error in executing statement 4.<br>". $sp."<br>".print_r($params, true));
		} else {
			$arr[0] = $this->getArray($stmt);
			$rs = sqlsrv_next_result($stmt);
			$i = 1;
			while($rs){
				$arr[$i] = $this->getArray($stmt);
				$i++;
				$rs = sqlsrv_next_result($stmt);
			}
			return $arr;
		}
	}
	
	/**
	 * 关闭连接
	 */
	public function close() {
			@sqlsrv_close($this->conn);
	}

	/**
	 * 输出错误信息
	 * @param string $msg
	 * @param string $error
	 * @param int $errorno
	 */
	public function errorMsg($msg = '', $error = ''){
		$error     = !empty($error) ? $error : sqlsrv_errors();
		//输出错误信息
		$str = __CLASS__." {$msg}<br>
		<b>SQL</b>: {$this->sql}<br>
		<b>Error details</b>: {$error}<br>";
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