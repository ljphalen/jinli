<?php
Doo::loadModel('datamodel/base/AdPublishBase');

class AdPublish extends AdPublishBase{
    
    /*
     * 返回客户个数
     */
    public function getCount($keyword=''){
        return $this->count(array("where"=>'compay like "%'.$keyword.'%" and del = 1 ','asArray' => TRUE));
    }
    
    /**
     * 获取客户列表
     * @param type $keyword
     * @param type $limit
     */
    public function customerlist($keyword,$limit){
        $limitArr = array();
        if ($limit){
            $limitArr = array('limit' => $limit);
        }
        return $this->find(array_merge(array("where"=>'compay like "%'.$keyword.'%" and del = 1 ',"desc"=>"updatetime",'asArray' => TRUE), $limitArr));
    }
    
    /**
     * 软删除客户名
     * @param type $customerid
     */
    public function softDelCustomer($customerid){
        $this->updatetime=time();
        $this->id=$customerid;
        $this->del=0;
        return $this->update();
    }

    /**
     * 新增客户
     * @param type $compay
     * @param type $conact
     * @param type $tel
     * @param type $address
     * @return type
     */
    public function addCustomer($compay, $conact, $tel,  $address){
        $this->compay = $compay;
        $this->conact = $conact;
        $this->tel = $tel;
        $this->address = $address;
        $now = time();
        $this->createtime=$now;
        $this->updatetime=$now;
        return $this->insert();
    }
    
    /**
     * 更新客户信息
     * @param type $id
     * @param type $compay
     * @param type $conact
     * @param type $tel
     * @param type $address
     * @return type
     */
    public function updateCustomer($id, $compay, $conact, $tel,  $address){
        $this->id = $id;
        $this->compay = $compay;
        $this->conact = $conact;
        $this->tel = $tel;
        $this->address = $address;
        $now = time();
        $this->updatetime=$now;
        return $this->update();
    }
    
    /**
     * 获取指定的客户信息
     * @param type $id
     * @return boolean
     */
    public function getCustomerByid($id){
        if(empty($id)){
            return false;
        }
        $infos=$this->find(array("where"=>'id ='.$id.' and del = 1 ','asArray' => TRUE)); 
        return $infos[0];
    }
    
}