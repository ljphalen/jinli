<?php
/*
 默认调用配置文件中 $ini_memcache 中的键为  0 的数据库服务器配置
如果需要改变调用的数据库服务器，须在加载本文件前设置 $memcache_id
加载本插件后，自动生成  $memcache 对象，直接使用该对象对Memcache进行操作
在页面最后面或使用完成  $memcache 对象后手动释放对象：$memcache = null;
*/

require 'check.php';
if (!extension_loaded('memcache'))
	trigger_error('403|extension php memcache not found');

$memcache = new memcache();
if (empty($memcache_id)) $memcache_id = '0';

try {
	$memcache->connect($ini_memcache[$memcache_id]['host'], $ini_memcache[$memcache_id]['port'], 5);
} catch (Exception $e) {
	trigger_error('404|Could not connect to memcache '. $ini_memcache[$memcache_id]['host'] .':'. $ini_memcache[$memcache_id]['port']);
}
?>