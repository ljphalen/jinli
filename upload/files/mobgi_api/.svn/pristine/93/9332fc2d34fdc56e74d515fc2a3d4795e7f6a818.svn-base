<?php
/**
 * 扩展Doophp模型
 *
 * @author: Intril
 * Date: 13-4-15
 */

Doo::loadCore('db/DooModel');
class AppModel extends DooModel {
	/**
	 * 构造函数
	 * Enter description here ...
	 * @param unknown_type $properties
	 */
	public function __construct($properties=null){
		parent::__construct($properties=null);
		Doo::db()->reconnect('prod');
	}

	/**
	 * 分表分库调用
	 *
	 * @param Integer $shardId	分表分库ID
	 * @param String $dbPrefix	表名和库名前缀
	 * @return string $shardId['table'] 返回表名
	 */
	public function mysqlShard($shardId, $dbPrefix){
		Doo::loadClass('MysqlShard/MysqlShard');
		$shardInfo = MysqlShard::init($shardId, $dbPrefix);
		// 设置数据库连接
		Doo::db()->setDb($shardInfo, 'host');
		// 断开默认连接后，再连接新DB
		Doo::db()->disconnect();
		return $shardInfo['table'];
	}
	public function snoopy(){
		Doo::loadClass("net/Snoopy.class");
		$snoopy=new Snoopy();
		return $snoopy;
	}
	public function curl(){
		Doo::loadClass("net/class.curl");
		$curl=new CURL();
		return $curl;
	}
	
