<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-单次消费[单区间-百分比]
 * @author fanch
 * 
 */
 class Task_Rules_ConsumeSingle extends Task_Rules_ConsumeBase {
	
	/**
	 * 计算要赠送A券
	 */
	public function onCaculateGoodsForConsume(){
        $task = $this->mTaskRecord;
		$ruleConfig = json_decode($task['rule_content'], true);
		$money = $this->mRequest['money'];
		$uuid = $this->mRequest['uuid'];
		$sendMoney = 0 ;
		if($money < Client_Service_TaskHd::MIN_AMOUNT){
		    return array();
		}
		//单笔消费赠送规则
		if($money >= $ruleConfig['denomination']){
		    $sendMoney  = ($money * $ruleConfig['restoration'])/100;
		    $sendMoney = number_format($sendMoney, 2, '.', '');
		    
		    $debugMsg = array(
		            'msg' => "计算用户[{$this->mRequest['uuid']}]本次任务返还的金额",
		            'taskId' => $task['id'],
		            'sendMoney' => $sendMoney
		    );
		    $this->debug($debugMsg);
		    
		    if($sendMoney < Client_Service_TaskHd::MIN_AMOUNT){
		        return array();
		    }
		}
		
		//根据配置组装A券数据
		$sendGoods["Acoupon"][]  =$this->createAcouponItem($sendMoney, Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY, $ruleConfig['deadline']);
		return $sendGoods;
	}
}
