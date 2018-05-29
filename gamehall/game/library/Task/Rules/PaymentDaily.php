<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-每天首次充值（多区间-百分比）2
 * @author zzw
 */
class Task_Rules_PaymentDaily extends Task_Rules_PaymentBase {
    /**
     * 解析执行规则
     * @param array $task      表game_client_task_hd的任务配置
     * @param string $uuid     用户ID
     * @return boolean
     */
    protected function onCalcGoods() {
        $isFirst = $this->isFirstPayment();
        if (!$isFirst) {
            return array();
        }
        
        $goods = $this->returnACoupon();
        return $goods;
    }
};
