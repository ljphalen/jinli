
<?php
/**
 * redis 队列服务
*/
@include_once(dirname(__FILE__)."/redisQconfig.php");
if (!class_exists('redisQconfig')){
	throw new RuntimeException;
}
class redisQ{
	/**
	 * 写入redis 队列
	 */
	private static $_instance;
	private $config =null;
	private $queueConfig = null;
	private static $queueName =null;
	private $connected = false;
	private $redis = null;
	private $lastError ='';
	private $consumerGuid='';
	private function __construct(){//私有构造方法防止类被直接创建
	}

	/**
	 * 取得redisQ的实例
	 * @param $queueName
	 * @eenurn mixed
	 * @throws redisQparameterError
	 */
	public static function getInstance($queueName){
		
		if(!isset(self::$_instance))
		{
			
			if (!isset($queueName)||$queueName==''){
				throw new redisQparameterError();//参数错误
			}
			self::$queueName = 'RQ:' . $queueName;
			$class=__CLASS__;
			self::$_instance=new $class();
			self::$_instance->init();
						
			return self::$_instance;
		}
		return self::$_instance;
	}

	/**
	 * 防止类的复制
	 */
	public function __clone(){
		trigger_error('Clone is not allow' ,E_USER_ERROR);
		throw new redisQException;
	}
	/**
	 * 初始化
	 * @return array|bool
	 */
	private function init(){
		$this->connected=false;
		$this->loadConfig();
		
		$connect=false;
		$queueName = $this->getQueueKeyName();
		if (!in_array($queueName,$this->config->queues)){
			return $this->returnError(3);
		}
		try{	
			$connect= $this->connectRedis();	
		}catch (Exception $e){
			
			return $this->returnError(2);
		}
		if ($connect===false){
			return $this->returnError(2);
		}
		return true;
	}

	public function getQueueKeyName(){
		$queueName = substr(self::$queueName,3,strlen(self::$queueName));
		return $queueName;
	}
	private function setLastError($errorStatus,$errorMessage=""){
		$this->lastError = $this->config->statusConfig[$errorStatus] . $errorMessage;
	}

	/**
	 * 长连接存在一定bug 在shell 产生notice之后进程会僵死，无法继续生存
	 *
	 * 使用短连接后，在一定时间内，链接可以复用 ，直到 空闲时间达到设定的值
	 * 链接到redis
	 * @return array|bool
	 * @throws redisQconnectRedisError
	 */
	private function connectRedis(){
		
		$redis = new Redis();		
		try{
			
			if ($this->queueConfig['connectType']==0){
				$connect = $redis->connect($this->queueConfig['server'],$this->queueConfig['port'],10); //去掉长连接
				
			}else{
				$connect = $redis->connect($this->queueConfig['server'],$this->queueConfig['port'],10);
			}
			if ($connect!== true ){
				
				return $this->returnError(3);
			}
			$this->redis= $redis;
			$this->connected = $connect;
			return true;
		}catch (Exception $e){
			throw new redisQconnectRedisError();
			return false;
		}
	}

