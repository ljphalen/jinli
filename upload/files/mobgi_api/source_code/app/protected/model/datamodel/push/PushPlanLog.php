<?php
Doo::loadModel('datamodel/base/PushPlanLogBase');

class PushPlanLog extends PushPlanLogBase{
    /**
     * 新增push日志
     * @param type $plan_id
     * @param type $start_time
     * @param type $end_time
     * @param type $send_time
     * @param type $operator
     * @return type
     */
    public function add($plan_id, $start_time, $end_time, $send_time){
        $this->plan_id = $plan_id;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->send_time = $send_time;
        return $this->insert();
    }

    /**
     * 获取某个周期性导量计划最新分拆的push导量日志
     * @param type $planid
     * @return type
     */
    public function getLatestPushplanlogByPlanid($planid){
        $sql = 'select * from push_plan_log where plan_id = '. $planid. ' order by send_time desc limit 1;';
        $result = Doo::db()->query($sql)->fetchAll();
        return $result[0];
    }
    
}