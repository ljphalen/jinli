<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 充值返利活动-基类
 * @author zzw
 */
abstract class Task_Rules_PaymentBase extends Task_Rules_Base {
    /**
     * 解析执行规则
     * @return boolean
     */
    abstract protected function onCalcGoods();
    
    /**
     * 解析执行规则
     */
    public function onCaculateGoods() {
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
         
        $result = $this->filterUserByUuid();
        if (!$result) {
            return array(); 
        }

        $result = $this->canPartiActivity();
        if (!$result) { return array(); }
        
        return $this->onCalcGoods($task, $uuid);
    }
    
    /**
     * 是否是今天首次付费
     * @param array $task
     * @param string $uuid
     * @return boolean
     */
    protected function isFirstPayment() {
        $currentTime = date('Y-m-d',Common::getTime());
        $startTime = strtotime($currentTime.' 00:00:00');
        $endTime = strtotime($currentTime.' 23:59:59');
        
        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_PAYMENT; // 2是充值
        $search['trade_time'][0] = array('>=', $startTime);
        $search['trade_time'][1] = array('<=', $endTime);
        $search['uuid'] = $this->mRequest['uuid'];
        
        $result = Client_Service_MoneyTrade::getsBy($search);
        $count = count($result);
        if (1 == $count) {
            return true;
        }
        $this->debug(array('msg' => "每日充值返利,用户[{$this->mRequest['uuid']}]已经充值过了,总次数[{$count}],任务ID[{$this->mTaskRecord['id']}]"));
        return false;
    }


    /**
     * 统计一段时间内的充值总金额，
     * 1.6.1 增加[全平台|单游戏]累计计算方式
     * @return string  总金额
     */
    protected function getTotalPayment(){
        $sumType = $this->mTaskRecord['condition_value'];
        $startTime = $this->mTaskRecord['hd_start_time'];
        $endTime = $this->mTaskRecord['hd_end_time'];
        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_PAYMENT; // 2是充值
        $search['trade_time'][0] = array('>=', $startTime);
        $search['trade_time'][1] = array('<=', $endTime);
        $search['uuid'] = $this->mRequest['uuid'];
        if($sumType == Client_Service_Acoupon::PAYMENT_SINGLE_GAME){
            $search['api_key'] = $this->mRequest['apiKey'];
        }

        $total = Client_Service_MoneyTrade::getCount($search);

        $this->debug(array('msg' => "用户[{$this->mRequest['uuid']}]在{$startTime} 至{$endTime} 期间的充值总金额为{$total}"));
        return $total;
    }
};
