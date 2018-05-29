<?php
Doo::loadModel('datamodel/base/IptadConfigBase');

class IptadConfig extends IptadConfigBase{
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    function getConfigList($id="",$name="",$appkey="", $appname=''){
        $whereSql="a.del!=0 ";
        if(!empty($id)){
            $whereSql=" and a.id='".$id."'";
        }
        if(!empty($name)){
            $whereSql.=" and a.config_name ='".$name."'";
        }
        if(!empty($appkey)){
            $whereSql.=" and a.appkey='".$appkey."'";
        }
        if(!empty($appname)){
            $whereSql.=" and b.appname like'%".$appname."%'";
        }
        $res=Doo::db()->query("select a.*,a.id as config_id,b.appname from iptad_config a left join iptad_app b on a.appkey=b.appkey where ".$whereSql." order by createdate desc")->fetchAll();
        return $res;
    }
    function getConfigApp($configid){
        $res=Doo::db()->query("select * from iptad_config a  left join iptad_app b on a.appkey=b.appkey where a.id=".$configid)->fetch();
        return $res;
    }
    function getConfig($configid){
        return $this->getOne(array("where"=>'id ='.$configid.' ','asArray' => TRUE));
    }
    function updateConfig($data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->config_name = $data['config_name'];
        $this->appkey = $data['appkey'];
        $this->platform = 1;
        $this->del=$data["del"];
        $this->status = $data['status'];
        $this->operator = $currentUser['username'];
        $this->updatedate = time();
        if(empty($data["config_id"])){
            $this->createdate = time();
            $this->insert();
            return $this->lastInsertId();
        }else{
            $this->id=$data["config_id"];
            $this->update();
            $this->res("IMPLANTABLE_REDIS_CACHE_DEFAULT")->delete("CONFIG_MAP_APPKEY_".$data["config_id"]);
            return $data["config_id"];
        }
    }
    function addconfig($data){
        $config_id=$this->updateConfig($data);
        $limit=Doo::loadModel("datamodel/implantable/IptadLimit",TRUE);
        $data["config_id"]=$config_id;
        $limit->updateLimit($data);
    }
    function delConfig($id){
        $this->id=$id;
        $this->del=0;
        $this->update();
        $this->res("IMPLANTABLE_REDIS_CACHE_DEFAULT")->delete("CONFIG_MAP_APPKEY_".$id);
    }
}