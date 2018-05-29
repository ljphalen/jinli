<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-每天首次充值（单区间-百分比）
 * @author zzw
 */
class Task_Rules_PaymentDailyOld extends Task_Rules_PaymentBase {
    
    /**
     * 解析执行规则
     * @param array $task      表game_client_task_hd的任务配置
     * @param string $uuid     用户ID
     * @return boolean
     */
    protected function onCalcGoods($task, $uuid) {
        $taskId = $task['id'];
        $isFirst = $this->isFirstPayment($task, $uuid);
        if (!$isFirst) {
            return array();
        }
        
        $ruleConfig = json_decode($task['rule_content'], true);
        if ($this->mRequest['money'] >= $ruleConfig['denomination']) {
            $sendMoney = round($this->mRequest['money'] * $ruleConfig['restoration']) / 100.0;
            if ($sendMoney < 0.01) {
                $this->debug(array('msg' => "赠送金额[{$sendMoney}]小于0.01,用户[{$uuid}],充值金额[{$this->mRequest['money']}],任务ID[{$taskId}]"));
                return array(); 
            }
            
            $sendGoods["Acoupon"][] = $this->createAcouponItem($task, $uuid, $sendMoney, Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY, $ruleConfig['deadline']);
            $this->debug(array('msg' => "用户[{$uuid}]获得返利[{$sendMoney}],充值金额[{$this->mRequest['money']}],任务ID[{$taskId}]"));
                
            return $sendGoods;
        }
        return array();
    }
};
