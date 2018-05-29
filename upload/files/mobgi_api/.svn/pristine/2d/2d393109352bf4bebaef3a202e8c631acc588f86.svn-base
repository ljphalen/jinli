<?php
$db_config["hostname"] = "192.168.0.14"; //服务器地址
$db_config["username"] = "stephen"; //数据库用户名
$db_config["password"] = "123456"; //数据库密码
$db_config["database"] = "mobgi_ads"; //数据库名称
$db_config["charset"] = "utf8";//数据库编码
$db_config["pconnect"] = 1;//开启持久连接
$db_config["log"] = 1;//开启日志
$db_config["logfilepath"] = './';//开启日志
$db=new DB($db_config);
$db->query("truncate table mobgi_ads.ad_apk");
$db->query("truncate table mobgi_ads.ad_pic");
$db->query("truncate table mobgi_ads.ad_text");
$db->query("truncate table mobgi_ads.ads_info");
$db->query("truncate table mobgi_ads.ads_product_info");
$db->query("truncate table mobgi_ads.ads_product_limit");

//把mobgi_api.ad_product_info的记录插入mobgi_ads
$sql="INSERT INTO mobgi_ads.`ads_product_info` (id,product_name,appkey,product_icon,product_desc,product_url,click_type_object,product_version,product_package,ischeck,checker,`owner`,check_msg,created,updated) SELECT id,product_name,appkey,product_icon,product_desc,product_url,click_type_object,product_version,product_package,1,'fengfangqian','fengfangqian','数据导入',created,updated FROM mobgi_api.`ad_product_info`";
$db->query($sql);
echo "正在转化mobgi_api.ad_product_info->mobgi_ads.ads_product_info\r\n";
//转化产品中的图标为素材库中的图标
$sql="select * from mobgi_api.ad_product_info where product_icon!=''";
$productres=  $db->get_all($sql);
if(!empty($productres)){
        echo "正在转化产品图标和APK\r\n";
	foreach($productres as $product){
		$sql="insert into mobgi_ads.ad_pic set ad_product_id='".$product["id"]."',product_name='".$product["product_name"]."',pic_name='".$product["product_name"]."-icon-".$product["product_name"]."',pic_url='".$product["product_icon"]."',ad_type='10',"
					. "ad_subtype='10',resolution='',screen_ratio='1',ischeck=1,checker='fengfangqian',owner='fengfangqian',check_msg='系统转化',creattime=".$product["created"].",updatetime='".$product['updated']."'";
		$db->query($sql);
		if(!empty($product["click_type_object"])){
			$clicktype=json_decode($product["click_type_object"],true);
			if(is_array($clicktype) && isset($clicktype["inner_install_manage"])){
                                $clicktype=$clicktype["inner_install_manage"];
				$sql="insert into mobgi_ads.ad_apk set ad_product_id='".$product["id"]."',product_name='".$product["product_name"]."',apk_url='".$product["product_url"]."',apk_version='".$product['product_version']."',package_name='".$clicktype["product_package"]."',size='".$clicktype["package_size"]."',ischeck=1,checker='fengfangqian',owner='fengfangqian',check_msg='系统转化',createtime=".$product["created"].",updatetime='".$product['updated']."'";
				$db->query($sql);
			}
		}	
	}
}
$sql="INSERT INTO mobgi_ads.`ads_product_limit` SELECT * FROM mobgi_api.`ad_product_limit`";
$db->query($sql);
echo "正在转化导量信息\r\n";
$sql="select * from mobgi_api.ad_info";
$res=  $db->get_all($sql);
foreach ($res as $v){
    $product=$db->get_one("select * from mobgi_api.ad_product_info where id='".$v["ad_product_id"]."'");
    if($v["type"]==0){//抢占式
        echo "正在转化抢占是广告".$v["ad_name"]."\r\n";
        $sql="select * from mobgi_api.ad_not_embedded_info where ad_info_id=".$v["id"];
        $nres=$db->get_one($sql);
		
		$screen_type=$nres["screen_type"];
		if(!empty($nres["ad_pic_url"]) && empty($nres["resolution"])){//如果分辨率为空这取ad_pic_info的信息
			echo "正在获取".$nres["ad_pic_url"]."图片的分辨率信息.......\r\n";
			$imagesinfo=getimagesize($nres["ad_pic_url"]);
			$nres["resolution"]=$imagesinfo[0]."*".$imagesinfo[1];
			$sql="update  mobgi_api.ad_not_embedded_info set resolution='".$nres["resolution"]."' where ad_info_id=".$v["id"];
			$db->query($sql);
		}
		if($screen_type==2){continue;}//如果是横竖屏都支持则丢弃
		
        $sql="insert into mobgi_ads.ad_pic set ad_product_id='".$v["ad_product_id"]."',product_name='".$product["product_name"]."',pic_name='".$v["ad_name"]."',pic_url='".$nres["ad_pic_url"]."',ad_type='".$v["type"]."',"
                . "ad_subtype='$screen_type',resolution='".$nres["resolution"]."',screen_ratio='".$nres["screen_ratio"]."',ischeck=1,checker='fengfangqian',owner='fengfangqian',check_msg='系统转化',creattime=".$nres["created"].",updatetime='".$nres['updated']."'";
		$show_time=$nres["show_time"];
        $rate=$nres["rate"];
        $type=0;
        $r_type=1;//抢占式广告都是图片
        $db->query($sql);
        $r_id=$db->insert_id();
    }
    if($v["type"]==1){//嵌入式
        $sql="select * from mobgi_api.ad_embedded_info where ad_info_id=".$v["id"];
        $eres=$db->get_one($sql);
		$show_time=$eres["show_time"];
        if(empty($eres)){continue;}
        if(!empty($eres["ad_pic_url"]) && empty($eres["resolution"])){//如果分辨率为空这取ad_pic_info的信息
			echo "正在获取".$eres["ad_pic_url"]."图片的分辨率信息.......\r\n";
			$imagesinfo=getimagesize($eres["ad_pic_url"]);
			$eres["resolution"]=$imagesinfo[0]."*".$imagesinfo[1];
			$sql="update  mobgi_api.ad_embedded_info set resolution='".$nres["resolution"]."' where ad_info_id=".$v["id"];
			$db->query($sql);
		}
        if($eres["type"]==1){//如果是banner文字广告则插入文本素材库
                echo "正在转化嵌入式广告".$v["ad_name"]." banner\r\n";	
                $sql="insert into mobgi_ads.ad_text set ad_product_id='".$v["ad_product_id"]."',product_name='".$product["product_name"]."',"
        . "subtype=1,type='介绍',ad_name='".$eres["ad_name"]."',content='".$eres["ad_desc"]."',ischeck=1,checker='fengfangqian',owner='fengfangqian',check_msg='系统转化',createtime='".$eres["created"]."',updatetime='".$eres['updated']."'";
                $rate=$eres["rate"];
                $type=1;
                $r_type=2;//嵌入式文案素材
                $db->query($sql);
                $r_id=$db->insert_id();
        }else{//其他的插入图片库
				
                echo "正在转化嵌入式广告".$v["ad_name"]." 插页\r\n";	
                if($eres["type"]==1){$subtype=1;}else if($eres["type"]==0){$subtype=20;}else{$subtype=2;}
                $sql="insert into mobgi_ads.ad_pic set ad_product_id='".$v["ad_product_id"]."',product_name='".$product["product_name"]."',pic_name='".$v["ad_name"]."',pic_url='".$eres["ad_pic_url"]."',ad_type='1',"
        . "ad_subtype='$subtype',resolution='".$eres["resolution"]."',screen_ratio='".$eres["screen_ratio"]."',ischeck=1,checker='fengfangqian',owner='fengfangqian',check_msg='系统转化',creattime='".$eres["created"]."',updatetime='".$eres['updated']."'";
                $rate=$eres["rate"];
                $type=1; 
                $r_type=1;//抢占式广告都是图片
                $db->query($sql);
                $r_id=$db->insert_id();
        }
    }
    if($v["type"]==2){//push
        $sql="select * from mobgi_api.ad_embedded_info where ad_info_id=".$v["id"];
        $eres=$db->get_one($sql);
        echo "正在转化push广告".$v["ad_name"]." banner\r\n";	
        if(empty($eres)){continue;}
        $sql="insert into mobgi_ads.ad_text set ad_product_id='".$v["ad_product_id"]."',product_name='".$product["product_name"]."',"
        . "subtype=2,type='介绍',ad_name='".$eres["ad_name"]."',content='".$eres["ad_desc"]."',ischeck=1,checker='fengfangqian',owner='fengfangqian',check_msg='系统转化',createtime='".$eres["created"]."',updatetime='".$eres['updated']."'";
		$show_time=$eres["show_time"];
        $rate=$res["rate"];
        $type=2;
        $r_type=3;//嵌入式push
        $db->query($sql);
        $r_id=$db->insert_id();
    }
    
    $sql="insert into mobgi_ads.ads_info set id='".$v["id"]."',ad_product_id='".$v["ad_product_id"]."',ad_name='".$v["ad_name"]."',ad_desc='".$v["ad_desc"]."',ad_click_type_object='".$v["ad_click_type_object"]."',"
            . "ad_target='".$v["ad_target"]."',state='".$v["state"]."',show_detail='".$v["show_detail"]."',show_time='".$show_time."',pos='".$v["pos"]."',type='$type',created='".$v["created"]."',updated='".$v["updated"]."',rate='$rate',r_id=$r_id,r_type=$r_type";
    $db->query($sql);
}



