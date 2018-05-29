<?php
include 'common.php';
/**
 *初始化游戏列表缓存数据
 * 执行频率每天一次
 *fanch
 */
$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_GAMELIST_DATA_CRON;
$lock = $cache->get($lockName);
if($lock) exit("nothing to do.\n");
$cache->set($lockName, 1, 60*60);

$contentKey = Util_CacheKey::GAME_LIST_CONTENT_KEY;
$obj = new Cache_ListContent($contentKey);
$perPage = 100;
$begin = $obj->buildContentBegin();
if($begin){
    $work = true;
    $page = 1;
    do {
        //只扫上线的游戏
        list($total, $games) = Resource_Service_Games::getList($page, $perPage, array('status' => 1));
        if (empty($games)) {
            if(1 == $page) {
                $work = false;
                $message = '初始化游戏列表数据失败：没有读取到任何数据.';
            }
            break;
        }
        $games = Common::resetKey($games, 'id');
        $gameIds = array_keys($games);
        $listData = Resource_Service_GameListData::buildListData($gameIds);
        if (!$listData) {
            $work = false;
            $message = '初始化游戏列表数据失败：游戏组装失败,' . json_encode($gameIds);
            break;
        }
        $result = $obj->storeListContent(Resource_Service_GameListData::GAME_ID_KEY, $listData);
        if (!$result) {
            $work = false;
            $message = '初始化游戏列表数据失败：游戏保存失败,' . json_encode($gameIds);
            break;
        }
        $page++;
    } while ($total > (($page - 1) * $perPage));

    if($work){
        $finish = $obj->buildContentFinish();
        if(!$finish){
            $message = '初始化游戏列表数据失败：buildFinish()失败';
        }
    }
} else {
    $work = false;
    $message = '初始化游戏列表数据失败：buildBegin()失败.';
}

if($work == false) {
    Util_Log::debug('saveGameListData', 'gamelistdata.log', $message);
    //发送预警邮件
    $to = "huyk@gionee.com,fanch@gionee.com,zhengzw@gionee.com,yxdtyf@gionee.com";
    Common::sendEmail('游戏服务器计划任务异常',  $message,  $to);
}

echo CRON_SUCCESS;

$cache->set($lockName, 0, 60*60);
exit;
