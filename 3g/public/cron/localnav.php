<?php
include 'common.php';
//抓取新闻数据
$out = Nav_Service_NewsParse::run();
foreach($out as $sourceId => $ids) {
	echo date('m/d H:i:s').':['.$sourceId.']'.Common::jsonEncode($ids)."\n";
}
//构建二级新闻数据
Nav_Service_NewsData::makeList();
//构建卡片数据
$out = Gionee_Service_LocalNavList::run();
//同步appcache文件 (主要目的离线缓存)
$out .= Gionee_Service_LocalNavList::make_appcache_file();
//卡片数据生成静态文件
$out .= Gionee_Service_LocalNavList::card_data_2_html();
echo $out;
echo CRON_SUCCESS;