<?php
$isDebug   = preg_match("/debug/i", $_SERVER['QUERY_STRING']) >= 1;
$isLocal   = preg_match("/localhost|dev|[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$isLocalIp = preg_match("/[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$staticPath   = $isLocal ? $isLocalIp ? "http://".$_SERVER['HTTP_HOST'].":8899": "http://dev.assets.gionee.com" : 'http://assets.3gtest.gionee.com';
$staticResPath = $staticPath.'/apps';
$staticSysPath = $staticPath.'/sys';
?>