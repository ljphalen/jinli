<?php
Doo::loadModel('datamodel/base/IptadBlacklistBase');

class IptadBlacklist extends IptadBlacklistBase{
        
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    
    /**
     * 获取所有类型的黑名单记录
     * @return type
     */
    public function getChannelBlacklist(){
        $sql = "select * from iptad_blacklist ";
        return Doo::db()->query($sql)->fetchAll();
    }
    
    /**
     * 获取屏蔽的渠道的详细信息
     * @param type $channel_id
     * @return boolean
     */
    public function getChannelById($channel_id){
        if(empty($channel_id)){
            return false;
        }
         $channelinfo=$this->_getChannelById($channel_id);
         return $channelinfo;
    }
    
    /**
     * 根据类型获取黑名单记录,$type值为package或者ip
     * @param type $type
     * @return type
     */
    public function getBlacklistByType($type){
        return $this->getOne(array("select"=>"*","where"=>"type='".$type."'","asArray"=>TRUE));
    }
    
    /**
     * 新增一条黑名单记录
     * @param type $type
     * @param type $value
     * @param type $operator
     */
    public function addBlacklist($type, $value,  $operator){
        $this->type = $type;
        $this->value = $value;
        $this->oprator = $operator;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    /**
     * 更新黑名单的值.
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
    
    /**
     * 新增,修改时删除此key "BLACKLIST_".$type."_".$platform;
     * @return boolean
     */
    public function del_blacklistcache($type, $platform){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('IMPLANTABLE_REDIS_CACHE_DEFAULT');
        // 删除Redis
        $key = "BLACKLIST_".$type."_".$platform;
        $redis->delete($key);
        return true;
    }
    
}