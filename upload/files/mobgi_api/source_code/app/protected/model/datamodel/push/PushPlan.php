<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-5 10:26:38
 * $Id: PushPlan.php 62100 2015-1-5 10:26:38Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushPlanBase');

class PushPlan extends PushPlanBase{
    
    /**
     * 获取某个导量配置的所有计划列表(未删除的导量计划)
     * @param type $configId
     * @return type
     */
    public function getPlan($configId){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = 'config_id='.$configId ." and del=1 ";
        $result = $this->find($whereArr);
        return $result;
    }
    
    /**
     * 根据配置id获取导量计划
     * @param type $configids
     * @return boolean
     */
    public function getPlanByCfgIds($configids){
        if(empty($configids)){
            return false;
        }
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = 'config_id in('.implode(',', $configids). ") and del = 1 ";
        $result = $this->find($whereArr);
        return $result;
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
        $whereArr['desc'] = 'state,runstatus ,createdate ';
        // 当存在条件时。组合条件
        #$conditions["del"]=1;
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->find($whereArr);
        if(!empty($result)){
            foreach($result as $key=>$item){
                //列表先按照“导量状态”排序（导量中>未到开始时间>手动停止>导量完毕），再按“导量时间-开始时间”降序排序（开始时间晚的排在上面）；
                if($item['go_method'] == 1){
                    $result[$key]['go_method_desc'] = "一次性有效";
                }else if($item['go_method'] == 2){
                    $result[$key]['go_method_desc'] = "周期性有效";
                }else{
                    $result[$key]['go_method_desc'] = "未知";
                }
                
                if($item['method'] == 1){
                    $result[$key]['method_desc'] = "CPM";
                }else if($item['method'] == 2){
                    $result[$key]['method_desc'] = "CPC";
                }else{
                    $result[$key]['method_desc'] = "未知";
                }
                
                if($item['runstatus'] == 1){
                    $result[$key]['runstatus_desc'] = "未到开始时间";
                }else if($item['runstatus'] == 2){
                    $result[$key]['runstatus_desc'] = "导量中";
                }else if($item['runstatus'] == 3){
                    $result[$key]['runstatus_desc'] = "导量完毕";
                }else{
                    $result[$key]['runstatus_desc'] = "未知";
                }
                if(!$this->judgeCanEditPlan($item)){
                    $result[$key]['intime'] = 1;
                }else{
                    $result[$key]['intime'] = 0;
                }
                
                //二维排序因子整理
                if($item['runstatus'] == 1){
                    $result[$key]['order_state'] = 2;
                }else if($item['runstatus'] == 2){
                    $result[$key]['order_state'] = 1;
                }else if($item['runstatus'] == 3){
                    $result[$key]['order_state'] = 4;
                }
                if($item['state'] == 2){
                    $result[$key]['order_state'] = 3;
                }
                 $sort_order_state[] = $result[$key]['order_state'];
                 $sort_start_time[] = $result[$key]['start_time'];
            }
            array_multisort($sort_order_state, SORT_ASC, $sort_start_time, SORT_DESC, $result);
        }
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
        if (isset($conditions['config_id']) && $conditions['config_id']){
            $where[] = "config_id = ".$conditions['config_id'];
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
        if (isset($conditions['r_start_time']) && $conditions['r_start_time']){
            $where[] = "start_time = '".$conditions['r_start_time']."'";
        }
        if (isset($conditions['r_end_time']) && $conditions['r_end_time']){
            $where[] = "end_time = '".$conditions['r_end_time']."'";
        }
        if (isset($conditions['go_method']) && $conditions['go_method']){
            $where[] = "go_method ='".$conditions['go_method']."'";
        }
        if(isset($conditions['runstate']) && $conditions['runstate']){
            if($conditions["runstate"]==="3"){
                $where[] = "(runstatus != '2' or state=2)";
            }else{
                $where[] = "(runstatus=2 and state=1)";
            }
        }
        if(isset($conditions['runstatus']) && $conditions['runstatus']){
            $where[] = "runstatus = '".$conditions['runstatus']."'";
        }
        if(isset($conditions['price']) && $conditions['price']){
            $where[] = "price = '".$conditions['price']."'";
        }
        if(isset($conditions['del'])){
            $where[] = "del= '".$conditions['del']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    
    /**
     * 变更导量计划状态
     * @param type $plan_id
     * @param type $state
     * @param type $operator
     * @return type
     */
    public function set_state($plan_id, $state, $operator){
        $updateSql = "update push_plan set state = ". $state. " , oprator='". $operator."', updatedate= ". time(). "  where id=". $plan_id ." limit 1";
        return Doo::db()->query($updateSql);   
    }
    
    /**
     * 更新一条记录
     * @param Array $conditions
     */
    public function upd($conditions){
        $update_sql_arr = array();
        foreach($conditions as $k=>$v){
            if($k==="start_time" || $k==="end_time"){
                $v=strtotime($v);
            }
            if(in_array($k,$this->_fields)){
                $this->$k=$v;
                $update_sql_arr[] = $k."='".$v."'";
            }
        }
        try{
            $this->updatedate=time();
            if(isset($conditions["id"]) && !empty($conditions["id"])){
                $updateSql = implode(" , ", $update_sql_arr);
                $updateSql = "update push_plan set ".$updateSql.   " where id=". $conditions["id"] ." limit 1";
                return Doo::db()->query($updateSql);   
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
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*,from_unixtime(start_time) as stime,from_unixtime(end_time) as etime';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        return $result;
    }
    
    /**
     * 软删除导量计划
     * @param type $planid
     * @param type $operator
     * @return type
     */
    public function delPlan($planid, $operator){
        $updateSql = "update push_plan set del = 0 , oprator='". $operator."', updatedate= ". time(). "  where id=". $planid ." limit 1";
        return Doo::db()->query($updateSql);   
    }
    
    /**
     * 软删除导量配置下面的导量计划
     * @param type $configid
     * @param type $operator
     * @return type
     */
    public function delPlanByConfigid($configid, $operator){
        $updateSql = "update push_plan set del = 0 , oprator='". $operator."', updatedate= ". time(). "  where config_id=". $configid ." ";
        return Doo::db()->query($updateSql);   
    }
    
    
    /**
     * 硬删除导量计划
     * @param type $planid
     * @param type $operator
     * @return type
     */
    public function deletePlan($planid){
        $deleteSql = "delete from push_plan where id=". $planid ." limit 1";
        return Doo::db()->query($deleteSql);   
    }
    
    /**
     * 判断导量计划是否可以编辑、删除
     * @param type $planid
     * @return boolean
     */
    public function canEditPlan($planid){
        $conditions=array();
        $conditions["id"]=$planid;
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        if(!empty($result)){
            return $this->judgeCanEditPlan($result);
        }
        return false;
    }
    
    /**
     * 对连续性导量和周期性导量配置分别判断是否可编辑
     * @param type $planItem
     * @return boolean
     */
    public function judgeCanEditPlan($planItem){
        $now = time();
        if($planItem['go_method'] == 1){
            if(($now > $planItem['start_time'] - 300) && ($now < $planItem['end_time'])){
                return false;
            }else{
                return true;
            }
        }else if($planItem['go_method'] == 2){
            if(($now > $planItem['start_time'] - 300) && ($now < $planItem['end_time'])){
                $today_start_time = strtotime(date("Y-m-d") . " " .date("H:", $planItem['start_time']) .date("i:", $planItem['start_time']) .date("s", $planItem['start_time']));
                $today_end_time = strtotime(date("Y-m-d") . " " .date("H:", $planItem['end_time']) .date("i:", $planItem['end_time']) .date("s", $planItem['end_time']));
                if(($now > $today_start_time - 300) && ($now <$today_end_time)){
                    return false;
                }else{
                    return true;
                }
            }else{
                return true;
            }
        }
        return true;
    }
    
    /**
     * 判断是否可以编辑配置
     * @param type $configId
     * @return boolean
     */
    public function canEditConfig($configId){
        $return = array();
        $configId = intval($configId);
        $result =  $this->getOne(array("select"=>"*","where"=>"config_id='".$configId."'","asArray"=>TRUE));
        
        if(!empty($result)){
            foreach ($result as $planItem){
                if(!$this->judgeCanEditPlan($planItem)){
                    return false;
                }
            }
            
        }else{
            return false;
        }
        return true;
    }
    
    /**
     * 获取指定产品的导量计划的开始时间和结束时间
     * @param type $productId
     * @return type
     */
    public function getPlanByProductid($productId){
        $sql = 'select p.start_time, p.end_time from push_config c left join push_plan p on c.id = p.config_id where c.product_id = '. $productId .' and c.del = 1 and p.del = 1';
        return  Doo::db()->query($sql)->fetchAll();
    }
    
    /**
     * 获取今天内有效的周期性导量计划(未删除的运行中的今天有效期内周期性导量计划）
     * @return int
     */
    public function getEffectiveCyclePlan(){
        $s_time = strtotime(date("Y-m-d"));
        $e_time = strtotime("tomorrow");
        $sql = 'select * from push_plan where del = 1 and go_method = 2 and state = 1 and ((start_time <=' . $s_time .' and end_time >= '. $s_time. ') or (start_time >='.$s_time.' and start_time<='.$e_time.')) ';
        return  Doo::db()->query($sql)->fetchAll();
    }
    
}

