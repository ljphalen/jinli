<?php
Doo::loadModel('datamodel/base/Roles2productsBase');

class Roles2products extends Roles2productsBase{
    //更新产品所属角色
    private $admininfo;
    function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("admin");
        $this->admininfo=Doo::session()->__get("admininfo");
    }
    public function roleUpdate($product_id,$role_id){
        if(!is_array($product_id)){(array)$product_id;}
        if(empty($role_id)){return false;}
        try{
            $this->role_id=$role_id;
            $this->delete();
            foreach($product_id as $pid){
                $this->product_id=$pid;
                $this->updatetime=time();
                $this->role_id=$role_id;
                $this->operator=$this->admininfo["username"];
                $this->operator=$this->admininfo["username"];
                $this->insert();
            }
        }  catch (Exception $e){
            return false;
        }
        return TRUE;
    }
    public function deleteRole2products($product_id){
        if(empty($product_id)){
            $this->delete();
            return true;
        }
        $this->product_id=$product_id;
        $this->delete();
    }
    public function getProductsByRole($role_id){
        try {
            $where=array('select'=>'product_id','where'=>'role_id="'.$role_id.'"','asArray' => TRUE);
            return $this->find($where);
        } catch (Exception $exc) {
            return false;
        }
    }
}