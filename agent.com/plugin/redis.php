<?php
/*
默认调用配置文件中 $ini_redis 中的键为  0 的数据库服务器配置
如果需要改变调用的数据库服务器，须在加载本文件前设置 $redis_id 变量
加载本插件后，自动生成  $redis 对象，直接使用该对象对Redis进行操作
在页面最后面或使用完成  $redis 对象后手动释放对象：$redis = null;
*/

require 'check.php';
if (!extension_loaded('redis'))
	trigger_error('401|extension php redis not found');

$redis = new Redis();
if (empty($redis_id)) $redis_id = '0';

try {
	$redis->connect($ini_redis[$redis_id]['host'], $ini_redis[$redis_id]['port'], 5);
} catch (Exception $e) {
	trigger_error('402|Could not connect to redis '. $ini_redis[$redis_id]['host'] .':'. $ini_redis[$redis_id]['port']);
}
?>