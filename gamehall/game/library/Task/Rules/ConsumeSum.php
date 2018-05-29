<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-累计消费[多区间-百分比] 7
 * @author fanch
 *
 */
 class Task_Rules_ConsumeSum extends Task_Rules_ConsumeBase {

     /**
      * 计算赠送规则
      */
     public function onCaculateGoodsForConsume(){
         $money = $this->mRequest['money'];
         $consumeTotal = $this->getUserConsumeMoney();
         if($consumeTotal < Client_Service_TaskHd::MIN_AMOUNT){
             return array();
         }
          
         //根据消费总额计算本次赠送的A券
         $goods = $this->returnACouponForSum($consumeTotal - $money, $consumeTotal);
         return $goods;
     }
}
