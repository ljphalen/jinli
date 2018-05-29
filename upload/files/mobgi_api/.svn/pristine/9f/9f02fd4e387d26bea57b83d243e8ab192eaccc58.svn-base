<?php
Doo::loadModel('base/RtbBlacklistBase');

class RtbBlacklist extends RtbBlacklistBase{
   
    /**
     * 根据rtb的类型获取一条rtb黑名单记录.
     * @param type $type
     * @return type
     */
    public function getRtbBlackByType($type){
        return $this->getOne(array("select"=>"*","where"=>"type='".$type."'","asArray"=>TRUE));
    }
    
}