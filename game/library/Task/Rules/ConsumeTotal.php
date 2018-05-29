<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-累计消费[新]
 * @author fanch
 * 
 * 请求参数内容如下
 * $reqeust =array(
 *   'uuid'=>$uuid, 
 *	 'money'=>$money, 
 *	 'api_key'=>$api_key
 * )
 */
 class Task_Rules_ConsumeTotal extends Task_Rules_ConsumeBase {

     /**
      * 计算赠送规则
      * @param  array $task
      * @param  array $uuid
      */
     public function onCaculateGoodsForConsume($task, $request){
         $ruleConfig = json_decode($task['rule_content'], true);
         if(!$ruleConfig){
             return array();
         }
         $money = $request['money'];
         $uuid = $request['uuid'];
         $consumeTotal = $this->getUserConsumeMoney($task, $uuid);
         if($consumeTotal < Client_Service_TaskHd::MIN_AMOUNT){
             return array();
         }
          
         //根据消费总额计算本次赠送的A券
         $goods = $this->returnACouponForSum($uuid, $task, $consumeTotal - $money, $consumeTotal);
         return $goods;
     }
}
