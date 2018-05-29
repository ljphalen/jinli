<?php class mysql implements db{private $sql;private $conn;public function __construct(){if(!extension_loaded('mysqli'))trigger_error('307|extension php_mysqli is required');}public function connect($config){$aes=new aes('szmykj@tonlo.com');$pwd=$aes->decrypt(base64_decode($config['pwd']));$conn=mysqli_connect($config['host'],$config['uid'],$pwd,$config['db'],$config['port']);if($conn){mysqli_set_charset($conn,str_replace('-','',CHARSET));$this->conn=$conn;}}public function query($sql,$param=array(),$dimension=2,$type=1){}public function select($table,$field,$where=array(),$order='',$offset='',$dimension=2,$type=1){}public function insert($table,$data,$auto=false){}public function update($table,$data,$where){}public function delete($table,$where){}public function execute($procedure,$param=array(),$dimension=2,$type=1){$sql_inout='';$sql_out='';$arrout=array();$result=array();$sql='CALL '.$procedure.'(';if($param&&is_array($param)){$str='';$len=count($param);for($i=0;$i<$len;$i++){$direction=intval($param[$i][1]);switch($direction){case 1:if(is_numeric($param[$i][0]))$str.=(','.$param[$i][0]);else $str.=(',\''.addslashes($param[$i][0]).'\'');break;case 2:$str.=',@out'.$i;$sql_inout.=('SET @out'.$i.'=\''.$param[$i][0].'\'');$sql_out.=(',@out'.$i);$arrout[]=$i;break;case 4:$str.=',@out'.$i;$sql_out.=(',@out'.$i);$arrout[]=$i;break;}}$str=substr($str,1);$sql_out=substr($sql_out,1);$sql.=$str;}$sql.=')';if($sql_out){$sql_out='SELECT '.$sql_out;$this->sql=$sql_inout;$stmt=mysqli_query($this->conn,$sql_inout);if($stmt===FALSE){trigger_error('300|'.$this->get_error().'.sql is:'.$this->sql);}}$this->sql=$sql;$stmt=mysqli_query($this->conn,$sql);if($stmt===FALSE){trigger_error('301|'.$this->get_error().'.sql is:'.$this->sql);}switch($dimension){case 0:$result=true;if(is_object($stmt))$this->clear_more_result();break;case 1:if(is_object($stmt)){$result=mysqli_fetch_array($stmt,$type);$this->clear_more_result();}break;case 2:if(is_object($stmt)){$result=mysqli_fetch_all($stmt,$type);$this->clear_more_result();}break;case 3:if(is_object($stmt))$result=$this->fetch_all_result($stmt,$type);break;}if(is_object($stmt))mysqli_free_result($stmt);if($sql_out){$stmt=mysqli_query($this->conn,$sql_out);if($stmt===FALSE){trigger_error('302|'.$this->get_error().'.sql is:'.$sql_out);}else{$output=mysqli_fetch_array($stmt,MYSQLI_NUM);if($output){for($i=0;$i<count($arrout);$i++){$param[$arrout[$i]][0]=$output[$i];}}}mysqli_free_result($stmt);}return $result;}private function fetch_all_result($stmt,$type){$result=array();$result[]=mysqli_fetch_all($stmt,$type);while(mysqli_more_results($this->conn)){mysqli_next_result($this->conn);if($stmt2=mysqli_store_result($this->conn)){$row=mysqli_fetch_all($stmt2,$type);array_push($result,$row);mysqli_free_result($stmt2);}}return $result;}private function clear_more_result(){while(mysqli_more_results($this->conn)){mysqli_next_result($this->conn);if($rs=mysqli_store_result($this->conn)){mysqli_free_result($rs);}}}private function get_error(){$errnum=mysqli_errno($this->conn);$errmsg=mysqli_error($this->conn);$result=$errnum.$errmsg;return $result;}public function get_sql(){return $this->sql;}public function get_version(){return mysqli_get_server_info($this->conn);}public function __destruct(){if($this->conn)mysqli_close($this->conn);}}?>