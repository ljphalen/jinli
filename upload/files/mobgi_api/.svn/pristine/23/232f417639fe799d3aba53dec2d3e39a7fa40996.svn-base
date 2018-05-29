<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-6 16:25:29
 * $Id: PushWeight.php 62100 2015-1-6 16:25:29Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushWeightBase');

class PushWeight extends PushWeightBase{
    
    /**
     * 获取全部的导量权重配置
     * @return type
     */
    public function getPushWeight($weightId, $cancel){
        $weightId = intval($weightId);
        if(empty($weightId)){
            return false;
        }
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = 'start_time as start,end_time as end, product_combo ';
        if($cancel){
            $whereArr['where'] = " id=". $weightId;
        }else{
            $whereArr['where'] = "del= 1 and id=". $weightId;
        }
        $result = $this->getOne($whereArr);
        if(!empty($result)){
            $result['id'] = $weightId;
            $result['cancel'] = $cancel;
            $product_combo_arr = json_decode($result['product_combo'], true);
            $product_combo = $product_combo_arr['product_combo'];
            //权重乘以100给ＳＤＫ
            if(!empty($product_combo)){
                foreach($product_combo as $key=>$item){
                    $product_combo[$key]['weight'] = $item['weight'] * 100;
                }
            }
            
            $result['list'] = $product_combo;
            unset($result['product_combo']);
        }
        
        //配置类型  type:0/1/2  分别代表广告推送消息、全局配置、全局产品权重配置
        $extra = array();
        $extra['type'] = 2;
        $extra['product_weight'] = $result;
        
        $pushConfig = array();
        $pushConfig['appid'] = 0;
        $pushConfig['key'] = 'dfv4tdfq34gd';
        $pushConfig['type'] = 1;
        $pushConfig['start_time'] = $result['start']*1000;
        $pushConfig['end_time'] = $result['end']*1000;
        $pushConfig['important'] = 1;
        $pushConfig['extra'] = json_encode($extra);
        //接收端是java,所以需要加http_build_query
        $pushConfig = http_build_query($pushConfig);
        return $pushConfig;
    }
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return type
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->count($whereArr);
        return $result;
    }
    
    /**
     * 查询列表-多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*,from_unixtime(start_time) as stime,from_unixtime(end_time) as etime ';
        $whereArr['desc'] = 'state,createdate desc,id ';
        // 当存在条件时。组合条件
        #$conditions["del"]=1;
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->find($whereArr);
        
        //获取产品名
        if(!empty($result)){
            $prodcutmodel = Doo::loadModel("AdProducts", TRUE);
            $product_id_name = array();
            foreach($result as $key=>$item){
                $product_combo = array();
                $product_names = '';
                if(!empty($item['product_combo'])){
                    $product_combo_arr = json_decode($item['product_combo'], true);
                    if(count($product_combo_arr['product_combo']) > 0){
                        foreach($product_combo_arr['product_combo'] as $key2=>$productItem){
                            if(isset($product_id_name[$productItem['pid']])){
                                $product_combo[$key2]['product_name'] = $product_id_name[$productItem['pid']];
                            }else{
                                $productinfo = $prodcutmodel->view($productItem['pid']);
                                $product_combo[$key2]['product_name'] = $productinfo['product_name'];
                                $product_id_name[$productItem['pid']] = $productinfo['product_name'];
                            }
                            $product_names .=$product_combo[$key2]['product_name'];
                            $product_combo[$key2]['weight'] = $productItem['weight'];
                            $product_combo[$key2]['pid'] = $productItem['pid'];
                        }
                    }
                }
                $result[$key]['product_combo_arr'] = $product_combo;
                $result[$key]['product_names'] = $product_names;
                //是否在生效时间内
                if((time() > $item['start_time'] - 300 && time() < $item['end_time'])){
                    $result[$key]['intime'] = 1;
                }else{
                    $result[$key]['intime'] = 0;
                }
                
            }
        }
        
        if(isset($conditions['keyword']) && !empty($conditions['keyword'])){
            $search_keyword =$conditions['keyword'];
            //若有关键字，则根据配置项id,配置项名称，产品名搜索
            foreach($result as $key=>$result_item){
                if((strpos($result_item['id'],$search_keyword) === false) && (strpos($result_item['config_name'],$search_keyword) === false) && (strpos($result_item['product_names'],$search_keyword) === false)){
                    unset($result[$key]);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*,from_unixtime(start_time) as stime,from_unixtime(end_time) as etime';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        $product_combo = array();
        if(!empty($result['product_combo'])){
            $product_combo_arr = json_decode($result['product_combo'], true);
            if(count($product_combo_arr['product_combo']) > 0){
                $prodcutmodel = Doo::loadModel("AdProducts", TRUE);
                foreach($product_combo_arr['product_combo'] as $key=>$productItem){
                    $productinfo = $prodcutmodel->view($productItem['pid']);
                    $product_combo[$key]['product_name'] = $productinfo['product_name'];
                    $product_combo[$key]['weight'] = $productItem['weight'];
                    $product_combo[$key]['pid'] = $productItem['pid'];
                }
            }
        }
        $result['product_combo_arr'] = $product_combo;
        return $result;
    }
    
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "1=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        
        if (isset($conditions['id'])){
            $where[] = "id = ".$conditions['id'];
        }
        
        if (isset($conditions['ustart_time']) && $conditions['ustart_time'] && isset($conditions['uend_time']) && $conditions['uend_time']){
            $where[] = "((start_time <= UNIX_TIMESTAMP('".$conditions['ustart_time']."') and UNIX_TIMESTAMP('".$conditions['ustart_time']."') <=end_time and end_time < UNIX_TIMESTAMP('".$conditions['uend_time']."')) or ".
                "(start_time >= UNIX_TIMESTAMP('".$conditions['ustart_time']."') and UNIX_TIMESTAMP('".$conditions['uend_time']."') >=start_time and end_time >=UNIX_TIMESTAMP('".$conditions['uend_time']."')) or ".
                "(start_time <= UNIX_TIMESTAMP('".$conditions['ustart_time']."') and end_time >= UNIX_TIMESTAMP('".$conditions['uend_time']."')) or".
                "(UNIX_TIMESTAMP('".$conditions['ustart_time']."') <=start_time and end_time <= UNIX_TIMESTAMP('".$conditions['uend_time']."')))";
        }
        if (isset($conditions['start_time']) && $conditions['start_time']){
            $where[] = "start_time >= UNIX_TIMESTAMP('".$conditions['start_time']."')";
        }
        if (isset($conditions['end_time']) && $conditions['end_time']){
            $where[] = "end_time <= UNIX_TIMESTAMP('".$conditions['end_time']."')";
        }
        if(isset($conditions['state']) && $conditions['state']){
            $where[] = "state = ".$conditions['state'];
        }
        if(isset($conditions['del']) && $conditions['del']){
            $where[] = "del= '".$conditions['del']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    
    /**
     * 更新一条记录
     * @param Array $conditions
     */
    public function upd($conditions){
        foreach($conditions as $k=>$v){
            if($k==="start_time" || $k==="end_time"){
                $v=strtotime($v);
            }
            if(in_array($k,$this->_fields)){
                $this->$k=$v;
            }
        }
        try{
            $this->updatedate=time();
            if(isset($conditions["id"]) && !empty($conditions["id"])){
                return $this->update();
            }else{
                $this->createdate=time();
                return $this->insert();
            }
            return true;
        }  catch (Exception $e){
            return false;
        }
    }
    
    /**
     * 判断导量计划是否可以编辑、删除
     * @param type $weightId
     * @return boolean
     */
    public function canEditWeight($weightId){
        $conditions=array();
        $conditions["id"]=$weightId;
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        if(!empty($result)){
            if((time() > $result['start_time'] - 300) && (time() < $result['end_time'])){
                return false;
            }else{
                return true;
            }
        }
        return false;
    }
    
    /**
     * 软删除导量权重
     * @param type $weightId
     * @param type $operator
     * @return type
     */
    public function delWeight($weightId, $operator){
        $updateSql = "update push_weight set del = 0 , operator='". $operator."', updatedate= ". time(). "  where id=". $weightId ." limit 1";
        return Doo::db()->query($updateSql);   
    }
    
    /**
     * 获取配置了push导量的产品信息
     * @return boolean
     */
    public function getProductPop(){
        //获取导量配置表中的产品id和导量配置id
        $PushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        $configArr = $PushConfigModel->getProductids();

        $pid_cfgid_arr = array();
        $pid_arr = array();
        if(!empty($configArr)){
            foreach($configArr as $item){
                $pid_cfgid_arr[$item['product_id']][] = $item['config_id'];
                if(!in_array($item['product_id'], $pid_arr)){
                    $pid_arr[] = $item['product_id'];    
                }
            }
        }
        else{
            return false;
        }
        $prodcutModel = Doo::loadModel("AdProducts", TRUE);
//        $this->data["products"] = $prodcutModel->productlist($keyword,$platform);
        $lists = $prodcutModel->getProductsByids($pid_arr);
        
        if(empty($lists)){
            return false;
        }

        $plan=Doo::loadModel("datamodel/push/PushPlan",True);
        
        foreach($lists as $key=>$product){
            $planResult = $plan->getPlanByCfgIds($pid_cfgid_arr[$product['id']]);
            $planTime = array();
            
            if(!empty($planResult)){
                foreach($planResult as $planItem){
                    $tmp = array();
                    $tmp['start_time'] = date("Y-m-d H:i:s",$planItem['start_time']);
                    $tmp['end_time'] = date("Y-m-d H:i:s",$planItem['end_time']);
                    $planTime[]= $tmp;
                }
            }
            $lists[$key]['plantime'] = $planTime;
        }
        
        return $lists;
    }
    
    /**
     * 获取所有的未删除的导量权重记录
     */
    public function isTimeAvailable($start_time, $end_time){
        $sql = 'select * from push_weight where del =1;';
        $weightList = Doo::db()->query($sql)->fetchAll();
        if(empty($weightList)){
            return true;
        }
        foreach($weightList as $weightItem){
            if($this->isMixTime($start_time, $end_time, $weightItem['start_time'], $weightItem['end_time'])){
                return false;
            }
        }
        return true;
    }
    
    /*
    *比较时间段一与时间段二是否有交集
    */
   private function isMixTime($begintime1, $endtime1, $begintime2, $endtime2) {
        $status = $begintime2 - $begintime1;
        if ($status > 0) {
            $status2 = $begintime2 - $endtime1;
            if ($status2 > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            $status2 = $begintime1 - $endtime2;
            if ($status2 > 0) {
                return false;
            } else {
                return true;
            }
        }
    }
}

