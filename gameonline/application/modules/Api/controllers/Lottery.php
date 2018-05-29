<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LotteryController extends Api_BaseController {
	
	public $perpage = 10;
	public $awards = array(
			1 => '一等奖',
			2 => '二等奖',
			3 => '三等奖',
	);
	
	public $grant_status = array(
			1 => '已发放',
			0 => '未发放',
	);
    
    /**
     * 中奖记录接口
     */
    public function myawardAction() {
    	$imei = $this->getInput('imei');
    	$uname = $this->getInput('uname');
    	if(!$uname) $this->clientOutput(array());

    	$logs = Client_Service_FateLog::getFateLogByUname($uname);
    	//判断是否有数据
    	if(!$logs) $this->clientOutput(array());
    	
    	//检测用户是否在线
    	$online_status = Account_Service_User::checkOnline($uname, $imei);
    	//客服电话
    	$customer_phone = Game_Service_Config::getValue('game_service_tel');
    	
    	$tmp = $temp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['uname'] = $uname;
    	$tmp['cuphone'] = $customer_phone;
    	$tmp['isLogin'] = ($online_status ? 1 : 0);
    	foreach($logs as $key=>$value){
    		if($value['lottery_id']){  //只取出中奖的记录
    			$activity = Client_Service_Activity::get($value['activity_id']);
    			$award = Client_Service_Activity::getOneRule($value['activity_id'],$value['lottery_id']);
    			$award_name = $this->awards[$award['lottery_id']].":".$award['award_name'];
    			$temp[] = array(
    					'isGrant'=>$value['grant_status'] > 0 ? true : false,
    					'luckyLevel'=>$this->awards[$award['lottery_id']],
    					'luckyContent'=>$award['award_name'],
    					'luckyTime'=>date("Y-m-d",$value['create_time']),
    					'grantTime'=>$value['grant_time'] ? date("Y-m-d",$value['grant_time']) : '无',
    			);
    		}
    		
    	}
    	//没有用户的抽奖记录
    	if(!$temp) $this->clientOutput(array());
    	
    	$tmp['data'] = $temp;
    	$this->clientOutput($tmp);
    }
    
    /**
     * 抽奖活动数据结构
     */
    public function activityAction() {
    	$activity_id = $this->getInput('activity_id');
    	$uname = $this->getInput('uname');
    	$prize_version = $this->getInput('activity_version');
    	$version = $this->getInput('version');
    	$imei = $this->getInput('imei');
    	//imei为空
    	//if($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510') $this->clientOutput(array());
    	//小于等于1.4.9不返回数据
    	if(strnatcmp($version, '1.4.9') <= 0) $this->clientOutput(array());
    	
    	$imeicrc = crc32(trim($imei));
    	$webroot = Common::getWebRoot();
    	$sign = 'GioneeGameHall';
    	$tmp = $temp = array();
    	$tmp['sign'] = $sign;
    	$tmp['prizeUname'] = $uname;
    	//$tmp['prizeState'] = 1;
    	
    	
    	//检测用户是否在线
    	$online_status = Account_Service_User::checkOnline($uname, $imei);    	
    	
    	//当前线上的活动
    	$activityInfo = Client_Service_Activity::getOnlineActivityInfo();
    	//没有活动
    	if(!$activityInfo)  $this->clientOutput(array());
    	
       	
    	
    	
    	//如果当前版本为空，则默认为1.4.9
    	if(!$activityInfo['min_version']) $activityInfo['min_version'] = '1.4.9';
    	//当前版本小于活动版本(至少为1.4.9以上)不能抽奖活动
    	if(strnatcmp($version, $activityInfo['min_version']) < 0) $this->clientOutput(array());
    	
    	//当前抽奖的奖项
    	$jianx_info = Client_Service_FateRule::getFateRuleByActivityId($activityInfo['id']);
    	//获取用户日志
    	$logs = Client_Service_FateLog::getFateLogsByUname($uname,$activityInfo['id']);
    	//剩余抽奖次数
		$num = $activityInfo['number'] - count($logs);
		
		//没有活动或活动结束
		if(!$activityInfo)  $num = 0;
		$tmp['prizeChance'] = ($online_status ? ($num > 0 ? $num : 0) : -1);
		
		//已经访问过并且活动状态未发生变更
		if($prize_version == $activityInfo['update_time'])  $this->clientOutput($tmp);
    	
    	$tmp['prizeId'] = $activityInfo['id'];
    	$tmp['prizeVersion'] = intval($activityInfo['update_time']);
    	$tmp['prizeName'] = $activityInfo['name'];
    	$tmp['prizeDesUrl'] = $webroot. '/client/activity/detail/?id='.$activityInfo['id'];
    	if ($activityInfo['popup_status'])  $tmp['prizePopup'] = $activityInfo['message'];
    	$tmp['prizeBackGroundIconUrl'] = Common::getAttachPath(). $activityInfo['img'];
    	
    	foreach($jianx_info as $key=>$value){
    			$award = Client_Service_Activity::getOneRule($value['activity_id'],$value['lottery_id']);
    			$award_name = $this->awards[$award['lottery_id']].":".$award['award_name'];
    			$temp[] = array(
    					'prizeLargeIconUrl'=>Common::getAttachPath().$award['icon'],
    					'prizeSmallIconUrl'=>Common::getAttachPath().$award['img'],
    					//'luckyContent'=>$award_name,
    			);
    	}
    	$tmp['prizeIcons'] = $temp;
    	$this->clientOutput($tmp);
    }
    
    /**
     * 开始抽奖数据结构
     */
    public function lotteryAction() {
    	$data = $this->getInput(array('imei','version','activity_id','uname'));
    	$webroot = Common::getWebRoot();
    	$sign = 'GioneeGameHall';
    	$lottery_data = array();
    	$lottery_data = Client_Service_Fate::fate($data);
    	if(!$lottery_data) $this->clientOutput(array());
    	$lottery_data['sign'] = $sign;
    	$this->clientOutput($lottery_data);
    }
    
    /**
     * 抽奖的状态
     * @param unknown_type $data
     * @return number
     */
    private  function _activityData($data) {
    	if(!$data)  return -1;
    	$curr_time = Common::getTime();
    	//活动未开始
    	if( $data['online_start_time'] > $curr_time || $data['online_end_time'] < $curr_time ) return 2;
    	//抽奖未开始
    	if( $data['online_start_time'] > $curr_time ) return -1;
    	if( $data['online_start_time'] <= $curr_time  && $data['start_time'] > $curr_time ) return -1;
    	//抽奖开始
    	if( $data['start_time'] <= $curr_time  && $data['end_time'] >= $curr_time ) return 0;
    	//抽奖抽奖已经结束
    	if( $data['end_time'] < $curr_time ) return 1;
    }
    
    public function testAction() {
    	$ts = Client_Service_Fate::test();
    	$this->clientOutput($ts);
    }
}