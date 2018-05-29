<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-VIP特权返利[多区间-百分比]
 * @author fanch
 *
 */
class Task_Rules_ConsumeVip extends Task_Rules_ConsumeBase {

    public function onCaculateGoodsForConsume(){
        if($this->mRequest['money'] < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        $sendGoods = $this->returnVipAcoupon();
        return $sendGoods;
    }
}
