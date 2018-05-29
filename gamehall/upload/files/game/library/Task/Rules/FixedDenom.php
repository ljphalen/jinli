<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-规则类-固定面额规则
 * @author fanch
 *
 */
class Task_Rules_FixedDenom extends Task_Rules_Base{

    public function onCalcSendGoods(){
        $ruleConfig = json_decode($this->mTaskRecord['rule_content'], true);
        $sendGoods['Acoupon'][] = $this->createAcouponItem(
            $ruleConfig['denomination'],
            Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY,
            $ruleConfig['deadline']
        );
        return $sendGoods;
    }

}