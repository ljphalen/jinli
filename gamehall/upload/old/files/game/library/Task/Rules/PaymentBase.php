<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-基类
 * @author zzw
 */
abstract class Task_Rules_PaymentBase extends Task_Rules_Base {
    /**
     * 解析执行规则
     * @param array $task      表game_client_task_hd的任务配置
     * @param string $uuid     用户ID
     * @return boolean
     */
    abstract protected function onCalcGoods($task, $uuid);
    
    /**
     * 解析执行规则
     */
    public function onCaculateGoods() {
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
         
        $result = $this->filterUserByUuid($task, $uuid);
        if (!$result) {
            return array(); 
        }

        $result = $this->canPartiActivity($task, $this->mRequest['apiKey']);
        if (!$result) { return array(); }
        
        return $this->onCalcGoods($task, $uuid);
    }
    
    /**
     * 是否是今天首次付费
     * @param array $task
     * @param string $uuid
     * @return boolean
     */
    protected function isFirstPayment($task, $uuid) {
        $currentTime = date('Y-m-d',Common::getTime());
        $startTime = strtotime($currentTime.' 00:00:00');
        $endTime = strtotime($currentTime.' 23:59:59');
        
        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_PAYMENT; // 2是充值
        $search['trade_time'][0] = array('>=', $startTime);
        $search['trade_time'][1] = array('<=', $endTime);
        $search['uuid'] = $uuid;
        
        $result = Client_Service_MoneyTrade::getsBy($search);
        $count = count($result);
        if (1 == $count) {
            return true;
        }
        $this->debug(array('msg' => "每日充值返利,用户[{$uuid}]已经充值过了,总次数[{$count}],任务ID[{$task['id']}]"));
        return false;
    }
};
