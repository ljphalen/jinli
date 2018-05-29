<?php
include 'common.php';
//用户当天统计数据
Gionee_Service_Log::userCenterDayDataReport();
$msg = date('Y-m-d H:i:s')."=>keyMain:".'userCenterDayReport';
Gionee_Service_CronLog::add(array('type'=>'user','msg'=>$msg));

User_Service_Earn::resetUserDays();//重置用户签到天数

//金币兑换通话时长日报
Gionee_Service_VoIPUser::collectPerDayExchangeData();
$msg = date('Y-m-d H:i:s')."=>keyMain:".'voipExcnageDayReport';
Gionee_Service_CronLog::add(array('type'=>'user','msg'=>$msg));


//经验等级用户统计
 Gionee_Service_Log::writeLevelUserAmountToDb();
 $msg = date('Y-m-d H:i:s')."=>keyMain:".'userExperienceLevelData';
 Gionee_Service_CronLog::add(array('type'=>'user','msg'=>$msg));

 //同一IP多账号限制
 User_Service_DubiousIp::addDubiousIps();
 
 echo CRON_SUCCESS;
