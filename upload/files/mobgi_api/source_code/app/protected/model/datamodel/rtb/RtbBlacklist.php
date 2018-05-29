<?php
Doo::loadModel('datamodel/base/RtbBlacklistBase');

class RtbBlacklist extends RtbBlacklistBase{
   
    /**
     * 根据类型获取黑名单记录,$type值为package或者ip
     * @param type $type
     * @return type
     */
    public function getRtbBlackByType($type){
        return $this->getOne(array("select"=>"*","where"=>"type='".$type."'","asArray"=>TRUE));
    }
    
    /**
     * 获取所有类型的黑名单记录
     * @return type
     */
    public function getRtbBlacklist(){
        $sql = "select * from rtb_blacklist where type in('ip', 'package')";
        return Doo::db()->query($sql)->fetchAll();
    }
    
    /**
     * 新增一条RTB黑名单记录
     * @param type $type
     * @param type $value
     * @param type $mober
     * @param type $operator
     */
    public function addBlacklist($type, $value, $mober, $operator){
        $this->type = $type;
        $this->value = $value;
        $this->mober = $mober;
        $this->oprator = $operator;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    /**
     * 更新RTB黑名单的值.
     * @param type $id
     * @param type $type
     * @param type $value
     * @param type $operator
     */
    public function updateBlacklist($id, $type, $value, $operator){
        $this->id = $id;
        $this->type = $type;
        $this->value = $value;
        $this->oprator = $operator;
        $this->updatedate = time();
        return $this->update();
    }
    
}
