<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-累计消费[多区间-固定额] 3
 * @author fanch
 * 
 * 请求参数内容如下
 */
 class Task_Rules_ConsumeSumQuota extends Task_Rules_ConsumeBase {

	
	/**
	 * 计算赠送规则
	 */
	public function onCaculateGoodsForConsume(){
        $consumeTotal = $this->getUserConsumeMoney();
        if($consumeTotal < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        //根据消费总额计算本次赠送的A券
        $goods = $this->returnQuotaAcouponForSum($consumeTotal);
        return $goods;
	}


}
