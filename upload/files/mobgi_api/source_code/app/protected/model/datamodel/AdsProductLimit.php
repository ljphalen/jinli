<?php
Doo::loadModel('datamodel/base/AdsProductLimitBase');

class AdsProductLimit extends AdsProductLimitBase{
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("ads");
    }
    public function upd($ad_limit,$ad_plan,$product_id){//导量信息
        $adlimit_id=$this->_getProductLimitIdByProductid($product_id);
        $this->ad_stat_limit=$ad_limit;
        $this->ad_stat_plan=$ad_plan;
        $this->ad_product_id=$product_id;
        $this->updated=time();
        
        if(empty($adlimit_id)){
            $this->created=time();
            return $this->insert();
        }
        $this->id=$adlimit_id;
        $this->update();
        return true;
    }
    //将素材库的导量限制信息导入到业务表
    public function resourcelimit2limit($product_id){
        $re=$this->getOne(array("select"=>"*","where"=>"ad_product_id='".$product_id."'","asArray"=>true));
        if(empty($re)){
            return false;
        }
        $limit=Doo::loadModel("datamodel/AdProductLimit",TRUE);
        $result=$limit->getOne(array("select"=>"id","where"=>"ad_product_id='".$product_id."'","asArray"=>true));
        $limit->ad_product_id=$product_id;
        $limit->ad_stat_limit=$re["ad_stat_limit"];
        $limit->ad_stat_plan=$re["ad_stat_plan"];
        $limit->created=$re["created"];
        $limit->updated=$re["updated"];
        if(empty($result)){
            $limit->insert();
        }else{
            $limit->id=$result["id"];
            $limit->update();
        }
    }
    /*************************************/
    private function _getProductLimitIdByProductid($product_id){
        $result=$this->getOne(array("select"=>"id","where"=>"ad_product_id='".$product_id."'","asArray"=>true));
        if(!empty($result)){
            return $result["id"];
        }
        return false;
    }
}