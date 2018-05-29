<?php
include 'common.php';

// 日常任务初始化
$dailyTaskId = array(2,3,4);
foreach ($dailyTaskId as $val){
	$dailyTaskPointsParams = $dailyTaskTicketsParams = $dailyTotalNumResult = array();
	$dailyTaskPointsResult = $dailyTicketsResult = $dailyTotalNumResult = 0;
	//总积分
	$dailyTaskPointsParams['status'] = Util_Activity_Context::FINISHED_STATUS;
	$dailyTaskPointsParams['task_id'] = $val;
	$dailyTaskPointsParams['send_object'] = Util_Activity_Context::SEND_POINTS;
	$dailyTaskPointsResult = Client_Service_DailyTaskLog::getTotal($dailyTaskPointsParams);
	if($dailyTaskPointsResult){
		Client_Service_TaskStatisticReport::updateStatisticReportPoints(Util_Activity_Context::TASK_TYPE_DAILY_TASK, $val, $dailyTaskPointsResult);
	}
	
	//总A券
	$dailyTaskTicketsParams['status'] = Util_Activity_Context::FINISHED_STATUS;
	$dailyTaskTicketsParams['task_id'] = $val;
	$dailyTaskTicketsParams['send_object'] = Util_Activity_Context::SEND_TICKET;
	$dailyTicketsResult = Client_Service_DailyTaskLog::getTotal($dailyTaskTicketsParams);
	if($dailyTicketsResult){
		Client_Service_TaskStatisticReport::updateStatisticReporTickets(Util_Activity_Context::TASK_TYPE_DAILY_TASK, $val, $dailyTicketsResult);
	}
	
	//总人数
	$dailyTaskTotalNumParams['status'] = Client_Service_DailyTaskLog::FINISHED_STATUS;
	$dailyTaskTotalNumParams['task_id'] = $val;
	$dailyTotalNumResult = Client_Service_DailyTaskLog::getNum($dailyTaskTotalNumParams);
	if($dailyTotalNumResult){
		Client_Service_TaskStatisticReport::updateStatisticReporPeopleNum(Util_Activity_Context::TASK_TYPE_DAILY_TASK, $val, $dailyTotalNumResult);
	}
}	


// 福利任务任务初始化
$wealTaskId = array(1,2,3,4,5,6);
foreach ($wealTaskId as $val){
	$wealTicketsResult = $wealTotalNumResult = 0;
	//总A券
	$wealTaskPointsParams['status'] = Util_Activity_Context::FINISHED_STATUS;
	$wealTaskPointsParams['send_type'] = Util_Activity_Context::TASK_TYPE_WEAK_TASK;
	$wealTaskPointsParams['sub_send_type'] = $val;
	$wealTicketsResult = Client_Service_TicketTrade::getCount($wealTaskPointsParams);
	if($wealTicketsResult){
		Client_Service_TaskStatisticReport::updateStatisticReporTickets(Util_Activity_Context::TASK_TYPE_WEAK_TASK, $val, $wealTicketsResult);
	}
	//总人数
	$wealTotalNumResult = Client_Service_TicketTrade::getCount($wealTaskPointsParams);
	if($wealTotalNumResult){
		Client_Service_TaskStatisticReport::updateStatisticReporPeopleNum(Util_Activity_Context::TASK_TYPE_WEAK_TASK, $val, $wealTotalNumResult);
	}
}







 
 
 