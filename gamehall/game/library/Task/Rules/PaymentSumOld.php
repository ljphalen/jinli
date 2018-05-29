<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-累计充值（多区间-固定额）7
 * @author zzw
 */
class Task_Rules_PaymentSumOld extends Task_Rules_PaymentBase {
    /**
     * 计算可以获得的奖励列表
     * @param array $task       表game_client_task_hd的任务配置
     * @param string $uuid      用户ID
     * @return string           奖励列表
     */
    protected function onCalcGoods() {
        $paymentTotal = $this->getTotalPayment();
        if ($paymentTotal <= 0) {
            $this->err(array('msg' => "累计充值<=0, 用户[{$this->mRequest['uuid']}]"));
            return array();
        }

        $goods = $this->returnQuotaAcouponForSum($paymentTotal);
        return $goods;
    }
};

