<?php
include 'common.php';
/**
 *通过账号登陆日志筛选联运游戏登陆用户赠送A券数据
 */
$cache = Cache_Factory::getCache();
$lock = $cache->get(Util_CacheKey::LOCK_UNLOGIN_CRON);
if($lock) exit("nothing to do.\n");
$cache->set(Util_CacheKey::LOCK_UNLOGIN_CRON, 1, 6*60); //6分钟过期

$time = Common::getTime();
$lastTime = $cache->get(Util_CacheKey::LOGIN_LAST_TIME);
$startTime = $lastTime ? $lastTime : strtotime("-5 minute", $time);
$endTime = $time;
$params['LogTime'][0] = array('>=', date('Y-m-d H:i:s', $startTime ));
$params['LogTime'][1] = array('<=', date('Y-m-d H:i:s', $endTime));
//测试环境
//$params['AppID'] = array('<>','4D97BA59A2404E28BE445D833551753A');
//生产环境
//$params['AppID'] = array('<>','CEECAAC948CA4322B19954266B4858F9');

Common::log(array($params), date('Y-m-d').'_corn_ulogin.log');

$params['Type'] = array('IN', array(1,3,5));
$page = 1;
do {
	list($total,$logs) = Client_Service_ACCLog::getList(1, 1000, $params); //每次获取前5分钟登陆日志做处理
	if(empty($logs)){
        break;
    }
	foreach($logs as $key=>$value){
		$UserId = strtoupper(bin2hex($value['UserId']));
		//缓存的用户的信息
		$cache = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($UserId);

		$client_version = $cache->hGet($cacheKey,'clientVersion');
		if(strnatcmp($client_version, '1.5.4') < 0 ){
			continue;
		}
		//福利任务联运登录赠送
		$login_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>$UserId,'type'=>1,'api_key'=>$value['AppId'])));
		$login_class ->sendTictket();
		//活动联运登录赠送
		$login_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>$UserId,'type'=>3, 'logTime'=>strtotime($value['LogTime']), 'api_key'=>$value['AppId'])));
		$login_class ->sendTictket();
		echo "{$value['LogTime']} -- {$value['MobileNo']} -- App_KEY ={$value['AppId']} --- UUID ={$UserId}----ok\r\n";
		Common::log('loginTime='.$value['LogTime'].',MobileNo='.$value['MobileNo'].',App_KEY='.$value['AppId'].',UUID='.$UserId.',num='.$total, date('Y-m-d').'_corn_ulogin.log');
	}
	$page++;
	sleep(1);
} while ($total>(($page -1) * 1000));
echo CRON_SUCCESS;

$cache->set(Util_CacheKey::LOGIN_LAST_TIME, $time);

$cache->set(Util_CacheKey::LOCK_UNLOGIN_CRON, 0, 6*60); //6分钟过期
exit;
