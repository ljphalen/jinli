<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 赠送系统-返利活动-登录客户端-每天首次消费[旧版]
 * @author fanch
 * 
 * 请求参数内容如下
 * $reqeust =array(
 *   'uuid'=>$uuid, 
 *	 'money'=>$money, 
 *	 'api_key'=>$api_key
 * )
 */
 class Task_Rules_ConsumeDailyFirst extends Task_Rules_ConsumeBase {
     
     /**
      * 特殊条件过滤
      * @param array $task
      * @param array $request
      * @return 
      */
     protected function filterSpecialCondition($task ,$request){
         //每天首次消费过滤
         $filterResult = $this->isConsumeDailyFirst($request);
         
         $debugMsg = array(
                 'msg' => "判断用户[{$request['uuid']}]是否满足该任务的每日首次消费的赠送条件",
                 'taskId' => $task['id'],
                 'filterResult' => $filterResult
         );
         $this->debug($debugMsg);
         
         return $filterResult;
     }

	
     /**
      * 计算赠送规则
      * @param  array $task
      * @param  array $uuid
      */
    public function onCaculateGoodsForConsume($task, $request){
        $ruleConfig = json_decode($task['rule_content'], true);
        $money = $request['money'];
        $uuid = $request['uuid'];
        
        if($money < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }
        
        $sendGoods = $this->returnACoupon($uuid, $task, $money);
        return $sendGoods;
    }
  

    /**
     * 每天首次消费过滤
     * @param array $task
     * @param array $request
     */
    private function isConsumeDailyFirst($request){
        $currentTime = date('Y-m-d', Common::getTime());
        $startTime = strtotime($currentTime . ' 00:00:00');
        $endTime = strtotime($currentTime . ' 23:59:59');
        $params = array(
                'event' => Client_Service_MoneyTrade::TRADE_EVENT_CONSUME,
                'uuid' => $request['uuid'],
                'trade_time' => array(array('>=', $startTime), array('<=', $endTime))
        );
    
        $tradeTotal  = Client_Service_MoneyTrade::getsBy($params);

        if(1 == count($tradeTotal)){
            return true;
        }
        return false;
    }
}
