<?php
/*
 * 渠道商日月统计查询
* @since 1.0.0 (2013-05-15)
* @version 1.0.0 (2013-05-15)
* @author jun <huanghaijun@mykj.com>
*/
//http://192.168.5.48:93/api.php?ac=daycount&clientid=8168&subclientid=0&reporttype=day&gameid=0&ymdstart=2013-04-01&ymdend=2013-04-02&format=xml&sig=4671702bb8e5bc409ea8033d7a1ec4c4

//取请求参数
$reporttype  = get('reporttype')?get('reporttype'):'day';
$gameid  = intval(get('gameid'));
$clientid  = intval(get('clientid'));
$subclientid  = intval(get('subclientid'));
$dateStart  = get('ymdstart');
$dateEnd  = get('ymdend');

$format  = get('format')?get('format'):'xml';

$pagesize = 31;
$page = 1;


$dateStart = date('Y-m-d',strtotime($dateStart));
$dateEnd = date('Y-m-d',strtotime($dateEnd));


if($reporttype=='day'){
	if(strtotime($dateEnd)-strtotime($dateStart)>86400*$pagesize){
		//exit('日期区间不能超过'.$pagesize.'天');
		output_message(read_status(1007),get('format'));	//1005	参数不符合要求	参数非法
	}
}else{
	if(strtotime($dateStart.' +12 months')<strtotime(substr($dateEnd,0,7))){
		//exit('日期区间不能超过12个月');
		output_message(read_status(1007),get('format'));	//1005	参数不符合要求	参数非法
	}
}


//如未传值取默认值
if(empty($dateStart)){
	$dateStart  = date("Y-m-d",strtotime('-1 day'));
}

if(empty($dateEnd)){
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
}

//调用数据接口
$report = new report();
$records = $report->get_report_sub_count_list($reporttype, $gameid, $clientid, $subclientid, $dateStart, $dateEnd, $pagesize, $page);


$data = array();
if(!empty($records['list'])){
	$list = $records['list'];
	foreach($list as $k=>$v){
		$data[] = array(
				'date'=>$v['today'],
				'registerusers'=>$v['registerusers'],
				'consumeorders'=>$v['consumeorders'],
				'consumeusers'=>$v['consumeusers'],
				'consumemoney'=>$v['consumemoney'],
		);
	}
}

$rtn = read_status(0);
$rtn['data'] = $data;
output_message($rtn,get('format'));
/* if($format=='json'){
	$rtntext = json_encode($data);
}else{
	$rtntext = '<?xml version="1.0" encoding="utf-8" ?><root>';
	foreach($data as $k=>$v){
		$rtntext .= '<data';
		foreach($v as $key=>$val){
			$rtntext .= ' '.$key.'="'.$val.'"';
		}
		$rtntext .= ' />';
	}
	$rtntext .= '</root>';
}

echo $rtntext; */
?>