Class DB {
 
    private $link_id;
    private $handle;
    private $is_log;
    private $time;
 
    //构造函数
    public function __construct($db_config) {
        $this->time = $this->microtime_float();
        //require_once("config.db.php");
        $this->connect($db_config["hostname"], $db_config["username"], $db_config["password"], $db_config["database"], $db_config["pconnect"]);
        $this->is_log = $db_config["log"];
        if($this->is_log){
            $handle = fopen($db_config["logfilepath"]."dblog.txt", "a+");
            $this->handle=$handle;
        }
    }
     
    //数据库连接
    public function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0,$charset='utf8') {
        if( $pconnect==0 ) {
            $this->link_id = @mysql_connect($dbhost, $dbuser, $dbpw, true);
            if(!$this->link_id){
                $this->halt("数据库连接失败");
            }
        } else {
            $this->link_id = @mysql_pconnect($dbhost, $dbuser, $dbpw);
            if(!$this->link_id){
                $this->halt("数据库持久连接失败");
            }
        }
        if(!@mysql_select_db($dbname,$this->link_id)) {
            $this->halt('数据库选择失败');
        }
        @mysql_query("set names ".$charset);
    }
     
    //查询 
    public function query($sql) {
        $this->write_log("查询 ".$sql);
        $query = mysql_query($sql,$this->link_id);
        if(!$query) $this->halt('Query Error: ' . $sql);
        return $query;
    }
     
    //获取一条记录（MYSQL_ASSOC，MYSQL_NUM，MYSQL_BOTH）              
    public function get_one($sql,$result_type = MYSQL_ASSOC) {
        $query = $this->query($sql);
        $rt =& mysql_fetch_array($query,$result_type);
        $this->write_log("获取一条记录 ".$sql);
        return $rt;
    }
 
    //获取全部记录
    public function get_all($sql,$result_type = MYSQL_ASSOC) {
        $query = $this->query($sql);
        $i = 0;
        $rt = array();
        while($row =& mysql_fetch_array($query,$result_type)) {
            $rt[$i]=$row;
            $i++;
        }
        $this->write_log("获取全部记录 ".$sql);
        return $rt;
    }
     
    //插入
    public function insert($table,$dataArray) {
        $field = "";
        $value = "";
        if( !is_array($dataArray) || count($dataArray)<=0) {
            $this->halt('没有要插入的数据');
            return false;
        }
        while(list($key,$val)=each($dataArray)) {
            $field .="$key,";
            $value .="'$val',";
        }
        $field = substr( $field,0,-1);
        $value = substr( $value,0,-1);
        $sql = "insert into $table($field) values($value)";
        $this->write_log("插入 ".$sql);
        if(!$this->query($sql)) return false;
        return true;
    }
 
    //更新
    public function update( $table,$dataArray,$condition="") {
        if( !is_array($dataArray) || count($dataArray)<=0) {
            $this->halt('没有要更新的数据');
            return false;
        }
        $value = "";
        while( list($key,$val) = each($dataArray))
        $value .= "$key = '$val',";
        $value .= substr( $value,0,-1);
        $sql = "update $table set $value where 1=1 and $condition";
        $this->write_log("更新 ".$sql);
        if(!$this->query($sql)) return false;
        return true;
    }
 
    //删除
    public function delete( $table,$condition="") {
        if( empty($condition) ) {
            $this->halt('没有设置删除的条件');
            return false;
        }
        $sql = "delete from $table where 1=1 and $condition";
        $this->write_log("删除 ".$sql);
        if(!$this->query($sql)) return false;
        return true;
    }
 
    //返回结果集
    public function fetch_array($query, $result_type = MYSQL_ASSOC){
        $this->write_log("返回结果集");
        return mysql_fetch_array($query, $result_type);
    }
 
    //获取记录条数
    public function num_rows($results) {
        if(!is_bool($results)) {
            $num = mysql_num_rows($results);
            $this->write_log("获取的记录条数为".$num);
            return $num;
        } else {
            return 0;
        }
    }
 
    //释放结果集
    public function free_result() {
        $void = func_get_args();
        foreach($void as $query) {
            if(is_resource($query) && get_resource_type($query) === 'mysql result') {
                return mysql_free_result($query);
            }
        }
        $this->write_log("释放结果集");
    }
 
    //获取最后插入的id
    public function insert_id() {
        $id = mysql_insert_id($this->link_id);
        $this->write_log("最后插入的id为".$id);
        return $id;
    }
 
    //关闭数据库连接
    protected function close() {
        $this->write_log("已关闭数据库连接");
        return @mysql_close($this->link_id);
    }
 
    //错误提示
    private function halt($msg='') {
        $msg .= "\r\n".mysql_error();
        $this->write_log($msg);
        die($msg);
    }
 
    //析构函数
    public function __destruct() {
        $this->free_result();
        $use_time = ($this-> microtime_float())-($this->time);
        $this->write_log("完成整个查询任务,所用时间为".$use_time);
        if($this->is_log){
            fclose($this->handle);
        }
    }
     
    //写入日志文件
    public function write_log($msg=''){
        if($this->is_log){
            $text = date("Y-m-d H:i:s")." ".$msg."\r\n";
            fwrite($this->handle,$text);
        }
    }
     
    //获取毫秒数
    public function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}
?> 
