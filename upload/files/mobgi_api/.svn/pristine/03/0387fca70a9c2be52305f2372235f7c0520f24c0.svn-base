<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-5-11 11:13:57
 * $Id: PushPlanController.php 62100 2015-5-11 11:13:57Z hunter.fang $
 */


class PushPlanController extends DooController {
    public function __construct(){

    }
    
    public function index() {
        
    }
    
    /**
     * 获取当前正在运行的导量计划列表,并推送。
     */
    public function getEffectCyclePushPlan(){
        echo date("Y-m-d H:i:s"). ' run script, start:'. "\r\n";
        $num = 0;
        $harassModel=Doo::loadModel("datamodel/push/PushHarass",True);
        $harassInfo = $harassModel->getHarass();
        $one_product_msg_limit = $harassInfo['value']['one_product_msg_limit'];
        // 产品限制：N个自然日内，同一用户最多收到 1 条相同产品的PUSH
        $time_apart = 0;
        if($one_product_msg_limit > 0){
            $time_apart = $one_product_msg_limit * 3600 * 24;
        }
        
        //push日志
        $pushplanlogModel=Doo::loadModel("datamodel/push/PushPlanLog",True);

        $planModel=Doo::loadModel("datamodel/push/PushPlan",True);
        $plans = $planModel->getEffectiveCyclePlan();
        
        //把周期性导量计划分拆成今天可用的导量计划
        if(!empty($plans)){
            foreach($plans as $plan){
                $pushplanlog = $pushplanlogModel->getLatestPushplanlogByPlanid($plan['id']);
                if(empty($pushplanlog)){
                    $this->pushConfigCycle($plan['config_id'], $plan['id']);
                    $num ++;
                    echo 'push plan config_id='.$plan['config_id'].' plan_id='. $plan['id'] . " success.\r\n";
                }else{
                    //如果今天推送过了，就不需要重复推送
                    $s_time = strtotime(date("Y-m-d"));
                    $e_time = strtotime("tomorrow");
                    if($pushplanlog['start_time'] >= $s_time && $pushplanlog['end_time'] <= $e_time){
                        //本日已经推送过，不需要重复推送
                        echo 'push plan config_id='.$plan['config_id'].' plan_id='. $plan['id'] . " break, it has pushed today.\r\n";
                    }else{
                        if($pushplanlog['send_time']+$time_apart <=time()){
                            $this->pushConfigCycle($plan['config_id'], $plan['id']);
                            $num ++;
                            echo 'push plan config_id='.$plan['config_id'].' plan_id='. $plan['id'] . " success.\r\n";
                        }
                    }
                }
            }
        }
        echo 'process ' . $num . " cycle pushplan !\r\n" ;
        echo  date("Y-m-d H:i:s"). " run script. done!". "\r\n". "\r\n";
    }
    
    
    /**
     * 推送导量配置（针对周期性导量计划）
     * @param type $configId
     * @param type $planId
     */
    public function pushConfigCycle($configId, $planId, $cancel = false){
        $configModel=Doo::loadModel("datamodel/push/PushConfig",True);
        $pushConfig=$configModel->getPushConfigCyclePlan($configId, $planId, $cancel);
        $pushUrl = Doo::conf()->PUSH_URL;
        $returnStr = $configModel->curl()->post($pushUrl, $pushConfig, false);
        
        //记录push日志
        $this->recordPushLog(1, $pushConfig, $returnStr);
        
        $returnArr = json_decode($returnStr, true);
        if($returnArr['error_code'] == 0){
            //记录发送周期性导量计划日志
            parse_str($pushConfig, $pushConfigArr);
            //时间毫秒转秒
            $start_time = $pushConfigArr['start_time']/1000;
            $end_time = $pushConfigArr['end_time']/1000;
            $this->recordPushPlanLog($planId, $start_time, $end_time);
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 记录周期性导量计划日志
     * @param type $plan_id
     * @param type $start_time
     * @param type $end_time
     */
    public function recordPushPlanLog($plan_id, $start_time, $end_time){
        $pushplanlogModel=Doo::loadModel("datamodel/push/PushPlanLog",True);
        $send_time = time();
        $pushplanlogModel->add($plan_id, $start_time, $end_time, $send_time);
    }
    
    /**
     * 记录推送日志
     * @param type $type
     * @param type $log
     * @param type $returnStr
     */
    public function recordPushLog($type, $log, $returnStr){
        $pushlogModel=Doo::loadModel("datamodel/push/PushLog",True);
        $operator='crontab';
        $pushlogModel->add($type, $log, $operator, $returnStr);
    }
    
    
}