	/**
	 * 返回redis的链接状态
	 * @return bool
	 */
	private function getConnectStatus(){
		return $this->connected;
	}
	private function returnError($statusCode,$subMessage=""){
		$this->setLastError($statusCode,$subMessage);
		return array('ret'=>$statusCode,'message'=>$this->config->statusConfig[$statusCode],'detailMessage'=>$subMessage);
	}
	public function getLastError(){
		return $this->lastError;
	}
	/**
	 * 创建一个消费的guid
	 * @return string
	 */
	private function createGuid() {
		$charId = strtoupper(md5(uniqid(mt_rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = chr(123)// "{"
		.substr($charId, 0, 8).$hyphen
		.substr($charId, 8, 4).$hyphen
		.substr($charId,12, 4).$hyphen
		.substr($charId,16, 4).$hyphen
		.substr($charId,20,12)
		.chr(125);// "}"
		return $uuid;
	}
	/**
	 * 加载队列配置,附加到redisq
	 */
	private function loadConfig(){
		$config  = new redisQconfig();
		$queueName = $this->getQueueKeyName();
		@$this->queueConfig = $config->$queueName;
		$this->config = $config;
	}

	/**
	 * 写入一个数据到队列中
	 * @param $value
	 * @return array|bool
	 */
	public function push($value){
		$link =  $this->checkLink();
        if ($link!==true){
            throw new RuntimeException();
			$this->returnError(2);
		}
		if ($this->connected === false){
            return $this->returnError(15);
		}
		if (!isset($value)||$value===''){
            return $this->returnError(6,'输入的字段为空');
		}

		try{
            $write = $this->redis->RPUSH(self::$queueName,$value);
            if (!isset($write)||!$write){
				return $this->returnError(4,'写入队列失败，申请值'.$value);
			}
			if ($write>0){
				$maxLength = $this->queueConfig['maxLength'];
				if ($write>$maxLength){ //写入后超出队列长度，进行rdel
					$del = $this->rDelOneByValue($value);
					if ($del===true){
						return $this->returnError(8,'队列最大长度为:'.$maxLength);
					}
					else{
						return $this->returnError(8,'队列最大长度为:'.$maxLength.'从右边剔除元素发生失败');
					}
				}
				return $this->returnMessage(0,array(array('index',$write),array('maxLength',$maxLength)),'写入完成');
			}
		}catch (Exception $e){
			return $this->returnError(4,'写入失败'. json_encode($e));
		}
	}

	/**
	 * 根据value 值 ，在队列尾部移除最近添加的一条记录
	 *  @param $value 写入时的value值
	 */
	private function rDelOneByValue($value){
		if (!isset($value)||$value===''){
			return $this->returnError(6,'输入的字段为空');
		}
		$del = $this->redis->lRem(self::$queueName,$value,-1);
		if (is_numeric($del)&&is_int($del)&&$del===1){
			return true;
		}
		return $this->returnError(19,'value:'.$value.' time:'.time());
	}

	/**
	 * 清空队列内容
	 * @return array|bool
	 */
	public function destroy(){
		$link =  $this->checkLink();
		if ($link!==true){
			return $link;
		}
		$del =  $this->redis->delete(self::$queueName);
		if (isset($del) && is_numeric($del)){
			return $this->returnMessage(0,null,'清空队列成功');
		}
		return $this->returnError(16);
	}

	/**
	 * 从左消费第一个 消息 ，读取后立即删除
	 * @return mixed
	 */
	public function pop(){
		$link =  $this->checkLink();
		if ($link!==true){
			return $link;
		}
		$queueValue =  $this->redis->lPop(self::$queueName);
        if ($queueValue==''||$queueValue===false){
			return $this->returnError(11);
		}
		return $this->returnMessage(0,array(array('queueValue',$queueValue)));
	}

	/**
	 * 入队列时检查队列列表是否超出允许的大小
	 * @return bool
	 */
	private function CheckQueueSize(){
		$queueName = self::$queueName;
		$maxSize  = $this->queueConfig['maxLength'];
		$nowSize = $this->redis->lSize($queueName);
		if (!is_numeric($nowSize)){
			return false;
		}
		if ($nowSize<$maxSize){
			return true;
		}
		return false;
	}

	/**
	 * 构建成功时的返回
	 * @param int $warning
	 * @param null $subMessage
	 * @param string $message
	 * @return array
	 */
	private function returnMessage($warning=0,$subMessage=null,$message=''){
        $return  =array("ret"=>0,"message"=>$message,"warning"=>0);
		if(is_array($subMessage)){
			for ($i=0;$i<count($subMessage);$i++){
				$key = $subMessage[$i][0];
				$value =$subMessage[$i][1];
				$return[$key]= $value;
			}
        }
		$return['queuename']= self::$queueName;
        return $return;
	}

	/**
	 * 取得消费队列
	 * @param int $length
	 * @return array|bool|mixed
	 */
	public function get($length=0){
		$this->consumerGuid = '';//清空消费guid
		$link =  $this->checkLink();
		if ($link!==true){
			return $link;
		}
		if ($length===0){//如果为0 消费一条
			return $this->pop();
		}

		if ($length>$this->queueConfig['consumerMaxLength']){
			return $this->returnError(12); //请求消费长度大于队列要求时
		}
		$queueLength= $this->getQueueLength();
		if ($queueLength===false){
			return $this->returnError(5); //取队列长度失败
		}
		if ($queueLength == 0){
			return $this->returnMessage(0,array(array("queueLength",$queueLength),array('queueValue',null),array('dataLength',0)));
		}
		$data = $this->redis->lRange(self::$queueName, 0, $length -1);
		$consumerGuid = $this->createGuid();
		if (is_array($data)){
			if (count($data)>0){
				$this->consumerGuid = $consumerGuid;
				return $this->returnMessage(0,array(array('queueValue',$data),array('dataLength',count($data)),array('consumerGuid',$consumerGuid)));
			}
			return $this->returnMessage(0,array(array('queueValue',null),array('dataLength',0)));
		}
		return $this->returnError(5);
	}

	/**
	 * 删除队列
	 * @param int $length 删除的行数
	 * @return array|bool
	 */
	public function del($length=0,$consumerGuid){
		$link =  $this->checkLink();
		if ($link!==true){
			return $link;
		}
		if ($length===0){
			return $this->returnMessage(1,array(array('warningMessage','删除队列时请不要传递空值')));
		}
		if (empty($consumerGuid)){
			return $this->returnError(17); //无guid 直接返回错误
		}
		if ($consumerGuid!= $this->consumerGuid){
			return $this->returnError(18);
		}
		$length = 0 + $length;
		$trim= $this->redis->lTrim(self::$queueName,$length,-1);
		if ($trim!==true){
			return $this->returnError(13);
		}
		return $this->returnMessage(0,array(array('rows',$length ),array('consumerGuid',$consumerGuid)),'删除成功');
	}

	/**
	 * @return bool|int|string 取得队列长度，私有方法获取相应队列的长度
	 */
	private function getQueueLength(){
		$length = $this->redis->lSize(self::$queueName);
		if (is_numeric($length)&&$length>=0){
			return $length;
		}
		return false;
	}

	/**
	 * @return array|bool取得队列长度
	 */
	public function getLength(){
		$link =  $this->checkLink();
		if ($link!==true){
			return $link;
		}
		$length = $this->getQueueLength();
		if ($length!==false){
			$consumerNum = $this->queueConfig['consumerMaxLength']; //建议消费值
			$subMessage = array(
					array('consumerNum',$consumerNum),
					array('consumerMaxLength',$this->queueConfig['consumerMaxLength']),
					array('queueLength',$length),
			);
			return $this->returnMessage(0,$subMessage);
		}
	}

	/**
	 * 检查队列redis链接是否正常
	 * @return array|bool
	 */
	private function checkLink(){
		if ($this->connected === false){
			return $this->returnError(2);
		}
		return true;
	}

	/**
	 * 单列模式操作时，重新切换操作的队列
	 * @param $name
	 * @return array|bool
	 * @throws redisQparameterError  //如果参数为空，则直接抛出异常
	 */
	public function setQueueName($name){
		if (!$name||$name==""){
			throw new redisQparameterError();
		}
		self::$queueName='RQ:'.$name;
		$init = $this->init();
		if ($init!==true){
			return $init;
		}
		return true;
	}

	/**
	 * 关闭redis链接
	 */
	public function  __destruct(){
		if($this->redis) {
			$this->redis->close();
		}
		unset($this->redis);
	}
}
if (!function_exists('redisQOnError')){
	function redisQOnError($errno, $errstr, $errfile, $errline){
		if (!strstr($errfile,'redisQ.php')){
			return;
		}
		$LOG_OBJ = new Object();
		$ConfigObject = new redisQconfig();
		$ConfigString = json_encode($ConfigObject);
		$LOG_OBJ->log('未处理异常，redis 链接中断,redis 如果还不能继续链接，则此进程僵死'.$ConfigString,'redisQErrorLog');
		throw new redisQconnectRedisError();
		return true;
	}
	$error = set_error_handler("redisQOnError");
}
