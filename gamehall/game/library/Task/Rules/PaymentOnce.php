<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-单次充值（多区间-百分比）3
 * @author zzw
 */
class Task_Rules_PaymentOnce extends Task_Rules_PaymentBase {
    
    /**
     * 用户对象过滤
     * @return boolean
     */
    protected function onCalcGoods() {
        $goods = $this->returnACoupon();
        return $goods;
    }
};
