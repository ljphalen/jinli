<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-消费返利-累计消费[旧版]
 * @author fanch
 * 
 * 请求参数内容如下
 * $reqeust =array(
 *   'uuid'=>$uuid, 
 *	 'money'=>$money, 
 *	 'api_key'=>$api_key
 * )
 */
 class Task_Rules_ConsumeTotalOld extends Task_Rules_ConsumeBase {

	
	/**
	 * 计算赠送规则
	 * @param  array $task
	 * @param  array $uuid
	 */
	public function onCaculateGoodsForConsume($task, $request){
		$ruleConfig = json_decode($task['rule_content'], true);
		if(!$ruleConfig){
		    return array();
		}
		$money = $request['money'];
		$uuid = $request['uuid'];
		$consumeTotal = $this->getUserConsumeMoney($task, $uuid);
		if($consumeTotal < Client_Service_TaskHd::MIN_AMOUNT){
		    return array();
		}
		$lastSection = $this->getLastSection($task, $uuid);
		
		//根据上次返还最大区间结束值计算本次赠送的区间
		$grantSection = $this->getSendSection($task, $uuid, $consumeTotal, $lastSection);
		
		$debugMsg = array(
		        'msg' => "计算用户[{$request['uuid']}]在本次任务中适用的返还百分比区间",
		        'taskId' => $task['id'],
		        'grantSection' => $grantSection
		);
		$this->debug($debugMsg);
		
		if(!$grantSection){
		    return array();
		}
		
		//根据配置组装A券数据
		$grantAcoupon = $this->getGrantAcouponBySection($task, $uuid, $grantSection);
		if(empty($grantAcoupon)){
		    return array();
		}
		$sendGoods['Acoupon'] = $grantAcoupon;
		return $sendGoods;
	}


	/**
	 * 查询当前活动进行期间用户消费上次使用的返还区间
	 * @param array $task
	 * @param string $uuid
	 */
	private function getLastSection($task, $uuid){
	    $search['uuid'] = $uuid;
	    $search['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
	    $search['consume_time'][0] = array('>=', $task['hd_start_time']);
	    $search['consume_time'][1] = array('<=', $task['hd_end_time']);
	    $search['sub_send_type'] = $task['id'];
	
	    $sentTicket = Client_Service_TicketTrade::getsBy($search, array('consume_time'=>'DESC'));
	    $lastSection = 0;
	    $sentSection = array();
	    foreach ($sentTicket as $val){
	        $sentSection = json_decode($val['densection'], true);
	        if($lastSection < $sentSection['section_end']){
	            $lastSection = $sentSection['section_end'];
	        }
	    }

	    $debugMsg = array(
	            'msg' => "计算用户[{$uuid}]本次任务活动进行期间消费已返还的开始区间",
	            'taskId' => $task['id'],
	            'uuid' => $uuid,
	            'lastSection' => $lastSection
	    );
	    $this->debug($debugMsg);
	    
	    return $lastSection;
	}
	 
	/**
	 * 根据用户消费金额与上次最大的返还区间，获取本次赠送使用的返还区间
	 *
	 * @param array  $task
	 * @param string $uuid
	 * @param string $consumeTotal
	 * @param string $lastSection
	 */
	private function getSendSection($task, $uuid, $consumeTotal, $lastSection){
	    $section = array();
	    $ruleConfig = json_decode($task['rule_content'], true);
	    $findFirstSection = 0;
	    foreach($ruleConfig as $key=>$value){
	        //有赠送记录
	        if($lastSection){
	            //取出上一个赠送区间
	            if ($lastSection >= $value['section_end']) {
	                continue;
	            }
	            if($lastSection >= $value['section_start']){
	                $findFirstSection = 1;
	            }
	            //没有赠送记录
	        } else {
	            $findFirstSection = 1;
	        }
	        //合并用于赠送的返还区间
	        if($findFirstSection == 1 && $consumeTotal >= $value['section_start']) {
	            $section[] = $value;
	        }
	    }
	    
	    return $section;
	}
	
	/**
	 * 根据返还区间计算返还的A券
	 * @param array  $task
	 * @param string $uuid
	 * @param string $grantSection
	 */
	private function getGrantAcouponBySection($task, $uuid, $grantSection){
	    $grantData = $jsonData =  array();
	    foreach ($grantSection as $section){
	        $jsonData  = json_encode(array(
	                'section_start'=>$section['section_start'],
	                'section_end'=>$section['section_end']
	        ));
	        foreach($section['denarr'] as $item){
	            $grantData[] = $this->createAcouponItem($task, $uuid, $item['Step'], $item['effect_start_time'], $item['effect_end_time'], $jsonData);
	        }
	    }
	    return $grantData;
	}
}
