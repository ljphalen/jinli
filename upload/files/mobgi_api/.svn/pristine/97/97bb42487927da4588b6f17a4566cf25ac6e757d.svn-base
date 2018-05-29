<?php
Doo::loadModel('datamodel/base/IptadProductBase');

class IptadProduct extends IptadProductBase{
    
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    
    /**
     * 根据关键字获取产品
     * @param type $keyword
     * @return type
     */
    public function getCount($keyword){
        $count=$this->count(array("where"=>'product_name like "%'.$keyword.'%" ','asArray' => TRUE));
        return $count;
    }
    
    /**
     * 获取产品列表
     * @param type $keyword
     * @param type $limit
     * @return type
     */
    public function productlist($keyword,$limit=NULL){
        $limitArr = array();
        if ($limit){
            $limitArr = array('limit' => $limit);
        }
        $lists = $this->find(array_merge(array("where"=>'product_name like "%'.$keyword.'%" ','asArray' => TRUE, 'desc'=>'updated'), $limitArr));
        return $lists;
    }
    
    /*
     * 返回某一产品的详细信息
     * @param $productid如果为空则返回所有
     */
    public function view($productid=null){
        $where=array('select'=>'*','where'=>'pid="'.$productid.'"','asArray' => TRUE);
        $lists=$this->getOne($where);
        return $lists;
    }
    
    /**
     * 产品:修改,删除产品时删除对应的产品id key "PRODUCT_".$pid;
     * @return boolean
     */
    public function del_productcache($pid){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('IMPLANTABLE_REDIS_CACHE_DEFAULT');
        // 删除Redis
        $key = "PRODUCT_".$pid;
        $redis->delete($key);
        return true;
    }
}