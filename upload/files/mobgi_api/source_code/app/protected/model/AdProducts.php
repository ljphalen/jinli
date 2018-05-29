<?php


/**
 * Description of AdProducts
 *
 * @author Stephen.Feng
 */
Doo::loadModel('AppModel');
class AdProducts extends AppModel {
    public function productlist($keyword,$platform="", $limit=NULL, $customer=""){//产品列表
        $lists=$this->_getPrdocutlists($keyword,$platform, $limit, $customer);
        if(empty($lists)){
            return $lists;
        }
        foreach ($lists as $key=>$product){
            $lists[$key]["limit"]=$this->_adlimit($product["id"]);
            $lists[$key]["adcontent"]=$this->_adStat($product["id"]);
            $lists[$key]["company"]=$this->getCustomerName($product["publishid"]);
        }
        return $lists;
    }

    public function getCount($keyword,$platform, $customer=''){
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $platformStr = ' and 1=1 ';
        $customerStr = '';
        if(!empty($platform) || $platform==="0"){
            $platformStr=" and platform='$platform'";
        }
        if(!empty($customer)){
            $customerStr = ' and publishid='. $customer. " ";
        }
        $lists=$product->count(array("where"=>'product_name like "%'.$keyword.'%" '.$platformStr . $customerStr,'asArray' => TRUE));
        return $lists;
    }

    /*
     * 返回某一产品的详细信息
     * @param $productid如果为空则返回所有
     */
    public function view($productid=null){
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $where=array('select'=>'*','where'=>'id="'.$productid.'"','asArray' => TRUE);
        $lists=$product->getOne($where);
     
        if(!empty($lists)){
            //$lists["click_type_object"]=  json2array($lists["click_type_object"]);
            $ad_limit_stat=$this->_adlimit($productid);
            $lists["click_type_array"]= json_decode($lists["click_type_object"],true);
            $lists["limit"]=$ad_limit_stat["ad_stat_limit"];
            $lists["plan"]=$ad_limit_stat["ad_stat_plan"];
            $lists["limitjson"]=$ad_limit_stat["ad_stat_limit_json"];
            $lists["planjson"]=$ad_limit_stat["ad_stat_plan_json"];
            $lists["planjson"]=$ad_limit_stat["ad_stat_plan_json"];
            $ad=Doo::loadModel("Ad",TRUE);
            $lists["ad"]=$ad->getAdinfoForSubtype($productid);
           // $lists["embd"]=$ad->getAdinfo($productid,1);
        }
        return $lists;
    }
    public function del($productid){
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $product->id=$productid;
        $product->delete();
        $this->deleter("stat_map","CACHE_REDIS_SERVER_2");//删除redis中用于纠正统计数据pid=aid错误的key
        $this->deleter($productid."_product");//删除redis中的值
        return true;
    }
    /*
     * 更新product记录,如果id为空则是更新否则是添加
     */
    public function upd($pname,$picon,$pdesc,$purl,$clicktypeobj,$pversion,$package,$pappkey,$id=null){
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $product->product_name=$pname;
        $product->product_icon=$picon;
        $product->product_desc=$pdesc;
        $product->product_url=$purl;
        $product->click_type_object=$clicktypeobj;
        $product->product_version=$pversion;
        $product->product_package=$package;
        $product->appkey=$pappkey;
        $session=Doo::session()->__get("admininfo");
        $product->oprator=$session["username"];
        $product->updated=time();
        $this->deleter("stat_map","CACHE_REDIS_SERVER_2");//删除redis中用于纠正统计数据pid=aid错误的key
        if(empty($id)){
            $product->created=time();
            return $product->insert();
        }
        $this->deleter($id."_product");//删除redis中的值
        $product->id=$id;
        $product->update();
        return true;
    }
    /**************以下是私有方法***************/

