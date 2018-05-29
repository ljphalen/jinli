<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-规则类-消费规则
 * @author fanch
 *
 */
class Task_Rules_Consume extends Task_Rules_Base{

    public function onCalcSendGoods(){
        return $this->calcSendRule();
    }

    private function calcSendRule(){
        $task = $this->mTaskRecord;
        $ruleType =$task['condition_type'];
        switch($ruleType) {
            case 2: //单区间-百分比消费
                return $this->calcSingle();
                break;
            case 3: //累计消费(多区间-固定额)
                return $this->calcSumFixed();
                break;
            case 5: //单次消费(多区间-百分比)
                return $this->calcOnce(self::CALC_PERCENT);
                break;
            case 8: //单次消费(多区间-固定额)
                return $this->calcOnce(self::CALC_QUOTA);
                break;
            case 6: //每天首次消费(多区间-百分比)
                return $this->calcDaily(self::CALC_PERCENT);
                break;
            case 9: //每天首次消费(多区间-固定额)
                return $this->calcDaily(self::CALC_QUOTA);
                break;
            case 7: //累计消费(多区间-百分比))
                return $this->calcSumPercent();
                break;
            case 10: //vip(多区间-百分比)
                return $this->calcVip();
                break;
        }
    }

    /**
     * 累计消费金额-区分游戏
     * @return string
     */
    protected function getSumMoney(){
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        $apiKey = $this->mRequest['apiKey'];

        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_CONSUME;
        $search['trade_time'][0] = array('>=', $task['hd_start_time']);
        $search['trade_time'][1] = array('<=', $task['hd_end_time']);
        $search['uuid'] = $uuid;
        $search['api_key'] = $apiKey;

        $total = Client_Service_MoneyTrade::getCount($search);

        $debugMsg = array(
            'msg' => "计算用户[{$uuid}]本次任务活动期间消费总和",
            'apiKey' => $apiKey,
            'taskId' => $task['id'],
            'total' => $total
        );
        $this->debug($debugMsg);

        return $total;
    }

    /**
     * 每天首次条件
     * @return bool
     */
    protected function isDaily(){
        $apiKey = $this->mRequest['apiKey'];
        $taskStartTime = $this->mTaskRecord['hd_start_time'];
        $startDay = date('Y-m-d', $taskStartTime);
        $taskEndTime = $this->mTaskRecord['hd_end_time'];
        $endDay = date('Y-m-d', $taskEndTime);
        $currentDay = date('Y-m-d', Common::getTime());
        if($startDay == $currentDay){
            $startTime = $taskStartTime;
        }else{
            $startTime = strtotime($currentDay . ' 00:00:00');
        }

        if($endDay == $currentDay){
            $endTime = $taskEndTime;
        } else {
            $endTime = strtotime($currentDay . ' 23:59:59');
        }
        $params = array(
            'event' => Client_Service_MoneyTrade::TRADE_EVENT_CONSUME,
            'uuid' => $this->mRequest['uuid'],
            'trade_time' => array(array('>=', $startTime), array('<=', $endTime)),
            'api_key' => $apiKey
        );

        $tradeTotal  = Client_Service_MoneyTrade::getsBy($params);

        if(1 == count($tradeTotal)){
            return true;
        }
        return false;
    }
}