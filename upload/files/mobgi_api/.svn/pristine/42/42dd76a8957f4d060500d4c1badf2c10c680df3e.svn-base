<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-4 10:14:25
 * $Id: PushLimit.php 62100 2015-1-4 10:14:25Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushLimitBase');

class PushLimit extends PushLimitBase{
    
    /**
     * 新增
     * @param type $config_id
     * @param type $app_id
     * @param type $channel_id
     * @param type $opertor
     * @return type
     */
    public function addLimit($config_id, $app_id, $pkg, $channel_id, $opertor){
        $this->config_id = $config_id;
        $this->app_id = $app_id;
        $this->packagename = $pkg;
        $this->channel_id = $channel_id;
        $this->opertor = $opertor;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    /**
     * 更新记录
     * @param type $config_id
     * @param type $app_id
     * @param type $channel_id
     * @param type $opertor
     * @return type
     */
    public function updateLimit($config_id, $app_id, $pkg, $channel_id,  $opertor){
        $this->config_id = $config_id;
        $this->app_id = $app_id;
        $this->packagename = $pkg;
        $this->channel_id = $channel_id;
        $this->opertor = $opertor;
        $this->updatedate = time();
        return $this->update();
    }
    
    /**
     * 获取屏蔽的应用详细信息
     * @param type $appkey
     * @return boolean
     */
    public function getAppkey($appkey){
        if(empty($appkey)){
            return false;
        }
        $sql="select app_name,appkey from ad_app where appkey in(".implodeSqlstr($appkey).")";
        $appinfo=Doo::db()->query($sql)->fetchAll();    
        return $appinfo;
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
    
    
    
}