    private function _getPrdocutlists($keyword,$platform, $limit=NULL, $customer=''){
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $limitArr = array();
        if ($limit){
            $limitArr = array('limit' => $limit);
        }
        $platformStr = ' and 1=1 ';
        if(!empty($platform)|| $platform==="0"){
            $platformStr=" and platform='$platform'";
        }
        //根据公司名搜索
        $customerStr = '';
        if(!empty($customer)){
            $customerStr = ' and publishid='. $customer. " ";
        }
        $lists=$product->find(array_merge(array("where"=>'product_name like "%'.$keyword.'%" '.$platformStr. $customerStr,"desc"=>"created",'asArray' => TRUE), $limitArr));
        return $lists;
    }
    private function _adlimit($productid){
        $limit=Doo::loadModel("datamodel/AdProductLimit",TRUE);
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
        $adinfo=Doo::loadModel("datamodel/AdInfo",TRUE);
        $adlist=$adinfo->find(array('select' => 'id,type', 'where' => 'ad_product_id=\''.$productid.'\'','asArray' => TRUE));
        $adinfoid_nembed=array();
        $adinfoid_embed=array();
        $adinfoid_push=array();
        $adinfoid_video=array();
        if(!empty($adlist)){
            foreach($adlist as $key=>$value){
                if($value["type"]==0){
                    array_push($adinfoid_nembed, $value["id"]);
                }
                if($value["type"]==1){
                    array_push($adinfoid_embed, $value["id"]);
                }
                if($value["type"]==2){
                    array_push($adinfoid_push, $value["id"]);
                }
                if($value["type"]==3){
                    array_push($adinfoid_custom, $value["id"]);
                }
                if($value["type"]==4){
                    array_push($adinfoid_video, $value["id"]);
                }
                unset($adlist[$key]);
            }
            $adlist["nembed"]=count($this->_getNotEmbeddedStat($adinfoid_nembed));
            $adlist["embed"]=count($this->_getEmbeddedStat($adinfoid_embed));
            $adlist["push"]=count($this->_getPushStat($adinfoid_push));
            $adlist["custom"]=count($this->_getCustomStat($adinfoid_custom));
            $adlist["video"]  = count($adinfoid_video);
        }
        return $adlist;
    }
    
    private function getCustomerName($customerid){//获取客户名
        $customerModel = Doo::loadModel("datamodel/AdPublish", TRUE);
        $customerInfo = $customerModel->getCustomerByid($customerid);
        return $customerInfo['compay'];
    }
    
    private function _getEmbeddedStat($adid){//抢占式广告各类广告的数量
        $adid=is_array($adid)?$adid:(array)$adid;
        $adid= implode("','", $adid);
        $adid= "'".$adid."'";
        $embed=Doo::loadModel("datamodel/AdEmbeddedInfo",TRUE);
        $result=$embed->find(array("select" => "count(id) as count,case type WHEN 0 then 'Banner横幅图片广告' when 1 then 'Banner横幅文字广告' when 2 then '插页广告' when 3 then '浮窗广告' when 4 then 'banner网页' else '未知类型广告' end as typename, type", "where" => "ad_info_id in(".$adid.")","groupby"=>"type",'asArray' => TRUE));
        return $result;
    }
    private function _getNotEmbeddedStat($adid){//嵌入式广告各类型广告数量
        $adid=is_array($adid)?$adid:(array)$adid;
        $adid= implode("','", $adid);
        $adid= "'".$adid."'";
        $nembed=Doo::loadModel("datamodel/AdNotEmbeddedInfo",TRUE);
        $result=$nembed->find(array('select' => 'count(id) as count,case type WHEN 0 then "半屏" when 1 then "全屏" when 3 then "网页" when 4 then "视频" else "未知类型广告" end as typename,type', 'where' => 'ad_info_id in('.$adid.')','groupby'=>"type",'asArray' => TRUE));
        return $result;
    }
    private function _getPushStat($adid){//嵌入式各类型广告数量
        $adid=is_array($adid)?$adid:(array)$adid;
        $adid= implode("','", $adid);
        $adid= "'".$adid."'";
        $embed=Doo::loadModel("datamodel/AdEmbeddedInfo",TRUE);
        $result=$embed->find(array("select" => "count(id) as count,case type WHEN 0 then '消息' when 1 then 'APP' when 2 then '快捷方式'  else '未知类型广告' end as typename, type", "where" => "ad_info_id in(".$adid.")","groupby"=>"type",'asArray' => TRUE));
        return $result;
    }
    private function _getCustomStat($adid){//自定义广告类型广告数量
        $adid=is_array($adid)?$adid:(array)$adid;
        $adid= implode("','", $adid);
        $adid= "'".$adid."'";
        $custom=Doo::loadModel("datamodel/AdCustomizedInfo",TRUE);
        $result=$custom->find(array('select' => 'count(id) as count,IF(type=0,"信息流","其他") as typename,type', 'where' => 'ad_info_id in('.$adid.')','groupby'=>"type",'asArray' => TRUE));
        return $result;
    }

    public function findAll($whereArr){
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $result = $product->find($whereArr);
        return $result;
    }
    
    /**
     * 根据产品id获取产品信息（push导量权重弹出框要用）
     * @param type $idsArr
     * @return boolean
     */
    public function getProductsByids($idsArr){
        if(empty($idsArr)){
            return false;
        }
        $idsStr = implode(',', $idsArr);
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $lists=$product->find(array("where"=>' id in ('.$idsStr.")",'asArray' => TRUE));
        if(empty($lists)){
            return false;
        }
        foreach ($lists as $key=>$product){
            $lists[$key]["adcontent"]=$this->_adStat($product["id"]);
        }
        return $lists;
    }
    
}

?>
