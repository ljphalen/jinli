<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 赠送系统-返利活动-登录客户端-基类
 * @author fanch
 * 
 */
 abstract class Task_Rules_ConsumeBase extends Task_Rules_Base {
     
	abstract public function onCaculateGoodsForConsume($task, $request);

     /**
      * 解析规则方法
      * @see Task_Rules_Base::onCaculateGoods()
      */
     public function onCaculateGoods(){
         $task = $this->mTaskRecord;
         $request = $this->mRequest;
         
         //参与用户过滤
         $filterResult = $this->filterUserByUuid($task, $request['uuid']);
         
         $debugMsg = array(
                 'msg' => "判断用户[{$request['uuid']}]是否允许参加本次任务",
                 'taskId' => $task['id'],
                 'filterResult' => $filterResult
         );
         $this->debug($debugMsg);
         
         if(!$filterResult){
             return array();
         }
         
         //参与游戏过滤
         $result = $this->canPartiActivity($task, $request['apiKey']);
         if (!$result) { 
             return array(); 
         }
         
         //特殊条件处理
         $result = $this->filterSpecialCondition($task ,$request);
         if (!$result) {
             return array();
         }
         
         //计算规则
         $sendGoods = $this->onCaculateGoodsForConsume($task, $request);
     
         $debugMsg = array(
                 'msg' => "计算用户[{$request['uuid']}]本次任务赠送的物品",
                 'taskId' => $task['id'],
                 'sendGoods' => $sendGoods
         );
         $this->debug($debugMsg);
         
         return $sendGoods;
     }
     
     /**
      * 特殊条件判断
      * @param array $task
      * @param array $request
      * @return boolean
      */
     protected function filterSpecialCondition($task ,$request){
         return true;
     }  

     /**
      * 查询当前活动进行期间用户A币消费的总和
      * @param array $task
      * @param string $uuid
      * @return num
      */
     protected function getUserConsumeMoney($task, $uuid){
         $total = 0;
         $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_CONSUME;
         $search['trade_time'][0] = array('>=', $task['hd_start_time']);
         $search['trade_time'][1] = array('<=', $task['hd_end_time']);
         $search['uuid'] = $uuid;
         $total = Client_Service_MoneyTrade::getCount($search);
          
         $debugMsg = array(
                 'msg' => "计算用户[{$uuid}]本次任务活动期间消费总和",
                 'taskId' => $task['id'],
                 'total' => $total
         );
         $this->debug($debugMsg);
          
         return $total;
     }
 }
