<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdConfigTag
 *
 * @author Stephen.Feng
 */
Doo::loadModel('AppModel');
class AdConfigTag extends AppModel {
    private $configtagmodel;

    public function __construct() {
        parent::__construct();
        $this->configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
    }
    //通过id获取
    public function getValueByConfigid($configid){
        $result=array();
        $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
        $result=$configtagmodel->find(array("select"=>"type,type_value","where"=>"ad_config_id='".$configid."'","desc"=>"type","asArray"=>true));
        if(!empty($result)){
            $appkey=array();
            $channel_id=array();
            $app=Doo::loadModel("Apps",TRUE);
            foreach ($result as $value){
                if($value["type"]=="1"){
                    array_push($appkey, $value["type_value"]);
                }
                if($value["type"]=="2"){
                    array_push($channel_id, $value["type_value"]);
                }
            }
            $sql="select app_name,appkey from ad_app where appkey in(".implodeSqlstr($appkey).")";
            $appinfo=Doo::db()->query($sql)->fetchAll();
            $channelinfo=$this->_getChannelById($channel_id);
            return array("appkey"=>$appinfo,"channel_id"=>$channelinfo);
        }
        return $result;

    }
    /*
     * @param $config_id
     * @param $type_value array
     * @param $type
     */
    public function add($config_id,$type_value,$type){
        $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
        if(!is_array($type_value)){
            $type_value=(array)$type_value;
        }
        $this->delByConfigidType($config_id,$type);//首先全部删了
        $type_value=array_unique($type_value);//移除数组中重复的值

       // $filename="/tmp/ad_configtag.txt";
       // @unlink($filename);
        foreach($type_value as $value){
              //$sql="insert into ad_config_tags(ad_config_id,type,type_value,created,updated) value($config_id,$type,$value,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())\r\n";
              //$data="'',$config_id,$type,$value,".time().",".time()."\r\n";
              //file_put_contents($filename,$data,FILE_APPEND);
            $configtagmodel->ad_config_id=$config_id;
            $configtagmodel->type=$type;
            $configtagmodel->type_value=$value;
            $configtagmodel->updated=time();
            $configtagmodel->created=time();
            $configtagmodel->insert();
        }

       //if(file_exists($filename)){
         //   Doo::db()->query("load data local  infile '".$filename."' into table ad_config_tags fields terminated by ','");
       // }
       return true;
    }
    /*
     * @param $config_id
     * @param $type_value array
     * @param $type
     */
    public function delByConfigidType($config_id,$type,$type_value=NULL){
        $where="";
        if(!empty($type_value)){
            $where=" and type_value='".$type_value."'";
        }
        $sql="delete from ".$this->configtagmodel->_table." where ad_config_id='".$config_id."' and type='".$type."'".$where;
        Doo::db()->query($sql)->execute();
        return true;
    }
    /*
     * @param $type_value array
     * @param $type
     */
    public function delTypeValue($type_value,$type){
        $sql="delete from ".$this->configtagmodel->_table." where type_value='".$type_value."' and type='".$type."'";
        Doo::db()->query($sql)->execute();
        return true;
    }
    //根据configid删除redis中数据
    public function delConfigByIdFromRedis($configid){
        $sql="select type_value from ".$this->configtagmodel->_table." where ad_config_id='".$configid."' and type=1";
        $appkey=Doo::db()->query($sql)->fetchAll();
        foreach($appkey as $key){
            $this->deleterRegex("/^".$key["type_value"]."_*/");
        }
    }
    public function delByConfigid($config_id){
        $sql="delete from ad_config_tags where ad_config_id='".$config_id."'";
        Doo::db()->reconnect('prod');
        Doo::db()->query($sql)->execute();
    }
    //通过appkey,channel_id删除redis数据
    public function delConfiginfoByAppkeyChannelIdForRedis($appkey,$channel_id){
        //$keys=getArrayCom($appkey,$channel_id,"_1");//列表
        //$this->deleter($keys);

        //$keys=getArrayCom($appkey,$channel_id,"_");//非列表
        //$this->deleter($keys);
        if(empty($appkey)){
            return false;
        }
        foreach($appkey as $key){
            $this->deleterRegex("/^".$key."_*/");
        }
    }
    //删除redis中的config信息
    public function delConfiginfoByConfigidForRedis($config_id){
        $configid=$this->getValueByConfigid($config_id);
        if(!empty($configid)){
            foreach($configid["appkey"] as $v1){
                $appkey[]=$v1["appkey"];
            }
            foreach($configid["channel_id"] as $v2){
                $channel_id[]=$v2["identifier"];
            }
            $this->delConfiginfoByAppkeyChannelIdForRedis($appkey,$channel_id);
        }
    }
    public function checkUsed($type, $typeValue){
        $sql = "select name from ad_config as ad left join ad_config_tags as tags on ad.id = tags.ad_config_id where tags.type=".$type." and type_value ='".$typeValue."'";
        $rs = Doo::db()->query($sql);
        $result = array();
        while($row = $rs->fetch()){
            $result[] = $row['name'];
        }
        return $result;
    }
    //检查push的key是否被使用过
    public function checkPushKeyUsed($appkey){
        $appkey=  is_array($appkey)?$appkey:(array)$appkey;
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $configresult=$adconfig->find(array("select"=>"id","where"=>"type='push'","asArray"=>true));
        $configid=array();
        foreach($configresult as $id){
            array_push($configid,$id["id"]);
        }
        if(empty($configresult)){
            return false;
        }
        $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
        $result=$configtagmodel->find(array("select"=>"type_value","where"=>"ad_config_id in(".  implode(",",$configid).") and type=1","asArray"=>true));
        if(!empty($result)){
            foreach ($appkey as $key){
                if(in_array(array("type_value"=>$key),$result)){
                    return $key;
                }
            }
        }
        return false;
    }
    //根据configid获取他的appkey的值
    public function getAppkeyByConfigid($configid){
        $configid=  is_array($configid)?$configid:(array)$configid;
        $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
        $result=$configtagmodel->find(array("select"=>"type_value","where"=>"ad_config_id in(".  implode(",",$configid).") and type=1","asArray"=>true));
        if(empty($result)){
            return false;
        }
        $appkey=array();
        foreach($result as $key){
            array_push($appkey, $key["type_value"]);
        }
        return $appkey;
    }
    //根据appkey或者channel返回配置信息
   public function getConfigByTypeValue($value,$type=1){
       $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
       $result=$configtagmodel->find(array("select"=>"ad_config_id","where"=>"type_value='".$value."' and type=".$type,"asArray"=>true));
       $adconfigid=array();
       foreach($result as $v){
           array_push($adconfigid, $v["ad_config_id"]);
       }
       return $adconfigid;
   }
   /**
    * 把appkey或channel加入到配置项中
    * **/
  public function addConfig2Tag($ad_config_id,$type_value,$type=1){
      foreach($ad_config_id as $value){
            $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
              //$sql="insert into ad_config_tags(ad_config_id,type,type_value,created,updated) value($config_id,$type,$value,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())\r\n";
              //$data="'',$config_id,$type,$value,".time().",".time()."\r\n";
              //file_put_contents($filename,$data,FILE_APPEND);
            $configtagmodel->ad_config_id=$value;
            $configtagmodel->type=$type;
            $configtagmodel->type_value=$type_value;
            $configtagmodel->updated=time();
            $configtagmodel->created=time();
            //存在则删除,否则添加
            if(!$this->checkTypeValueisExsit($value, $type_value, $type)){
                $configtagmodel->insert();
            }else{
                $this->delTypeValue($type_value,$type);
            }
        }
  }
  /*
   * 审查appkey,channel是否在某个配置中
   */
  public function checkTypeValueisExsit($ad_config_id,$type_value,$type=1){
      if(empty($ad_config_id)){
          return false;
      }
      $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
      $result=$configtagmodel->getOne(array("select"=>"ad_config_id","where"=>"type_value='".$type_value."' and type=".$type,"asArray"=>true));
      if($result["ad_config_id"]==$ad_config_id){
          return true;
      }
      return false;
  }
}

?>
