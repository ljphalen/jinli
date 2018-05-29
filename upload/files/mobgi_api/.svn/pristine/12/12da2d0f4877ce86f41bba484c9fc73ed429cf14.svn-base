<?php

/**
 * Description of AdLimit
 *
 * @author Stephen.Feng
 */
class AdLimit extends AppModel{
    public function __construct() {
        parent::__construct();
    }
    
    public function upd($ad_limit,$ad_plan,$product_id){//导量信息
        $adlimit=Doo::loadModel("datamodel/AdProductLimit",TRUE);
        $adlimit->ad_stat_limit=$ad_limit;
        $adlimit->ad_stat_plan=$ad_plan;
        $adlimit->ad_product_id=$product_id;
        $adlimit->updated=time();
        $adlimit_id=$this->_getProductLimitIdByProductid($product_id);
        if(empty($adlimit_id)){
            $adlimit->created=time();
            return $adlimit->insert();
        }
        $adlimit->id=$adlimit_id;
        $adlimit->update();
        
        $this->delLimitForRedis($product_id);//删除redis
        
        return true;
    }
    public function del($product_id){
        $sql="delete from ad_product_limit where ad_product_id='$product_id'";
        Doo::db()->query($sql)->execute();
        
        $this->delLimitForRedis($product_id);//删除redis
        
        return true;
    }
    public function limit_plan($productid){
        $ad_limit_stat=$this->getAdlimitByProductid($productid);
        return $ad_limit_stat;
    }
    //======================   华丽的分割线   =======================//
    private function getAdlimitByProductid($productid){//通过productid获取到量限制信息
        $limit=Doo::loadModel("datamodel/AdProductLimit",TRUE);
        $result=$limit->getOne(array("select"=>"ad_stat_limit,ad_stat_plan","where"=>"ad_product_id='".$productid."'","asArray"=>true));
        if(!empty($result)){
            $result["ad_stat_limit"]= json2array($result["ad_stat_limit"]);
            $result["ad_stat_plan"]= json2array($result["ad_stat_plan"]);
        }
        return $result;
    }
    private function _getProductLimitIdByProductid($product_id){
        $limit=Doo::loadModel("datamodel/AdProductLimit",TRUE);
        $result=$limit->getOne(array("select"=>"id","where"=>"ad_product_id='".$product_id."'","asArray"=>true));
        
        if(!empty($result)){
            return $result["id"];
        }
        return false;
    }
    //删除redis中导量信息数据
    private function delLimitForRedis($product_id){
        $keys=array($product_id."_plan",$product_id."_limit");
        $this->deleter($keys,"CACHE_REDIS_SERVER_2");//删除redis中数据
    }
}

?>
