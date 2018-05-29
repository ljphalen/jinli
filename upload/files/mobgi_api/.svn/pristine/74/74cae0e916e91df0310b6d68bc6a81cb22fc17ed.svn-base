<?php
Doo::loadModel('datamodel/base/AdApkBase');

class AdApk extends AdApkBase{
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("ads");
    }
    public function getAdapk($id){
        return $this->getOne(array("select"=>"*","where"=>"id='".$id."'","asArray"=>TRUE));
    }
    /*
     * 通过产品名称查找
     */
    public function getAdApkByProductName($product_name,$platform){
        return $this->getOne(array("select"=>"*","where"=>"product_name='".$product_name."' and ischeck=1 and platform in(0, ${platform}) ","desc"=>"updatetime","asArray"=>TRUE));
    }
    
    public function getAdapkCheck($ischeck=array(1),$where){
        $whereSql=$this->_conditions($where);
        $sql="select id,product_name,`ischeck`,owner,createtime,platform from mobgi_ads.ad_apk where ischeck in(".  implode(",",$ischeck).") and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") and $whereSql";
        return Doo::db()->query($sql)->fetchAll();
        //return $this->find(array("select"=>"product_name,`ischeck`,owner,createtime","where"=>"`ischeck`!=1","asArray"=>TRUE));
    }
    
    public function upd($post){
        $this->ad_product_id=$post["product"];
        $this->product_name=trim(trim(trim($post["product_name"],"(A)"),"(I)"),"(T)");;
        $this->apk_version=$post["apk_version"];
        $this->package_name=$post["package"];
        $this->channel_id=$post["channel"];
        $this->size=$post["size"];
        $this->apk_url=$post["apk_url"];
        $this->updatetime=time();
        $this->check_msg=$post["check_msg"];
        $this->ischeck=$post["ischeck"];
        $this->checker=$post["checker"];
        $this->owner=$post["owner"];
        $this->platform = $post["platform"];
        if(empty($post["id"])){
            $this->createtime=  time();
            return $this->insert();
        }else{
            $this->id=$post["id"];
            $this->update();
            return $post["id"];
        }
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
        if (isset($conditions['createtime']) && $conditions['createtime']){
            $where[] = "createtime>= ".$conditions['createtime'];
        }
        if (isset($conditions['screatedate']) && $conditions['screatedate']){
            $where[] = "createtime>= ".$conditions['screatedate'];
        }
        if (isset($conditions['ecreatedate']) && $conditions['ecreatedate']){
            $where[] = "createtime<= ".$conditions['ecreatedate'];
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
}