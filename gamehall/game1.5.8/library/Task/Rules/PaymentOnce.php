<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-单次充值（多区间-百分比）
 * @author zzw
 */
class Task_Rules_PaymentOnce extends Task_Rules_PaymentBase {
    
    /**
     * 用户对象过滤
     * @param array $task      表game_client_task_hd的任务配置
     * @param string $uuid     用户ID
     * @return boolean
     */
    protected function onCalcGoods($task, $uuid) {
        $goods = $this->returnACoupon($uuid, $task, $this->mRequest['money']);
        return $goods;
    }
};
