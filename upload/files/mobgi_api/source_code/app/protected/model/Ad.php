<?php

/**
 * Description of Ad
 *
 * @author Stephen.Feng
 */
Doo::loadModel('AppModel');
class Ad extends AppModel{
    private $adinfo_model;
    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->adinfo_model=Doo::loadModel("datamodel/AdInfo",TRUE);
    }
    public function getView($type,$ad_id){
        $addata = $this->adinfo_model->getOne(array('select' => '*', 'where' => 'id =\''.$ad_id.'\'', 'asArray' => TRUE));
        if(!empty($addata)){
            if($type==0){//抢占式广告
                $subad=$this->getAdNotEmbeddedinfo($ad_id);
            }
            if($type==1 || $type==2){//嵌入式广告
                $subad=$this->getAdEmbeddedinfo($ad_id);
            }
            $addata["addetail"]=$subad;
            #$addata["click_type_array"]=json_decode($addata["ad_click_type_object"],true);
        }
        return $addata;
    }
    public function addAdinfo($ad_product_id,$ad_name,$desc,$state,$type,$ad_pos,$show_detail,$ad_click_type_object,$ad_target,$ad_id=null){//增加广告
        $ad=Doo::loadModel("datamodel/AdInfo",TRUE);
        $ad->ad_product_id=$ad_product_id;
        $ad->ad_name=$ad_name;
        $ad->ad_desc=$desc;
        $ad->state=$state;
        $ad->type=$type;
        $ad->show_detail=$show_detail;
        $ad->ad_click_type_object=$ad_click_type_object;
        $ad->pos=$ad_pos;
        $ad->ad_target=$ad_target;
        $ad->created=time();
        $ad->updated=time();
        $this->delAdFromRedis($ad_id."_ad_info_pos");
        if(empty($ad_id)){
            return $ad->insert();
        }else{
            $ad->id=$ad_id;
            $ad->update();
           return $ad_id;
        }
    }
    //删除广告,包括抢占式,嵌入式两个广告里的信息
    public function delad($ad_info_id,$type,$subtype,$scrrent_type){
        $adinfo=Doo::loadModel("datamodel/AdInfo",TRUE);
        $adinfo->id=$ad_info_id;
        $adinfo->delete();
        subtype($type);
        if($type=="0"){//抢占式
            $sql="delete from ad_not_embedded_info where ad_info_id='$ad_info_id'";
            Doo::db()->query($sql)->execute();
            $keys[]=$ad_info_id."_".$subtype."_0_notembeddedinfo";
            $keys[]=$ad_info_id."_".$subtype."_1_notembeddedinfo";
            $keys[]=$ad_info_id."_".$subtype."_2_notembeddedinfo";
            $this->delAdFromRedis($keys);
        }
        if($type=="1"){//抢占式
            $sql="delete from ad_embedded_info where ad_info_id='$ad_info_id'";
            Doo::db()->query($sql)->execute();
            $this->delAdFromRedis($ad_info_id."_".$subtype."_notembeddedinfo");
        }
        return true;
    }
    public function updAdStateRate($ad_info_id,$product_id,$state,$rate,$type){//修改广告的状态和分配比例
        //广告修改的时候此处只需要修改ad_info的state和抢占式,嵌入式里的rate
        //新增的时候此处此处需要修改ad_info的state,product_id和嵌入式,抢占式里的的rate
        $adinfo=Doo::loadModel("datamodel/AdInfo",TRUE);
        $adinfo->id=$ad_info_id;
        $adinfo->ad_product_id=$product_id;
        $adinfo->state=$state;
        $adinfo->update();
        if($type=="0" || $type=="nembd"){//抢占式
            $table="ad_not_embedded_info";
        }
        if($type=="1" || $type=="2" || $type=="embd"){//嵌入式
            $table="ad_embedded_info";
        }
        $sql="update $table set rate='$rate' where ad_info_id='$ad_info_id'";
        Doo::db()->query($sql)->execute();
        
        return false;
    }
    public function updAdNotEmbeddedinfo($type,$ad_pic_url,$screen_ratio,$show_time,$screen_type,$rate=0,$ad_info_id,$id=null,$resolution=null,$close_wait=0){//增加抢占式广告
        $nembed=Doo::loadModel("datamodel/AdNotEmbeddedInfo",TRUE);
        $nembed->type=$type;
        $nembed->ad_pic_url=$ad_pic_url;
        $nembed->screen_ratio=empty($screen_ratio)?0:$screen_ratio;
        $nembed->show_time=$show_time;
        $nembed->screen_type=$screen_type;
        $nembed->resolution=$resolution;
        $nembed->rate=$rate;
        $nembed->close_wait=$close_wait;
        $nembed->ad_info_id=$ad_info_id;
        $nembed->created=time();
        $nembed->updated=time();
        if(empty($id)){
            return $nembed->insert();
        }else{
            $nembed->id=$id;
            $keys[]=$ad_info_id."_".$type."_0_notembeddedinfo";
            $keys[]=$ad_info_id."_".$type."_1_notembeddedinfo";
            $keys[]=$ad_info_id."_".$type."_2_notembeddedinfo";
            $this->delAdFromRedis($keys);
            return $nembed->update();
        }
    }
    public function updAdEmbeddedinfo($type,$ad_pic_url,$ad_name,$ad_desc,$screen_ratio,$rate=0,$ad_info_id,$id=null,$resolution=null){//增加嵌入式广告
        $embed=Doo::loadModel("datamodel/AdEmbeddedInfo",TRUE);
        $embed->type=$type;
        $embed->ad_pic_url=$ad_pic_url;
        $embed->ad_name=$ad_name;
        $embed->ad_desc=$ad_desc;
        $embed->screen_ratio= empty($screen_ratio)?0:$screen_ratio;
        $embed->resolution=$resolution;
        $embed->rate=$rate;
        $embed->ad_info_id=$ad_info_id;
        $embed->created=time();
        $embed->updated=time();
        if(empty($id)){
           return $embed->insert();
        }else{
            $embed->id=$id;
            $keys=$ad_info_id."_".$type."_embeddedinfo";
            $this->delAdFromRedis($keys);
            return $embed->update();
        }
    }
    public function updAdCustomizedinfo($type,$ad_pic_url,$ad_desc,$screen_ratio,$rate=0,$ad_info_id,$id=null,$resolution=null){//增加嵌入式广告
        $custm=Doo::loadModel("datamodel/AdCustomizedInfo",TRUE);
        $custm->type=$type;
        $custm->ad_pic_url=$ad_pic_url;
        $custm->ad_desc=$ad_desc;
        $custm->screen_ratio= empty($screen_ratio)?0:$screen_ratio;
        $custm->resolution=$resolution;
        $custm->rate=$rate;
        $custm->ad_info_id=$ad_info_id;
        $custm->created=time();
        $custm->updated=time();
        if(empty($id)){
           return $custm->insert();
        }else{
            $custm->id=$id;
            $keys=$ad_info_id."_".$type."_customizedinfo";
            $this->delAdFromRedis($keys);
            return $custm->update();
        }
    }
