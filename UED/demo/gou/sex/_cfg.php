<?php
$isDebug   = preg_match('/debug/i', $_SERVER['QUERY_STRING']);
$ucweb     = preg_match('/UC/i', $_SERVER['HTTP_USER_AGENT']);

$source    = $isDebug? '.source' : '';
$ucClass   = $ucweb? 'uc-hack' : '';
$webroot   = '.';
$timestamp = '?t=20120913';

$sysRef = $appRef = 'assets';
$appPic = 'pic';
?>