	//获取渠道信息
	public function _getChannelById($channle_id){
		$channle_id=  is_array($channle_id)?$channle_id:(array)$channle_id;
		$channel_result=array();
		$return = array();
		if (empty($channle_id)){
			return $channel_result;
		}
		$channelAll = $this->get('CHANNEL_ALL', 'CACHE_REDIS_SERVER_3');
		$channelAllArr = json_decode($channelAll, true);

        //所有渠道从DB中获取
        $channelAllArrFromDb = array();
        $categoryModel = Doo::loadModel('datamodel/Category', TRUE);
        $categoryArr = $categoryModel->findAll();
        if(!empty($categoryArr)){
            foreach($categoryArr as $categoryItem){
                $channelItem = json_decode($categoryItem['channelData'], true);
                foreach($channelItem as $channelinfo){
                    //渠道不存在的才新增(去重)
                    if(!isset($channelAllArrFromDb[$channelinfo['channels']['identifier']])){
                        $channelAllArrFromDb[$channelinfo['channels']['identifier']] = $channelinfo['channels']['realname'];
                    }
                }
            }
        }
        
        if (empty($channelAllArr)) {
//			$channelAllArr = Doo::conf()->channel_all_conf;
            $channelAllArr = $channelAllArrFromDb;
			$this->set('CHANNEL_ALL',json_encode($channelAllArr),"CACHE_REDIS_SERVER_3");
		}
		foreach ($channle_id as $cid){
            //redis中不存在某个渠道时，则从存有全部渠道的DB中获取指定的渠道。
			if (empty($channelAllArr[$cid])){ // redis has not
//				$url=  sprintf(Doo::conf()->CHANNEL_URL,$cid);
//				if(!empty($url)){
//					$channel=$this->curl()->get($url,0);
//					$channel = json_decode($channel, true);
//					if (!empty($channel['result'][0]['realname'])){
//						$channel_result[$cid] = $channel['result'][0]['realname'];
//					}
//				}
//				$return[$cid] = $channel['result'][0]['realname'];
                $return[$cid] = $channelAllArrFromDb[$cid];
			}else{
				$return[$cid] = $channelAllArr[$cid];
			}
		}
		if (!empty($channel_result)){
			$channelAllArr = array_merge($channelAllArr, $channel_result);
			$this->set('CHANNEL_ALL',json_encode($channelAllArr),"CACHE_REDIS_SERVER_3");
		}
		return $return;
	}
	//获取appkey信息
	//$appkey array
	public function _getAppkeyBykey($appkey){
		$sql="select app_name,appkey from ad_app where appkey in(".implodeSqlstr($appkey).")";
		$appinfo=Doo::db()->query($sql)->fetchAll();
		return $appinfo;
	}
	/*
	 * 获取Redis值
	 * @param $key string
	 */
	public function get($key,$port=""){
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		$adconfig=$this->res($port)->get($key);
		return $adconfig;
	}
	protected function res($port){
		Doo::loadClass("Fredis/FRedis");
		if(empty($port)){
			$port="CACHE_REDIS_SERVER_DEFAULT";
		}
		$res= FRedis::getSingletonPort($port);
		return $res;
	}
	/*
	 * 设定Redis值
	 * @param $key string
	 * @param $data string
	 * @param $port redis配置中的名字
	 */
	public function set($key,  $data, $port="",  $timeout=''){
        if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
            return false;
        }
        $data=  is_array($data)?json_encode ($data):$data;
        $this->res($port)->set($key, $data);
        if($timeout){
            $this->res($port)->expire($key, intval($timeout));
        }
        return true;
    }
    
	/*
	 * 清空redis数据
	 */
	public function flushDB($port=""){
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		$this->res($port)->flushdb();
		return true;
	}
	/*
	 * 删除Redis值
	 * @param $key string|array
	 * @param $data string
	 */
	public function deleter($key, $port=""){
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		$key=  is_array($key)?$key:(array)$key;
		foreach ($key as $v){
			$this->res($port)->delete($v);
		}
		return true;
	}
	/*
	 * 获取redis内的key值
	 */
	public function getkeys($port,$string="*")
	{
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		$keys=$this->res($port)->keys($string);
		return $keys;
	}
	//正则表达式匹配删除redis key
	public function deleterRegex($regex){
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		if(empty($regex)){
			return false;
		}
		$keys=$this->res($port)->keys("*");
		if($regex=="*"){//清空
			$this->res($port)->flushdb();
		}
		foreach ($keys as $v){
			if (preg_match($regex,$v)) {
				$this->res($port)->delete($v);
			}
		}
		return true;
	}
	public function getArray( $key,$port="") {
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		$data = array();
		$res_arr = $this->res($port)->hGetAll( $key );
		if ( is_array( $res_arr ) && count( $res_arr ) > 0 ) {
			$data = $res_arr;
		}
		return $data;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $key 要插入的key名
	 * @param unknown_type $value 要插入的数组
	 * @param unknown_type $port  要插入的redis的配置名称
	 * @param unknown_type $timeout 过期时间
	 */
	public function setArray($key,$value,$port="",$timeout='')
	{
		if(!Doo::conf()->ADS_IS_RDIS){//如果不走redis则直接返回
			return false;
		}
		if ( is_array( $value ) ) {
			$this->res($port)->hMset( $key, $value );		
			if($timeout!=""){
				$res->expire("$key",intval($timeout));
			}
		}
		return true;
	}

	/*
	 * 条件构造SQL查询语句
	 * @params $conditions string | array  ->所有参数的数组
	 * @params $_array string | array  ->需要构造的字段数组，像 array('id','title');
	 */
	protected function conditions($conditions = NULL,$_array = array())
	{
		if (empty($conditions)){
			return "1=1";
		}
		if(!is_array($conditions)){
			return $conditions;
		}
		$where = array('1=1');//防止conditions内没有_array要包含的字段
		if(!is_array($_array))
		{
			if (isset($conditions["$_array"]) && $conditions["$_array"]){
				$where[] = $_array."=".$conditions[$_array];
			}
		}
		else
		{
			foreach($_array as $value)
			{
				if(isset($conditions["$value"]))
				{
					$where[] = $value."=".$conditions["$value"];
				}
			}
		}
		$whereSQL = implode(" AND ", $where);
		return $whereSQL;
	}

	/**
	 * 上传到CDN函数
	 * Enter description here ...
	 * @param $sourcefile
	 * @param $dir_name
	 * @param $option
	 * @param $item_id
	 */
	protected  function UpToCDN($sourcefile,$dir_name,$option="publish",$item_id="normal")
	{
		$username = Doo::conf()->cdn_username;
		$pwd = Doo::conf()->cdn_pwd;
		$checkstr = $item_id.$username."chinanetcenter".$pwd;
		$checkcode = md5($checkstr);
		$checkfile = md5_file($sourcefile);
		$report = "HTTP://".$_SERVER['HTTP_HOST']."/Callback/report";
		if($option == "publish")
		{
			
			$xml = '<?xml%20version="1.0"%20encoding="UTF-8"%20?>'.
		'<ccsc>'.
			'<cust_id>'.
				$username.
			'</cust_id>'.
			'<passwd>'.
				$checkcode.
			'</passwd>'.
			'<publish_report>'.
				$report.
			'</publish_report>'.
			'<item_id%20value="'.$item_id.'">'.
				'<source_path>'.
					$sourcefile.
				'</source_path>'.
				'<publish_path>'.
					$dir_name.
				'</publish_path>'.
				'<md5>'.
					$checkfile.
				'</md5>'.
				'<fsize>'.
				'</fsize>'.
			'</item_id>'.
		'</ccsc>';
		}
		else if($option == "delete")
		{
			$xml = '<?xml%20version="1.0"%20encoding="UTF-8"%20?>'.
		'<ccsc>'.
			'<cust_id>'.
				$username.
			'</cust_id>'.
			'<passwd>'.
				$checkcode.
			'</passwd>'.
			'<item_id%20value="'.$item_id.'">'.
				'<source_path>'.
					$sourcefile.
				'</source_path>'.
				'<publish_path>'.
					$dir_name.
				'</publish_path>'.
			'</item_id>'.
		'</ccsc>';
		}
		$url = Doo::conf()->cdn_url.'?op='.$option.'&context='.$xml;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_AUTOREFERER , TRUE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_BINARYTRANSFER, TRUE);
		$output = curl_exec($ch);
		curl_close($ch);

		return Doo::conf()->cdn_path.$dir_name;
    }
}