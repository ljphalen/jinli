<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-6 11:18:36
 * $Id: PushHarass.php 62100 2015-1-6 11:18:36Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushHarassBase');

class PushHarass extends PushHarassBase{
    /**
     * 获取push需要的防骚挠配置
     * @param type $id
     * @return type
     */
    public function getPushHarass($id){
        $where = '1';
        $id = intval($id);
        if(!empty($id)){
            $where = "and id='". $id."'";
        }
        $result = $this->getOne(array("select"=>"*","where"=>$where,"asArray"=>TRUE));
        if(!empty($result['value'])){
            $result['value'] =  json_decode($result['value'], true);
        }else{
            //默认参数
            $result['value']=array("one_day_max_msg"=>3,"one_product_msg_limit"=>2, "lastest_msg_interval"=>4);
        }
        
        //配置类型  type:0/1/2  分别代表广告推送消息、全局配置、全局产品权重配置
        $extra = array();
        $extra['type'] = 1;
        $extra['rules'] = $result['value'];
        
        $pushConfig = array();
        $pushConfig['appid'] = 0;
        $pushConfig['key'] = 'dfv4tdfq34gd';
        $pushConfig['type'] = 1;
        $pushConfig['start_time'] = time()*1000;
        $pushConfig['end_time'] = (time() + 3600*24*3)*1000;//全局防防骚挠配置3天有效期
        $pushConfig['important'] = 1;
        $pushConfig['extra'] = json_encode($extra);
        //接收端是java,所以需要加http_build_query
//        print_r($pushConfig);die;
        $pushConfig = http_build_query($pushConfig);
        return $pushConfig;
    }
    
    /**
     * 
     * @param type $id
     * @return int
     */
    public function getHarass($id){
        $where = '1';
        $id = intval($id);
        if(!empty($id)){
            $where = "and id='". $id."'";
        }
        $result = $this->getOne(array("select"=>"*","where"=>$where,"asArray"=>TRUE));
        if(!empty($result['value'])){
            $result['value'] =  json_decode($result['value'], true);
        }else{
            //默认参数
            $result['value']=array("one_day_max_msg"=>3,"one_product_msg_limit"=>2, "lastest_msg_interval"=>4);
        }
        return $result;
    }
    
    /**
     * 新增防骚挠配置
     * @param type $value
     * @param type $operator
     * @return type
     */
    public function add($value, $operator){
        
        $this->value = $value;
        $this->oprator = $operator;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    /**
     * 更新防骚挠配置
     * @param type $id
     * @param type $value
     * @param type $operator
     * @return type
     */
    public function upd($id, $value, $operator){
        $this->id = $id;
        $this->value = $value;
        $this->oprator = $operator;
        $this->updatedate = time();
        return $this->update();
    }
}

