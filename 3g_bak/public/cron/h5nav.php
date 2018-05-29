<?php
include 'common.php';
$out = '';
$rc  = Common::getCache();

//同步机型数据到缓存
Gionee_Service_ModelContent::allModelList(true);

//同步导航首页数据到缓存
$tmpKey    = 'TMP_CACHE:INDEX';
$indexData = Gionee_Service_Ng::getIndexData(true);
$out .= " index_data to cache \n";
$datamd5 = md5(json_encode($indexData));
$verify  = $rc->get($tmpKey);
if ($verify != $datamd5) {
	$out .= " index_data change appc \n";
	$rc->set($tmpKey, $datamd5);
	Gionee_Service_Config::setValue('APPC_Front_Nav_index', Common::getTime());
}

Gionee_Service_Ng::upNavFrontIndexAPPC($indexData['img']);
Gionee_Service_Ng::syncAppcache('Front_Nav_index');


//广告缓存检测
$newAds         = Gionee_Service_Ng::getAds();
$cacheVer    = crc32(json_encode($newAds));
$vKey        = Gionee_Service_Ng::KEY_NG_AD; //版本号相关的缓存内容
$oldCacheVer = $rc->get($vKey);
if ($oldCacheVer != $cacheVer) {
	$rc->set($vKey, $cacheVer, Common::T_ONE_DAY);
	$out .= "ad cache change {$cacheVer} \n";
}

//同步网址大全数据到缓存
Gionee_Service_SiteContent::getSitesData(true);
$out .= " sitemap cache data \n";

echo $out;

//股票指数 start
echo Gionee_Service_LocalNavList::grap_stock_share_index()."\n";
//股票指数 end

Gionee_Service_CronLog::add(array('type' => 'gen_data', 'msg' => $out));

echo CRON_SUCCESS;