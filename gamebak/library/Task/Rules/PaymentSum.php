<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-累计充值（多区间-百分比）
 * @author zzw
 */
class Task_Rules_PaymentSum extends Task_Rules_PaymentBase {
    /**
     * 计算可以获得的奖励列表
     * @param array $task       表game_client_task_hd的任务配置
     * @param string $uuid      用户ID
     * @return string           奖励列表
     */
    protected function onCalcGoods($task, $uuid) {
        $startTime = $task['hd_start_time'];
        $endTime = $task['hd_end_time'];
        
        $moneySum = $this->getTotalPayment($uuid, $startTime, $endTime);
        if ($moneySum <= 0) {
            $this->err(array('msg' => "累计充值<=0, 用户[{$uuid}]"));
            return array();
        }
        
        $money = $this->mRequest['money'];
        $goods = $this->returnACouponForSum($uuid, $task, $moneySum - $money, $moneySum);
        return $goods;
    }
    
    /**
     * 统计一段时间内的充值总金额
     * @param string $uuid      用户ID
     * @param string $startTime 统计起始时间
     * @param string $endTime   统计结束时间
     * @return string  总金额
     */
    private function getTotalPayment($uuid, $startTime, $endTime){
        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_PAYMENT; // 2是充值
        $search['trade_time'][0] = array('>=', $startTime);
        $search['trade_time'][1] = array('<=', $endTime);
        $search['uuid'] = $uuid;
        $total = Client_Service_MoneyTrade::getCount($search);
        
        $this->debug(array('msg' => "用户[{$uuid}]在{$startTime} 至{$endTime} 期间的充值总金额为{$total}"));
        return $total;
    }
    
};

