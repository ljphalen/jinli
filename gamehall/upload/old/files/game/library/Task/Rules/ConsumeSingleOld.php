<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-登录客户端-单次消费[旧版]
 * @author fanch
 * 
 * 请求参数内容如下
 * $reqeust =array(
 *   'uuid'=>$uuid, 
 *	 'money'=>$money, 
 *	 'api_key'=>$api_key
 * )
 */
 class Task_Rules_ConsumeSingleOld extends Task_Rules_ConsumeBase {
	
	/**
	 * 计算要赠送A券
	 * @param  array $task
	 * @param  array $uuid
	 */
	public function onCaculateGoodsForConsume($task, $request){
		$ruleConfig = json_decode($task['rule_content'], true);
		$money = $request['money'];
		$uuid = $request['uuid'];
		$sendMoney = 0 ;
		if($money < Client_Service_TaskHd::MIN_AMOUNT){
		    return array();
		}
		//单笔消费赠送规则
		if($money >= $ruleConfig['denomination']){
		    $sendMoney  = ($money * $ruleConfig['restoration'])/100;
		    $sendMoney = number_format($sendMoney, 2, '.', '');
		    
		    $debugMsg = array(
		            'msg' => "计算用户[{$request['uuid']}]本次任务返还的金额",
		            'taskId' => $task['id'],
		            'sendMoney' => $sendMoney
		    );
		    $this->debug($debugMsg);
		    
		    if($sendMoney < Client_Service_TaskHd::MIN_AMOUNT){
		        return array();
		    }
		}
		
		//根据配置组装A券数据
		$sendGoods["Acoupon"][]  =$this->createAcouponItem($task, $uuid, $sendMoney, Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY, $ruleConfig['deadline']);
		return $sendGoods;
	}
}
