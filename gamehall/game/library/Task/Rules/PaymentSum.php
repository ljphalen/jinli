<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-累计充值（多区间-百分比）4
 * @author zzw
 */
class Task_Rules_PaymentSum extends Task_Rules_PaymentBase {
    /**
     * 计算可以获得的奖励列表
     * @return string           奖励列表
     */
    protected function onCalcGoods() {
        $moneySum = $this->getTotalPayment();
        if ($moneySum <= 0) {
            $this->err(array('msg' => "累计充值<=0, 用户[{$this->mRequest['uuid']}]"));
            return array();
        }
        
        $money = $this->mRequest['money'];
        $goods = $this->returnACouponForSum($moneySum - $money, $moneySum);
        return $goods;
    }

};

