<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 赠送系统-返利活动-登录客户端-每天首次消费[多区间-百分比] 6
 * @author fanch
 *
 */
class Task_Rules_ConsumeDaily extends Task_Rules_ConsumeBase {

    /**
     * 特殊条件过滤
     * @return
     */
    protected function filterSpecialCondition(){
        //每天首次消费过滤
        $filterResult = $this->isConsumeDailyFirst();

        $debugMsg = array(
            'msg' => "判断用户[{$this->mRequest['uuid']}]是否满足该任务的每日首次消费的赠送条件",
            'taskId' => $this->mTaskRecord['id'],
            'filterResult' => $filterResult
        );
        $this->debug($debugMsg);

        return $filterResult;
    }


    /**
     * 赠送规则计算
     */
    public function onCaculateGoodsForConsume(){
        if($this->mRequest['money'] < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }
        $sendGoods = $this->returnACoupon();
        return $sendGoods;
    }


    /**
     * 每天首次消费过滤
     */
    private function isConsumeDailyFirst(){
        $currentTime = date('Y-m-d', Common::getTime());
        $startTime = strtotime($currentTime . ' 00:00:00');
        $endTime = strtotime($currentTime . ' 23:59:59');
        $params = array(
            'event' => Client_Service_MoneyTrade::TRADE_EVENT_CONSUME,
            'uuid' => $this->mRequest['uuid'],
            'trade_time' => array(array('>=', $startTime), array('<=', $endTime))
        );

        $tradeTotal  = Client_Service_MoneyTrade::getsBy($params);

        if(1 == count($tradeTotal)){
            return true;
        }
        return false;
    }
}
