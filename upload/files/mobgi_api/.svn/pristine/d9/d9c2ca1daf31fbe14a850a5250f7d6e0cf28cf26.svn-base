<?php
/**
 * CacheController
 * cache管理器,对reids等进行增删改查删除等操作
 *
 * @author Stephen.Feng
 */
Doo::loadController("AppDooController");

class CacheController extends AppDooController{
    private $portlist;
    public function __construct() {
        parent::__construct();
        $this->portlist=array(Doo::conf()->CACHE_REDIS_SERVER_DEFAULT);
        for($i=1;$i<15;$i++){
            $port="CACHE_REDIS_SERVER_".$i;
            if(empty(Doo::conf()->$port)){
               break;
            }
            array_push($this->portlist,Doo::conf()->$port);
        }
		
    }
    public function index(){
        if (!$this->checkPermission(APP, CACHE_EDIT)) {
            $this->displayNoPermission();
        }
        $port=$this->port2sting($this->get["port"]);
        Doo::loadClass("Fredis/FRedis");
        $res= FRedis::getSingletonPort($port);
        $keys=$res->keys("*");
        $this->data["keys"]=$keys;
        $this->data["port"]=$this->get["port"];
        $this->data["portlist"]=$this->portlist;
        $this->myrender("cache/index", $this->data);
    }
    public function del(){
        if (!$this->checkPermission(CACHE, CACHE_EDIT)) {
            $this->displayNoPermission();
        }
        $port=$this->port2sting($this->get["port"]);
        Doo::loadClass("Fredis/FRedis");
        $res= FRedis::getSingletonPort($port);
        $keys=$res->del($this->get["key"]);
        $this->userLogs(array('msg' => json_encode($this->get), 'title' => 'CACHE管理', 'action' => 'delete'));
        $this->redirect("javascript:history.go(-1)","删除成功");
    }

    public function cleanredis(){
        if (!$this->checkPermission(CACHE, CACHE_EDIT)) {
            $this->displayNoPermission();
        }
        $port=$this->port2sting($this->get["port"]);
        Doo::loadClass("Fredis/FRedis");
        $res= FRedis::getSingletonPort($port);
        if($port=="CACHE_REDIS_SERVER_2"){
            $this->cleankey($port,"current");
        }else{
            $res->flushdb();
        }
        $this->redirect("javascript:history.go(-1)","清空成功");
    }
    public function cleankey($port,$notdel=""){//删除指定key
        Doo::loadClass("Fredis/FRedis");
        $res= FRedis::getSingletonPort($port);
        $key=$res->keys("*");
        foreach($key as $k=>$v){
            if(empty($notkey)){
                $s=strstr($v,$notdel);
                if(!empty($s)){continue;}
            }
            $res->del($v);
        }
    }
    public function flushdb(){
        if (!$this->checkPermission(CACHE, CACHE_EDIT)) {
            $this->displayNoPermission();
        }
        Doo::loadClass("Fredis/FRedis");
        foreach ($this->portlist as $k=>$p){
            $port=$this->port2sting($p["port"]);
            $res= FRedis::getSingletonPort($port);
            if($port=="CACHE_REDIS_SERVER_2"){
                $this->cleankey($port,"current");
            }else{
                $res->flushdb();
            }
        }
        $this->userLogs(array('msg' => json_encode($this->portlist), 'title' => 'CACHE管理', 'action' => 'delete'));
        $this->redirect("javascript:history.go(-1)","刷新成功");
    }
    private function port2sting($port){//转换port
        foreach ($this->portlist as $k=>$p){
            if($port==$p["port"]){
                if($k==0){
                    return "CACHE_REDIS_SERVER_DEFAULT";
                }else{
                    return "CACHE_REDIS_SERVER_".$k;
                }
            }
        }
    }
}
?>
