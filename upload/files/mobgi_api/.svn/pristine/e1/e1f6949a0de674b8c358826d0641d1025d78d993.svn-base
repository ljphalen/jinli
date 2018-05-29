<?php
Doo::loadModel('datamodel/base/AdIncentiveVideoBase');

class AdIncentiveVideo extends AdIncentiveVideoBase{
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("ads");
    }
    public function getAdIncentivevideo($id){
        return $this->getOne(array("select"=>"*","where"=>"id='".$id."'","asArray"=>TRUE));
    }
    
  
    /*
     * 通过产品名称查找
     */
    public function getAdIncentiveVideoByProductName($product_name,$updated,$platform){
        return $this->find(array("select"=>"*","where"=>"product_name='".$product_name."' and ischeck=1 and platform in (0, ${platform}) and updatetime>${updated}","asArray"=>TRUE));
    }
    /*
     * 获取未审核的video列表
     */
    public function getAdIncentivevideoCheck($ischeck=array(1),$where){
        $whereSql=$this->_conditions($where);
        $sql="select *  from mobgi_ads.ad_incentive_video where ischeck in(".  implode(",",$ischeck).") and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") and $whereSql";
        return Doo::db()->query($sql)->fetchAll();
        //return $this->find(array("select"=>"product_name,`ischeck`,owner,createtime","where"=>"`ischeck`!=1","asArray"=>TRUE));
    }
    public function upd($post){
        $this->ad_product_id=$post["product_id"];
        $this->product_name=trim(trim(trim($post["product_name"],"(A)"),"(I)"),"(T)");;
        $this->video_url=$post["video_url"];
        $this->h5_url=$post["h5_url"];
        $this->video_name=$post["video_name"];
        $this->ad_type=$post["ad_type"];
        $this->updatetime=time();
        $this->check_msg=$post["check_msg"];
        $this->ischeck=$post["ischeck"];
        $this->checker=$post["checker"];
        $this->owner=$post["owner"];
        $this->platform = $post["platform"];
        
        if(empty($post["id"])){
            $this->creattime=  time();
            return $this->insert();
        }else{
            $this->id=$post["id"];
            $this->update();
            return $post["id"];
        }
    }
    public function getCount($whereString=null){
        if(empty($whereString)){//如果条件为空则默认返回全部
            $adconfig_info = $this->count();
        }else{
            $adconfig_info = $this->count(array('select' => '*', 'where' => $whereString, 'asArray' => TRUE));
        }
        return $adconfig_info;
    }
    
    public function getList($sql_string=null, $limit = NULL){
        $limitArr = array();
        if ($limit){
            $limitArr = array('limit' => $limit);
        }
        if(empty($sql_string)){//如果条件为空则默认返回全部
            $result = $this->find(array_merge($limitArr, array('asArray' => TRUE)));
        }else{
            $result = $this->find(array_merge($limitArr, array('select' => '*', 'where' => $sql_string, 'asArray' => TRUE)));
        }
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
            $where[] = "product_name like '%".$conditions['product_name']."%'";
        }
        if (isset($conditions['creattime']) && $conditions['creattime']){
            $where[] = "creattime>= ".$conditions['creattime'];
        }
        if (isset($conditions['screatedate']) && $conditions['screatedate']){
            $where[] = "creattime>= ".$conditions['screatedate'];
        }
        if (isset($conditions['ecreatedate']) && $conditions['ecreatedate']){
            $where[] = "creattime<= ".$conditions['ecreatedate'];
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