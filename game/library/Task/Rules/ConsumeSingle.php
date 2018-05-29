<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-单次消费[新版]
 * @author fanch
 * 
 * 请求参数内容如下
 * $reqeust =array(
 *   'uuid'=>$uuid, 
 *	 'money'=>$money, 
 *	 'api_key'=>$api_key
 * )
 */
 class Task_Rules_ConsumeSingle extends Task_Rules_ConsumeBase {
     
     /**
      * 计算赠送规则
      * @param  array $task
      * @param  array $uuid
      */
    public function onCaculateGoodsForConsume($task, $request){
        $ruleConfig = json_decode($task['rule_content'], true);
        $money = $request['money'];
        $uuid = $request['uuid'];
        $sendMoney = 0 ;
        if($money < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }
        
        $sendGoods = $this->returnACoupon($uuid, $task, $money);
        return $sendGoods;
    }
}
