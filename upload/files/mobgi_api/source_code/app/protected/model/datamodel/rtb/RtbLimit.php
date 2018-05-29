<?php
Doo::loadModel('datamodel/base/RtbLimitBase');

class RtbLimit extends RtbLimitBase{
    
    /**
     * 新增记录
     * @param type $config_id
     * @param type $cat
     * @param type $make
     * @param type $carrier
     * @param type $net_type
     * @param type $screen_type
     * @param type $loc
     * @param type $opertor
     * @return type
     */
    public function addLimit($config_id, $cat, $make, $carrier, $net_type, $screen_type, $loc, $opertor){
        $this->config_id = $config_id;
        $this->cat = $cat;
        $this->make = $make;
        $this->carrier = $carrier;
        $this->net_type = $net_type;
        $this->screen_type = $screen_type;
        $this->loc = $loc;
        $this->opertor = $opertor;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    /**
     * 更新记录
     * @param type $config_id
     * @param type $cat
     * @param type $make
     * @param type $carrier
     * @param type $net_type
     * @param type $screen_type
     * @param type $loc
     * @param type $opertor
     * @return type
     */
    public function updateLimit($config_id, $cat, $make, $carrier, $net_type, $screen_type, $loc, $opertor){
        $this->config_id = $config_id;
        $this->cat = $cat;
        $this->make = $make;
        $this->carrier = $carrier;
        $this->net_type = $net_type;
        $this->screen_type = $screen_type;
        $this->loc = $loc;
        $this->opertor = $opertor;
        $this->updatedate = time();
        return $this->update();
    }
    
}