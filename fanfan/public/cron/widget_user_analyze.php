<?php
include 'common.php';
//每日分析一次


//每日活跃用户 last_visit_time
//每日订阅栏目百分比 column_id  array(栏目ID=>数量)

//每日网络百分比 net  array(net=>数量)
//每日订阅分类百分比 url_id array(url_id=>数量)
//每日新增用户 created_at
//每日版本分布 app_ver array(app_ver=>数量)
$st     = microtime();
$nowTime = time() - 3600;
$nowDay  = date('Ymd', $nowTime);

//总用户数量
$row        = array('date' => $nowDay, 'type' => '2num', 'key' => '2total_num');
$info       = W3_Service_StatsData::getBy($row);
$row['num'] = Widget_Service_User::getTotal();
if (empty($info['id'])) {
	W3_Service_StatsData::add($row);
} else {
	W3_Service_StatsData::set($row, $info['id']);
}
echo date('Y-m-d H:i:s') . ':' . json_encode($row) . "\n";

//每日活跃用户
$row        = array('date' => $nowDay, 'type' => '2num', 'key' => '2visit_num');
$info       = W3_Service_StatsData::getBy($row);
$visitDate  = array('>=', strtotime($nowDay));
$row['num'] = Widget_Service_User::getTotal(array('last_visit_at' => $visitDate));
if (empty($info['id'])) {
	W3_Service_StatsData::add($row);
} else {
	W3_Service_StatsData::set($row, $info['id']);
}
echo date('Y-m-d H:i:s') . ':' . json_encode($row) . "\n";


$fields = array('model', 'app_ver');
foreach ($fields as $v) {
	$list = Widget_Service_User::getGroupByField($v);
	foreach ($list as $val) {
		$type        = '2' . $v;
		$key         = !empty($val['val']) ? $val['val'] : 'unknown';
		$row         = array('date' => $nowDay, 'type' => $type, 'key' => $key);
		$info        = W3_Service_StatsData::getBy($row);
		$row['type'] = $type;
		$row['num']  = $val['num'];
		if (empty($info['id'])) {
			W3_Service_StatsData::add($row);
		} else {
			W3_Service_StatsData::set($row, $info['id']);
		}
		echo date('Y-m-d H:i:s') . ':' . json_encode($row) . "\n";
	}
}


$et = microtime();
echo date('Y-m-d H:i:s') . ':ids:' . sprintf('%.3f', $et - $st) . "\n";


echo CRON_SUCCESS;
