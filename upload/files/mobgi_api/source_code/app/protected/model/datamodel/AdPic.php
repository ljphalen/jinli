<?php
Doo::loadModel('datamodel/base/AdPicBase');

class AdPic extends AdPicBase{
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("ads");
    }
    public function getAdpic($id){
        return $this->getOne(array("select"=>"*","where"=>"id='".$id."'","asArray"=>TRUE));
    }
    /*
     * 通过产品名称查找图片素材
     */
    public function getAdPicByProductName($product_name,$updated,$platform){
        return $this->find(array("select"=>"*","where"=>"product_name='".$product_name."' and ad_type!=10 and ischeck=1 and platform in(0,${platform}) and updatetime>${updated}","desc"=>"creattime","asArray"=>TRUE));
    }
    /*
     * 通过产品名称查找图片素材 icon
     */
    public function getAdIconByProductName($product_name,$platform){
        return $this->getOne(array("select"=>"*","where"=>"product_name='".$product_name."' and ad_type=10  and ischeck=1 and platform in(0,${platform})","desc"=>"creattime","asArray"=>TRUE));
    }
    
    /*
     * 获取未审核的图片列表
     */
    public function getAdpicCheck($ischeck=array(1),$where){
        $whereSql=$this->_conditions($where);
        $sql="select id,product_name,`ischeck`,owner,creattime,pic_name,platform from mobgi_ads.ad_pic where ischeck in(".  implode(",",$ischeck).") and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") and $whereSql";
//        echo $sql;die;
        return Doo::db()->query($sql)->fetchAll();
        //return $this->find(array("select"=>"id,product_name,`ischeck`,owner,creattime,pic_name","where"=>"`ischeck`!=1","asArray"=>TRUE));
    }
    public function upd($post){
        if(!empty($_FILES["file_pic_name"]["name"]) && empty($post["id"])){//新增
            $pic_name=  explode(".",$_FILES["file_pic_name"]["name"]);
            $this->pic_name=$pic_name[0];
            $this->pic_url=$post['pic_url'];
        }else{
            if(!empty($post["pic_name"])){
                $pic_name=explode("-",$post["pic_name"]);
                $this->pic_name=$post["beizhu"]."-".$pic_name[1]."-".$pic_name[2];
            }
        }
        $this->product_name= trim(trim(trim($post["product_name"],"(A)"),"(I)"),"(T)");
        $this->ad_product_id = $post["product_id"];
        $this->ad_type=$post["adtype"];
        $this->ad_subtype=$post["ad_subtype"];
        $this->color=$post["color"];
        $this->focus=$post["focus"];
        $this->xuetou=$post["xuetou"];
        $this->resolution=$post["resolution"];
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
