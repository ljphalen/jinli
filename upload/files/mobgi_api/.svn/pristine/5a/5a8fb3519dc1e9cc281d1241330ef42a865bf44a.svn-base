<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdConfigs
 *
 * @author Stephen.Feng
 */
Doo::loadModel('AppModel');

class AdConfigs  extends AppModel {
    private $mAdconfigodel;
    const  AD_CONFIG_TYPE_VIDEO = 'video';
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->mAdconfigodel=Doo::loadModel("datamodel/AdConfig",TRUE);
    }

    public function getList($configName=null,  $limit = null, $platform=null, $type=null){
        //$pager=Doo::loadHelper("DooPager",TRUE);
        $limitArr = array();
        if ($limit){
            $limitArr = array('limit' => $limit);
        }
        if(!empty($configName)){
             $params['name']  = array('LIKE', $configName );
        }
        if($platform!== '' &&$platform!==NULL && in_array($platform, array(0, 1, 2, 3))){
            $params['platform']  = $platform;
        }
        if($type){
            $params['type']  = $type;
        }else{
            $params['type']  =  array('<>', self::AD_CONFIG_TYPE_VIDEO);
        }
        
        $sqlWhere = $this->sqlWhere($params);
        $adconfigInfo = $this->mAdconfigodel->find(array_merge($limitArr, array('select' => '*',
                                                                                                                                              'where' => $sqlWhere,
                                                                                                                                              "desc"=>"updated", 
                                                                                                                                               'asArray' => TRUE)));       
        
        if(!empty($adconfigInfo)){
            foreach($adconfigInfo as $key=>$value){
                $appChannel=$this->_getAppChannelByConfigId($value["id"]);
                $adconfigInfo[$key]["app"]=$appChannel["app"];
                if ($appChannel['channel']){
                    $channelArr = explode(",", $appChannel['channel']);
                    $adconfigInfo[$key]["channel"] = implode("<br/>", $this->_getChannelById($channelArr));
                }
                $adconfigInfo[$key]["prodcuct"]=$this->_getProductByProductComb($value["product_comb"]);
            }
        }
        return $adconfigInfo;
    }
    
    /**
     * 根据配置名,应用名,渠道名,产品名搜索.
     * @param type $searchKeyword
     * @param type $limit
     * @param type $platform
     * @return type
     */
    public function getListBySearch($searchKeyword=null, $limit = null,  $platform=null,  $type=null){
       
        $searchResult = array();
        $limitArr = array();
      
       if($platform!== '' &&$platform!==null && in_array($platform, array(0, 1, 2, 3))){
            $params['platform']  = $platform;
        }
        if($type){
            $params['type']  = $type;
        }else{
            $params['type']  =  array('<>', self::AD_CONFIG_TYPE_VIDEO);
        }
        $sqlWhere = $this->sqlWhere($params);
        
        $adconfigInfo = $this->mAdconfigodel->find(array_merge($limitArr, array('select' => '*', 'where' => $sqlWhere, 'asArray' => TRUE)));
        if(!empty($adconfigInfo)){
            foreach($adconfigInfo as $key=>$value){
                $app_channel=$this->_getAppChannelByConfigId($value["id"]);
                $adconfigInfo[$key]["app"]=$app_channel["app"];
                if ($app_channel['channel']){
                    $channelArr = explode(",", $app_channel['channel']);
                    $adconfigInfo[$key]["channel"] = implode("<br/>", $this->_getChannelById($channelArr));
                    //搜索渠道需要查询所有的渠道
                    $allChannelArr = explode(",", $app_channel['allchannel']);
                    $adconfigInfo[$key]["allchannel"] = implode("<br/>", $this->_getChannelById($allChannelArr));
                }
                $adconfigInfo[$key]["prodcuct"]=$this->_getProductByProductComb($value["product_comb"]);
            }
        }
        foreach($adconfigInfo as $adconfig_item){
            //根据配置项名称搜索
            if(strpos($adconfig_item['name'],$searchKeyword) !== false){
                $searchResult[] = $adconfig_item;
            }else{
                $searchByAppName =false;
                $searchByChannel =false;
                $searchByProduceName =false;
                //根据应用名称搜索
                foreach($adconfig_item['app'] as $appItem){
                    if(strpos($appItem['app_name'],$searchKeyword) !== false){
                        $searchByAppName = true;
                        break;
                    }
                }
                //根据渠道名称搜索
                if(strpos($adconfig_item['allchannel'],$searchKeyword) !== false){
                   $searchByChannel = true; 
                }
                //根据产品名称搜索
                foreach($adconfig_item['prodcuct'] as $productItem){
                    if(strpos($productItem['product_name'],$searchKeyword) !== false){
                        $searchByProduceName = true;
                        break;
                    }
                }
                if($searchByAppName || $searchByChannel || $searchByProduceName){
                    $searchResult[] = $adconfig_item;
                }
            }
        }
        $result = array();
        $result['total'] = count($searchResult);
        $result['list'] = $searchResult;
        return $result;
    }

    public function getCount($configName = null, $platform = null, $type = null){
          if(!empty($configName)){
             $params['name']  = array('LIKE', $configName );
        }
        if($platform!== '' &&$platform!==NULL && in_array($platform, array(0, 1, 2, 3))){
            $params['platform']  = $platform;
        }
        if($type){
            $params['type']  = $type;
        }else{
            $params['type']  =  array('<>', self::AD_CONFIG_TYPE_VIDEO);
        }
        $sqlWhere = $this->sqlWhere($params);
        $adconfigInfoCount = $this->mAdconfigodel->count(array('select' => '*', 'where' => $sqlWhere, 'asArray' => TRUE));
        return $adconfigInfoCount;
    }

    public function getView($configid){
        $adresult=$this->mAdconfigodel->getOne(array('select' => '*', 'where' => 'id=\''.$configid.'\'', 'asArray' => TRUE));
        if(empty($adresult))return false;
        $adresult["product_combjson"]=$adresult["product_comb"];
        $adresult["product_comb"]=$this->_getProductByProductComb($adresult["product_comb"]);
        $adresult["config_detail_condition"]=$this->__getConfigDetailById($adresult["config_detail_id"]);
        return $adresult;
    }
    /*
     * 删除
     */
    public function del($configid){
        $this->mAdconfigodel->id=$configid;
        $this->mAdconfigodel->delete();
        return true;
    }
    /*
     * 更新
     */
    public function upd($name,$desc,$product_comb,$config_detail_id=null,$config_detail_type,$config_type,$platform,$id=null){
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $adconfig->name=$name;
        $adconfig->config_desc=$desc;
        $adconfig->product_comb=$product_comb;
        $adconfig->config_detail_id=$config_detail_id;
        $adconfig->type=$config_type;
        $adconfig->platform=$platform;
        $adconfig->config_detail_type=$config_detail_type;
        $adconfig->updated=  time();
        if(empty($id)){
            $adconfig->created=  time();
            return $adconfig->insert();
        }
        $adconfig->id=$id;

        $adconfig->update();
        $this->_set_pid_in_show();
        return true;
    }
    
    public function findAll($id=NUll){
        $where=array('select' => '*' ,'asArray' => TRUE);
        if(!empty($id)){
            $where["where"]="id in (".$id.")";
        }
        $adresult=$this->mAdconfigodel->find($where);
        if(empty($adresult))return false;
        return $adresult;
    }
    
    /**
     * 替换Pid
     * @param unknown $product_comb
     * @param unknown $id
     * @return boolean
     */
    public function exchangepid($product_comb, $id){
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $adconfig->product_comb=$product_comb;
        $adconfig->id=$id;
        $adconfig->update();
        return true;
    }
    /*
     *根据appkey,channel_id生成条件过滤文件信息
     *@param $appkey array
     *@param $channel array
     *@param $configvalue string
     */
    public function CreateAppkeyChannelConfigContents($appkey,$channel,$configvalue){
        $channel=is_array($channel)?$channel:(array)$channel;
        $appkey=is_array($appkey)?$appkey:(array)$appkey;
        $push_json_path=Doo::conf()->PUSH_JSON_PATH;//pushjson
        if(empty($appkey) || empty($channel)){
            return false;
        }
        if(empty($configvalue)){
            $config_contents="[]";
        }else{
            //{"config_value":[{"oper_type":"start_times_addup","operator":">=","compare":"3"}]}
            $template=Doo::conf()->PUSH_JSON_TEMPLATE;
            if(empty($template)){
                $config_contents="[]";
            }else{
                $configvalue=  json_decode($configvalue,true);
                if(!empty($configvalue["config_value"])){
                    $configvalue=$configvalue["config_value"];
                    $con=Doo::loadModel("ConditionManages",TRUE);
                    foreach($configvalue as $item){
                         $condition=$con->getConditionByid($item["oper_type"]);
                         if(!empty($condition)){
                            $config_contents.=sprintf($template,$condition["type"],$item["oper_type"],$condition["name"],  str_replace("\"","'",$condition["value"]),$item["operator"],$item["compare"]).",";
                         }
                    }

                    $config_contents="[".trim($config_contents,",")."]";
                }else{
                    $config_contents="[]";
                }
            }
        }
        foreach ($appkey as $key){
            system("mkdir -p ".$push_json_path.$key);
            foreach ($channel as $c){
                file_put_contents($push_json_path.$key."/".$c.".json",$config_contents);
            }
        }
        $this->CreatePushConfigJson();
        return true;
    }
    //生成总的配置文件pushconfig.json
    public function CreatePushConfigJson(){
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $result=$adconfig->find(array("select"=>"id","where"=>"type='push'","asArray"=>true));
        $configid=array();
        if(!empty($result)){
            foreach($result as $id){
                array_push($configid,$id["id"]);
            }
            $configtagmodel=Doo::loadModel("datamodel/AdConfigTags",TRUE);
            $appkeylist=$configtagmodel->find(array("select"=>"type_value","where"=>"type=1 and ad_config_id in(".  implode(",",$configid).")","asArray"=>true));
            if(!empty($appkeylist)){
                foreach($appkeylist as $appkey){
                    $appkeyl.='"'.$appkey["type_value"].'",';
                }
                $otherconfig=Doo::loadModel("datamodel/AdOtherConfig",TRUE);
                $intval=$otherconfig->getOne(array("select"=>"config_detail","where"=>"type='pushcdnset' and channel_id='CURRENT00000'","asArray"=>true));
                if(!empty($intval)){
                    $intval=  json_decode($intval["config_detail"],true);
                    $intval=$intval["pushcdn"];
                }
                $push_interval=$intval?$intval:0;
                $contents=sprintf(Doo::conf()->PUSH_CONFIG_JSON_TEMPLATE,trim($appkeyl,","),$push_interval);
            }
        }else{
            $contents=sprintf(Doo::conf()->PUSH_CONFIG_JSON_TEMPLATE,"",Doo::conf()->PUSH_INTERVAL);
        }
        
        system("mkdir -p ".Doo::conf()->PUSH_PATH_CONFIG);
        $res=file_put_contents(Doo::conf()->PUSH_PATH_CONFIG."pushconfig.json",$contents);
        if($res){
            return true;
        }else{
            file_put_contents("/tmp/push.error","[".date("Y-m-d H:i:s")."]create pushconfig error");
        }

    }
    public function delpushFile($configid){
        $configtags=Doo::loadModel("datamodel/AdConfigTags",TRUE);
        $tagsinfo=$configtags->find(array('select' => 'type_value', 'where' => 'type=1 and ad_config_id='.$configid, 'asArray' => TRUE));
        $dir=Doo::conf()->PUSH_JSON_PATH;
        if(!empty($tagsinfo)) {
            foreach($tagsinfo as $info){
                if(is_dir($dir.$info["type_value"])){
                    system("rm -rf ".$dir.$info["type_value"]);
                }
            }
        }
    }


    //判断某一类型的appkey和channelid是否被使用过?
    public function checkisUseed($id){
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $adconfig->config_detail_type=-2;

    }
    /*
     * 添加
     */
    public function add(){
    }
    public function getChannelInfoByChannelid($channel_id){
        $channel_id=  explode(",", $channel_id);
        $channel=$this->_getChannelById($channel_id);
        return $channel;
    }
    
    /**********以下为私有方法*********/
    private function _getAppChannelByConfigId($configid){
        $adconfigtags=Doo::loadModel("datamodel/AdConfigTags",TRUE);
        $appinfo=$adconfigtags->find(array('select' => '*', 'where' => 'ad_config_id='.$configid, 'asArray' => TRUE));
        if(!empty($appinfo)){
            $appkey=array();
            $channel_id=array();
            foreach ($appinfo as $value){
                if($value["type"]==1){//应用
                    array_push($appkey,$value["type_value"]);
                }
                if($value["type"]==2){//渠道
                    array_push($channel_id, $value["type_value"]);
                }
                continue;
            }
            if(empty($appkey) || empty($channel_id)){
                return false;
            }

            $appinfo=$this->_getAppByAppId($appkey);
            //$channel=$this->_getChannelById($channel_id);
            $all_channel_id = $channel_id;
            if(sizeof($channel_id)>10){
                $channel_id=array_slice($channel_id,0,10);//最多取十个
                array_push($channel_id,"。。。。。。");
            }
            foreach($appinfo as $key=>$item){
                $appinfo[$key]['platformCn'] = $this->getPlatformCn($item['platform']);
            }
            return array("app"=>$appinfo,"channel"=>  implode(",",$channel_id), 'allchannel'=> implode(",",$all_channel_id));
        }
        return false;
    }
    /*
     *通过appid获取app信息
     *@param $appid array
     */
    private function _getAppByAppId($appid){
        $appid=implodeSqlstr($appid);

        $app=Doo::loadModel("datamodel/AdApp",TRUE);
        $appinfo=$app->find(array('select' => 'app_name,appkey,platform', 'where' => 'appkey in('.$appid.')', 'asArray' => TRUE));
        return $appinfo;
    }
    /*
     * 获取广告配置里的产品通过product_comb的值
     */
    private function _getProductByProductComb($productcomb){
        if(empty($productcomb)){
            return false;
        }
        $productinfo=$this->_parseProductComb($productcomb);
        if(empty($productinfo)){//{"products":[]}
            return ;
        }
        if(!empty($productinfo) && is_array($productinfo)){

            $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
            $product_id=array();
            foreach($productinfo as $value){
                array_push($product_id,$value["productid"]);
            }
            //合并数组,缺少percent 要做的
            if(!empty($product_id)){
                $products=$product->find(array('select' => 'id,product_name,product_icon,platform,promote_type', 'where' => 'id in('.implode(",",$product_id).')','asc'=>'FIND_IN_SET(id,"'.implode(",",$product_id).'")', 'asArray' => TRUE));
            }
            foreach ($products as $key=>$value){
                foreach ($productinfo as $v2){
                    if($value["id"]==$v2["productid"]){
                        $products[$key]["percent"]=$v2["percent"];
                    }
                }
                $products[$key]['platformCn'] = $this->getPlatformCn($value['platform']);
            }
        }
        return $products;
    }
    /*
     * 获取广告配置里的产品
     */
    private function _getProductById($configid){
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $configinfo=$adconfig->find(array('select' => 'product_comb', 'where' => 'id=\''.$configid.'\'','asArray' => TRUE));
        if(!empty($configinfo)){
            return $configinfo;
        }
        $productid=$this->_parseProductComb($configinfo);

        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $productinfo=$product->find(array('select' => '*', 'where' => 'id='.  implode(",",$productid), 'asArray' => TRUE));
        return $productinfo;
    }
    /*
     * 通过$product_comb获取详细产品信息
     */
    private function _parseProductComb($product_comb){
        if(empty($product_comb)){
            return false;
        }
        $product=  json_decode($product_comb,true);
        if(!empty($product))return $product["products"];
        return $product;
    }
    private function __getConfigDetailById($detailid){
        $detail=Doo::loadModel("datamodel/AdConfigDetails",TRUE);
        $detailinfo=$detail->getOne(array('select' => 'id,type,type_value as value', 'where' => 'id=\''.$detailid.'\'', 'asArray' => TRUE));
        if(empty($detailinfo)){return false;}
        if(!empty($detailinfo["value"])){
            $config_value=json2array($detailinfo["value"]);
            if(is_array($config_value)){
                $detailinfo["json2value"]=$config_value["config_value"];
            }
        }
        return $detailinfo;
    }
    //将目前在推的pid压入redis
    private function _set_pid_in_show(){
        $key="H:PID_IN_SHOW";
        $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $result=$adconfig->find(array('select' => 'product_comb', 'asArray' => TRUE));
        Doo::db()->query("delete from  mobgi_api.ad_pid_in_show")->execute();
        $this->res("CACHE_REDIS_SERVER_1")->delete($key);
        if(!empty($result)){
            foreach ($result as $v){
                $pcomb=$this->_parseProductComb($v["product_comb"]);
                foreach ($pcomb as $pid){
                    if($pid["percent"]!=0){
                        $acounting=Doo::db()->query("SELECT pid,'CPC'acounting_method,CASE acounting_method WHEN 2 THEN denominated WHEN 3 THEN denominated*0.04 WHEN 4 THEN denominated*0.08 WHEN 5 THEN denominated*0.04 END AS denominated  FROM `ad_product_acounting` WHERE pid='".$pid["productid"]."' AND acounting_method NOT IN(1,6) and `month`='".date("Ym")."' ORDER BY MONTH DESC LIMIT 1")->fetch();
                        $productinfo=$product->getOne(array('select' => 'id,product_package,profit_margin', 'where' => 'id='.$pid["productid"], 'asArray' => TRUE));
                        Doo::db()->query("insert into mobgi_api.ad_pid_in_show(id,product_package,acounting_method,denominated,profit_margin) value(".$productinfo["id"].",'".$productinfo["product_package"]."','".$acounting["acounting_method"]."','".$acounting["denominated"]."',".$productinfo["profit_margin"].") ON DUPLICATE KEY UPDATE id='".$productinfo["id"]."',product_package='".$productinfo["product_package"]."',acounting_method='".$acounting["acounting_method"]."',denominated='".$acounting["denominated"]."',profit_margin=".$productinfo["profit_margin"])->execute();
                        $this->res("CACHE_REDIS_SERVER_1")->hset($key,$productinfo["product_package"],$pid["productid"]);
                    }
                }
            }
        }
    }
    
    public function getPlatformCn($platform){
        if($platform=='' || $platform=='0'){
            return "(T)";
        }else if($platform == "1"){
            return '(A)';
        }else if($platform == '2'){
            return '(I)';
        }
    }
}

?>
