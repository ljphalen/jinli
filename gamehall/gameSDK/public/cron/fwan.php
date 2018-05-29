<?php
include 'common.php';

/**
 * 定时采集疯玩网
 * 资讯新闻
 * 修改数据库为普通索引
 */
$type = 1;
$keyword = '';
$fwan = new Api_Fwan_Game();
// 获取疯玩网资讯列表
$fwanList = $fwan->getList($type, $keyword);
if (empty($fwanList)) exit('-1');
$outids = array();
$list = Client_Service_News::getAllNewsOutID($type);
if (!empty($list)) {
	foreach ($list as $value) {
		$outids[] = $value['out_id'];
	}
}

$data = array();
foreach ($fwanList as $value) {
	// 排除已入库的数据
	if (!in_array($value['id'], $outids)) {
		// 获取新闻页面数据
		$news = $fwan->get($value['id']);
		if (!news) exit('-2');
		$data[] = array( 
				'id' => $value['id'],
				'title' => $value['title'],
				'resume' => $value['resume'],
				'thumb_img' => $value['thumb_img'],
				'time' => strtotime($value['time']),
				'ntype' => $type,
				'ctype' => 1,
				'content' => stripslashes($news['content']),
				'from' => $news['from'] 
		);
	}
}
// 入库操作
if (!empty($data)) Client_Service_News::addApiNews($data);

echo CRON_SUCCESS . "\n";