//    public function delAdEmbeddedinfo($ad_id){//
//        $embed=Doo::loadModel("datamodel/AdEmbeddedInfo",TRUE);
//        $embed->id=$ad_id;
//        $embed->delete();
//        $this->delAdFromRedis($key);
//        return true;
//    }
//    public function delAdNotEmbeddedinfo($ad_id){//
//        $nembd=Doo::loadModel("datamodel/AdNotEmbeddedInfo",TRUE);
//        $nembd->id=$ad_id;
//        $nembd->delete();
//        return true;
//    }
    public function getAdinfoForSubtype($product_id){//根据productid按分类返回所有类型广告
        $adcate=Doo::conf()->AD_TYPE_CATE;
        $ad=array();
        if(!empty($adcate)){
            foreach($adcate as $key=>$value){
                foreach($value["subtype"] as $skey=>$svalue){//$skey  0=>"半屏",1=>"全屏"
                    $subadinfo=$this->getAdinfo($product_id,$key,$skey);//抢占
                    if(!empty($subadinfo)){
                        //$subnotembinfo["subtypename"]=$adcate[$key]["subtype"][$skey];
                        $ad[$key][$skey]=$subadinfo;
                    }
                }
//                if($key==1 || $key=2){//嵌入式和PUSH广告
//                    $embinfo_ad=array();
//                    foreach($value["subtype"] as $eskey=>$esvalue){//$eskey  0=>"Banner横幅图片广告",1=>"Banner横幅文字广告", 2=>"插页广告",3=>"浮窗广告"
//                        $subembinfo=$this->getAdinfo($product_id,$key,$eskey);//嵌入
//                        if(!empty($subembinfo)){
//                            //$subembinfo["subtypename"]=$adcate[$key]["subtype"][$eskey];
//                            array_push($embinfo_ad,$subembinfo);
//                        }
//                    }
//                }
                //$ad=array("0"=>$notembinfo_ad,"1"=>$embinfo_ad);
            }
        }
        return $ad;
    }
    /*
     * 通过productid获取广告
     * @parduct_id 产品id
     * @param:type 广告类型,比如抢占式,嵌入式
     * @param:subtype 广告子类型 比如抢占式的全屏半屏,嵌入式的插页,浮窗等
     * @autor:Stephen.Feng
     */
    function getAdinfo($product_id,$type,$subtype=null){
        $addata = $this->adinfo_model->find(array('select' => 'id as ad_info_id', 'where' => 'ad_product_id =\''.$product_id.'\' and type=\''.$type.'\'', 'asArray' => TRUE));
        $result=array();
        if(!empty($addata)){
            $ad_id=array();
            foreach ($addata as $value){array_push($ad_id, $value["ad_info_id"]);}
            switch ($type){
                case 0://抢占式
                    $adnoteminfo=$this->getAdNotEmbeddedinfo($ad_id,$subtype);
                    $result=$adnoteminfo;
                    break;;
                case 1:case 2://嵌入式
                    $adembinfo=$this->getAdEmbeddedinfo($ad_id,$subtype);
                    $result=$adembinfo;
                    break;
                case 3://自定义广告
                    $ctminfo=$this->getAdCustomizedinfo($ad_id,$subtype);
                    $result=$ctminfo;
                    break;
                default:
                    break;
            }
            //unset($result["id"]);
        }
        return $result;
    }
    /*获取抢占是广告列表
     * @apram:$ad_id Array
     * @autor:Stephen.Feng
     */
    function getAdNotEmbeddedinfo($ad_id,$subtype=null){
        $wheresub='';
        if(!empty($subtype) || $subtype===0){
            $wheresub=' and b.type=\''.$subtype.'\'';
        }
        if(!is_array($ad_id)){$ad_id=(array)$ad_id;}
        $adnoeminfo_model=Doo::loadModel("datamodel/AdNotEmbeddedInfo",True);
        $sql="select b.id,b.ad_info_id,b.ad_pic_url,b.type,b.show_time,b.screen_type,b.screen_ratio,b.rate,a.ad_name as ad_title from ad_not_embedded_info b left join ad_info a on a.id=b.ad_info_id where b.ad_info_id in(".  implode(",", $ad_id).") ".$wheresub;
        $adnoemdata=Doo::db()->query($sql)->fetchAll();
        //$adnoemdata = $adnoeminfo_model->find(array('select' => 'id,ad_info_id,ad_pic_url,type,show_time,screen_type,screen_ratio,rate', 'where' => 'ad_info_id in('.  implode(",", $ad_id).') '.$wheresub, 'asArray' => TRUE));
        if(!empty($adnoemdata)){
            foreach($adnoemdata as $key=>$ad){
                $adnoemdata[$key]["state"]=$this->_getAdState($ad["ad_info_id"]);
            }
        }
        return $adnoemdata;
    }
    
    /*获取嵌入广告
     * @apram:$ad_id Array
     * @autor:Stephen.Feng
     */
    function getAdEmbeddedinfo($ad_id,$subtype=null){
        $wheresub='';
        if(!empty($subtype) || $subtype===0){
            $wheresub=' and b.type=\''.$subtype.'\'';
        }
        if(!is_array($ad_id)){$ad_id=(array)$ad_id;}
        $ademinfo_model=Doo::loadModel("datamodel/AdEmbeddedInfo",True);
        $sql="select b.id,b.ad_info_id,b.ad_pic_url,b.type,b.ad_name,b.ad_desc,b.screen_ratio,b.rate,a.ad_name as ad_title from ad_embedded_info b left join ad_info a on a.id=b.ad_info_id where b.ad_info_id in(".  implode(",", $ad_id).") ".$wheresub;
        $ademdata=Doo::db()->query($sql)->fetchAll();
        //$ademdata = $ademinfo_model->find(array('select' => 'id,ad_info_id,ad_pic_url,type,ad_name, ad_desc,screen_ratio,rate', 'where' => 'ad_info_id in('.implode(",", $ad_id).') '.$wheresub, 'asArray' => TRUE));
        if(!empty($ademdata)){
            foreach($ademdata as $key=>$ad){
                $ademdata[$key]["state"]=$this->_getAdState($ad["ad_info_id"]);
            }
        }
        return $ademdata;
    }
    /*获取自定义广告
     * @apram:$ad_id Array
     * @autor:Stephen.Feng
     */
    function getAdCustomizedinfo($ad_id,$subtype=null){
        $wheresub='';
        if(!empty($subtype) || $subtype===0){
            $wheresub=' and b.type=\''.$subtype.'\'';
        }
        if(!is_array($ad_id)){$ad_id=(array)$ad_id;}
        $sql="select b.id,b.ad_info_id,b.ad_pic_url,b.type,b.show_time,b.screen_type,b.screen_ratio,b.rate,a.ad_name as ad_title,a.ad_desc from ad_customized_info b left join ad_info a on a.id=b.ad_info_id where b.ad_info_id in(".  implode(",", $ad_id).") ".$wheresub;
        $adctmdata=Doo::db()->query($sql)->fetchAll();
        //$adnoemdata = $adnoeminfo_model->find(array('select' => 'id,ad_info_id,ad_pic_url,type,show_time,screen_type,screen_ratio,rate', 'where' => 'ad_info_id in('.  implode(",", $ad_id).') '.$wheresub, 'asArray' => TRUE));
        if(!empty($adctmdata)){
            foreach($adctmdata as $key=>$ad){
                $adctmdata[$key]["state"]=$this->_getAdState($ad["ad_info_id"]);
            }
        }
        return $adctmdata;
    }
    function getAdinfoByAdid($adid){
        return $this->__getAdinfo($adid);
    }
    /**********以下为私有方法********/
    private function _getAdState($ad_id){//根据广告id获取广告状态
        if(empty($ad_id)){
            return false;
        }
        $adstate = $this->adinfo_model->getOne(array('select' => 'state,id', 'where' => 'id =\''.$ad_id.'\'', 'asArray' => TRUE));
        if(!empty($adstate)){
            return $adstate["state"];
        }
        return false;
    }
    //通过adinfoid获取adid
    private function _getIdByAdInfoid($adid){
        $adid = $this->adinfo_model->getOne(array('select' => 'id', 'where' => 'ad_info_id=\''.$adid.'\'', 'asArray' => TRUE));
        if(!empty($adid)){
            return $adid["id"];
        }
        return '';
    }
    //获取adinfo的信息
    private function __getAdinfo($adid){
        $adinfo = $this->adinfo_model->getOne(array('select' => '*', 'where' => 'id=\''.$adid.'\'', 'asArray' => TRUE));
        if(!empty($adinfo)){
            return $adinfo;
        }
        return array();
    }
    private function delAdFromRedis($keys){
        $this->deleter("stat_map","CACHE_REDIS_SERVER_2");//删除redis中用于纠正统计数据pid=aid错误的key
        $this->deleter($keys,"CACHE_REDIS_SERVER_1");
    }
}

?>
