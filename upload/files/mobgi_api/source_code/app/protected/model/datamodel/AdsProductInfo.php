<?php
Doo::loadModel('datamodel/base/AdsProductInfoBase');

class AdsProductInfo extends AdsProductInfoBase{
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("ads");
    }
    public function getCount($keyword,$platform=""){
        $product=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
        if(isset($platform)&& $platform!=""){
            $platformSql=" and platform=${platform}";
        }
        $lists=$product->count(array("where"=>'product_name like "%'.$keyword.'%" and id in('.implode(",",Doo::session()->__get("role_productid")).') '.$platformSql,'asArray' => TRUE));
        return $lists;
    }
    public function productlist($keyword, $limit=NULL,$platform=""){//产品列表
        $lists=$this->_getPrdocutlists($keyword, $limit, $platform);
        if(empty($lists)){
            return $lists;
        }
        foreach ($lists as $key=>$product){
            $lists[$key]['limit']=$this->_adlimit($product["id"]);
            $lists[$key]['adcontent']=$this->_adStat($product["id"]); 
        }
        return $lists;
    }
    /*
     * 获取未审核的text列表
     */
    public function getAdsProductCheck($ischeck=array(1),$where){
        $whereSql=$this->_conditions($where);
        $sql="select id,product_name,`ischeck`,owner,created,platform from mobgi_ads.ads_product_info where ischeck in(".  implode(",",$ischeck).") and id in(".  implode(",",Doo::session()->__get("role_productid")).") and $whereSql";
        return Doo::db()->query($sql)->fetchAll();
        //return $this->find(array("select"=>"product_name,`ischeck`,owner,createtime","where"=>"`ischeck`!=1","asArray"=>TRUE));
    }
    /*
     * 返回某一产品的详细信息
     * @param $productid如果为空则返回所有
     */
    public function view($where){
        if(empty($where["id"]))return false;
        $productid=$where["id"];
        $whereSql=$this->conditions($where,  array_keys($where));
        $where=array('select'=>'*','where'=>$whereSql,'asArray' => TRUE);
        $lists=$this->getOne($where);
        
         $lists['json_conf'] = json_decode($lists['json_conf'], true);
         $lists['video_product_url']= $lists['json_conf']['video_product_url'];
         $lists['video_again']= $lists['json_conf']['video_again'];
         $lists['video_show_close_btn']= $lists['json_conf']['video_show_close_btn'];
         $lists['video_show_download_btn']= $lists['json_conf']['video_show_download_btn'];
         $lists['video_play_per']= $lists['json_conf']['video_play_per'];
         $videoClickTypeObj =$lists['json_conf']['videoclictypeobj']? json_encode($lists['json_conf']['videoclictypeobj']):'';
         $lists['video_click_type_object']= $videoClickTypeObj;
        if(!empty($lists)){;
            //$lists["click_type_object"]=  json2array($lists["click_type_object"]);
            $ad_limit_stat=$this->_adlimit($productid);
            $lists["click_type_array"]= json_decode($lists["click_type_object"],true);
            $lists["limit"]=$ad_limit_stat["ad_stat_limit"];
            $lists["plan"]=$ad_limit_stat["ad_stat_plan"];
            $lists["limitjson"]=$ad_limit_stat["ad_stat_limit_json"];
            $lists["planjson"]=$ad_limit_stat["ad_stat_plan_json"];
            #$ad=Doo::loadModel("Ad",TRUE);
           # $lists["ad"]=$ad->getAdinfoForSubtype($productid);
           // $lists["embd"]=$ad->getAdinfo($productid,1);
        }
        $adsinfo=Doo::loadModel("datamodel/AdsInfo", TRUE);
        $lists["ads"]=$adsinfo->getResourceByproductid($productid);
        $lists["videoAds"]=$adsinfo->getVedioResourceByproductid($productid);
        return $lists;
    }
    
    public function fillVedioJsonConf($post){
       
        $vedio = array();
        $vedio['video_product_url']  =  $post['video_product_url'];
        $vedio['video_product_url']  =  $post['video_product_url'];
        $vedio['video_again']  =  $post['video_again'];
        $vedio['video_show_close_btn']  =  $post['video_show_close_btn'];
        $vedio['video_show_download_btn']  =  $post['video_show_download_btn'];
        $vedio['video_play_per']  =  $post['video_play_per'];
        $clickTypeObj =  json_decode($post['videoclictypeobj'], true);
        $vedio['videoclictypeobj']  =  $clickTypeObj?$clickTypeObj:'';
        
        return json_encode($vedio);
    }
    
    /*
     * 将引用的素材保存至审核表
     */
    public function saveResource2product($post){
        
      
        $product=$this->getOne(array('select' => 'id', 'where' => 'id=\''.$post["id"].'\'','asArray' => TRUE));
        $clictypeobj = stripslashes($post["clictypeobj"]);
        $this->product_icon=$post["picon"];
        $this->product_name=$post["pname"];
        $this->appkey=$post["pappkey"];
        $this->product_desc=$post["pdesc"];
        $this->product_version=$post["product_version"];
        $this->product_package=$post["ppackage"];
        $this->product_url=$post["purl"];
        $this->platform=$post["platform"];
        $this->click_type_object=$clictypeobj;
        $this->id=$post["id"];
        $this->ischeck=$post["ischeck"];
        $this->checker=$post["checker"];
        $this->owner=$post["owner"];
        $this->check_msg=$post["check_msg"];
        $this->star=$post["star"];
        $this->playering=$post["playering"];
        $this->promote_type=$post["promote_type"];
        $this->profit_margin=$post["profit_margin"];
        $this->updated=time();
        $this->ios_identify=$post['ios_identify'];
        $this->json_conf= $this->fillVedioJsonConf($post);
        
        
        if(empty($product)){
            $this->created=time();
            $post["id"]=$this->insert();
        }else{
            $this->update();
        }
//        if(!empty($post["picid"]) || !empty($post["textid"]) || !empty($post["pushid"]) || !empty($post["htmlid"]) || !empty($post["videoid"])){
            $ad = Doo::loadModel("datamodel/AdsInfo", TRUE);
            $ad->upd($post);
//        }
        return $post["id"];
    }
    public function check_save($post){
        $adsinfo=Doo::loadModel("datamodel/AdsInfo",TRUE);      
        #try{
            $this->_resource2productinfo($post["id"]);//产品素材入库
            $adsinfo->resource2adsinfo($post);
            $limit=Doo::loadModel("datamodel/AdsProductLimit",TRUE);
            $limit->resourcelimit2limit($post["id"]);
        #}  catch (Exception $e){
        #    return false;
        #}
        $this->platform=$post["platform"];
        $this->ischeck=$post["ischeck"];
        $this->checker=$post["checker"];
        $this->owner=$post["owner"];
        $this->check_msg=$post["check_msg"];
        $this->updated=time();
        $this->id=$post["id"];
        $this->updated();
        return true;
    }
    /**************以下是私有方法***************/
    /*
     * 将产品素材入库
     */
    private function _resource2productinfo($id){

        $sql = 'select id,product_name,appkey,product_icon,product_desc,product_url,click_type_object,product_version,product_package,star,playering,promote_type,profit_margin,ios_identify,json_conf from ads_product_info where id ='.$id;
        $product_info = Doo::db()->query($sql)->fetch();
        $sql="INSERT INTO mobgi_api.`ad_product_info` (id,product_name,appkey,product_icon,product_desc,product_url,click_type_object,product_version,product_package,star,playering,promote_type,profit_margin,ios_identify,json_conf)"
                . " VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s','%s') ON DUPLICATE KEY UPDATE id=%d,product_name='%s',appkey='%s',product_icon='%s',product_desc='%s',product_url='%s',click_type_object='%s',product_version='%s',product_package='%s',star='%s',playering='%s',promote_type='%d',profit_margin='%s',ios_identify='%s',json_conf='%s'";
        $sql=  sprintf($sql,$product_info["id"],$product_info["product_name"],$product_info["appkey"],$product_info["product_icon"],$product_info["product_desc"],$product_info["product_url"],
                $product_info["click_type_object"],$product_info["product_version"],$product_info["product_package"],$product_info["star"],$product_info["playering"],$product_info["promote_type"],$product_info["profit_margin"],$product_info["ios_identify"],$product_info["json_conf"],$product_info["id"],$product_info["product_name"],$product_info["appkey"],$product_info["product_icon"],$product_info["product_desc"],$product_info["product_url"],
                $product_info["click_type_object"],$product_info["product_version"],$product_info["product_package"],$product_info["star"],$product_info["playering"],$product_info["promote_type"],$product_info["profit_margin"],$product_info["ios_identify"],$product_info["json_conf"]);
     
     
        Doo::db()->query($sql)->execute();
        //更新到ad_pid_in_show
        Doo::db()->query("update mobgi_api.ad_pid_in_show set profit_margin='".$product_info["profit_margin"]."' where id=".$id)->execute();
        
    }
    private function _getPrdocutlists($keyword, $limit=NULL, $platform=''){
        $product=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
        $limitArr = array();
        if ($limit){
            $limitArr = array('limit' => $limit);
        }
        if (isset($platform) && $platform!=""){
            $platformSql=" and platform=". $platform;
        }
        $lists=$product->find(array_merge(array("where"=>'product_name like "%'.$keyword.'%"  and id in('.implode(",",Doo::session()->__get("role_productid")).') '.$platformSql,"desc"=>"created",'asArray' => TRUE), $limitArr));
        return $lists;
    }
    private function _adlimit($productid){
        $limit=Doo::loadModel("datamodel/AdsProductLimit",TRUE);
        $limitinfo=$limit->getOne(array('select' => 'ad_stat_limit,ad_stat_plan', 'where' => 'ad_product_id=\''.$productid.'\'','asArray' => TRUE));
        if(!empty($limitinfo)){
            $limitinfo["ad_stat_limit_json"]= $limitinfo["ad_stat_limit"];
            $limitinfo["ad_stat_plan_json"]= $limitinfo["ad_stat_plan"];
            $limitinfo["ad_stat_limit"]= json2array($limitinfo["ad_stat_limit"]);
            $limitinfo["ad_stat_plan"]= json2array($limitinfo["ad_stat_plan"]);

        }
        return $limitinfo;
    }
    private function _adStat($productid){//显示广告的数量
        $adinfo=Doo::loadModel("datamodel/AdsInfo",TRUE);
        $adlist=$adinfo->find(array('select' => 'id,type', 'where' => 'ad_product_id=\''.$productid.'\'','asArray' => TRUE));
        $adinfoid_nembed=array();
        $adinfoid_embed=array();
        $adinfoid_push=array();
        $adcontent["nembed"]=0;
        $adcontent["embed"]=0;
        $adcontent["push"]=0;
        $adcontent["custom"]=0;
        $adcontent["video"]=0;
        if(!empty($adlist)){
            foreach($adlist as $key=>$value){
                if($value["type"]==0){
                    $adcontent["nembed"]+=1;
                }
                if($value["type"]==1){
                    $adcontent["embed"]+=1;
                }
                if($value["type"]==2){
                    $adcontent["push"]+=1;
                }
                if($value["type"]==3){
                    $adcontent["custom"]+=1;
                }
                if($value["type"]==4){
                    $adcontent["video"]+=1;
                }
            }
        }
        return $adcontent;
    }

    public function findAll($whereArr){
        $product=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
        $result = $product->find($whereArr);
        return $result;
    }
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "1=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        if (isset($conditions['product_name']) && $conditions['product_name']){
            $where[] = "product_name like  '%".$conditions['product_name']."%'";
        }
        if (isset($conditions['created']) && $conditions['created']){
            $where[] = "created>= ".$conditions['created'];
        }
        if (isset($conditions['screatedate']) && $conditions['screatedate']){
            $where[] = "created>= ".$conditions['screatedate'];
        }
        if (isset($conditions['ecreatedate']) && $conditions['ecreatedate']){
            $where[] = "created<= ".$conditions['ecreatedate'];
        }
        if (isset($conditions['platform']) && in_array($conditions['platform'], array(0,1,2))){
            $where[] = "platform = ".$conditions['platform'];
        }
        if(empty($where)){
            return "1=1";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}