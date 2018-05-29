<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-单次消费[多区间-百分比] 5
 * @author fanch
 *
 */
class Task_Rules_ConsumeOnce extends Task_Rules_ConsumeBase {

    /**
     * 计算赠送规则
     */
    public function onCaculateGoodsForConsume(){
        if($this->mRequest['money'] < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        $sendGoods = $this->returnACoupon();
        return $sendGoods;
    }
}
