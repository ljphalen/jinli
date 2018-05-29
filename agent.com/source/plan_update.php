<?php
/*
 * 订单表汇总操作通过任务计划来执行
* @since 1.0.0 (2013-05-13)
* @version 1.0.0 (2013-05-13)
* @author ljp <ljphalen@163.com>
*/
@set_time_limit(0);
@ini_set('memory_limit','256M');


//取生成日期，默认昨天
$createdate = get('createdate');

$rate =100;

//获取更新日期
if(empty($createdate)){
	$createdate = date('Y-m-d',strtotime('-1 day'));
}else{
	$createdate = date('Y-m-d',strtotime($createdate));
}

//连接数据库
$db = db::factory(get_db_config());
$params = array($createdate);
//取得订单数
$result = $db->simpleCall('sp_web_getOrderList',$params);
$gameid = 1 ;

//var_dump($result,$params);
//统计订单数
if(is_array($result[0])){	
	//插入数据
	foreach($result[0] as $key=>$val){
		$params = array($createdate,$val['payway'],$val['clientid'],$val['clientidsub'],
				        $val['consumeorders'],$val['consumeusers'],$val['consumemoney']/$rate,$gameid);
		$result1 = $db->simpleCall('sp_web_insertreportorder',$params);
		$result2 = $db->simpleCall('sp_web_insertreportdaytoorder',$params);
		
	}		
}
//var_dump($result1,$result2);
//统计注册人数
$params = array($createdate);
$rs_regnum = $db->simpleCall('sp_web_getRegNum',$params);
//var_dump($rs_regnum,$params);
if(is_array($rs_regnum[0])){
	foreach($rs_regnum[0] as $key=>$val){
		$params2=array($createdate,$val['clientid'],$val['clientidsub'],$val['regnum'],0,0,$gameid,0,1);
		$result3 = $db->simpleCall('sp_web_insertreportuser',$params2);
		$result4 = $db->simpleCall('sp_web_insertreportdaytoreg',$params2);	
	}
}
//var_dump($result3,$result4);
//统计登录人数
 $params = array($createdate);
$rs_loginnum = $db->simpleCall('sp_web_getLoginNum',$params);

//var_dump($rs_regnum,$params);
if(is_array($rs_loginnum[0])){
	foreach($rs_loginnum[0] as $key=>$val){
		$params3=array($createdate,$val['clientid'],$val['clientidsub'],0,0,0,$gameid,$val['loginnum'],2);
		$result5 = $db->simpleCall('sp_web_insertreportuser',$params3);
		$result6 = $db->simpleCall('sp_web_insertreportdaytoreg',$params3);
	}
} 
//30日首次登陆人数
$params=array($createdate,$gameid,1);
$db->simpleCall('sp_web_getFirstSata',$params);
//30日首次充值人数
$params=array($createdate,$gameid,2);
$db->simpleCall('sp_web_getFirstSata',$params);
//30日首次充值次数
$params=array($createdate,$gameid,3);
$db->simpleCall('sp_web_getFirstSata',$params);
//30日首充金额
$params=array($createdate,$gameid,4);
$db->simpleCall('sp_web_getFirstSata',$params);

$db=null;
echo '更新成功:'.$createdate;