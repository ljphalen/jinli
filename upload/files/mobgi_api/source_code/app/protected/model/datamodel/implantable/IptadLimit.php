<?php
Doo::loadModel('datamodel/base/IptadLimitBase');

class IptadLimit extends IptadLimitBase{
        /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    function updateLimit($data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->config_id = $data['config_id'];
        $this->make = json_encode($data['mobile_type']);
        $this->carrier = json_encode($data['carrieroperator']);
        $this->net_type = json_encode($data['network_environment']);
        $this->loc = json_encode($data['geographical_position']);
        $this->operator = $currentUser['username'];
        $this->updatedate = time();
        $this->res("IMPLANTABLE_REDIS_CACHE_DEFAULT")->delete("LIMIT_ALL");
        if(empty($data["limit_id"])){
            $this->createdate = time();
            return $this->insert();
        }else{
            $this->res("IMPLANTABLE_REDIS_CACHE_DEFAULT")->delete("LIMIT_".$data['config_id']);
            $this->id=$data["limit_id"];
            $this->update();
            return $data["limit_id"];
        }
    }
    function getLimitByConfigid($config_id){
        return $this->getOne(array("where"=>'config_id ='.$config_id.' ','asArray' => TRUE));
    }
}