<?php
include 'common.php';
/**
 *登陆游戏-定向专题中游戏的有奖状态
 * 执行频率 30分钟一次
 *fanch
 */
$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_LOGIN_GAME_ACOUPON;
$lock = $cache->get($lockName);
if($lock) exit("nothing to do.\n");
$cache->set($lockName, 1, 60*60);

function getSubjectGameIds($subjectId){
    $games = Client_Service_SubjectGames::getSubjectAllItemsGames($subjectId);
    $games = Common::resetKey($games, 'game_id');
    $gameIds = array_keys($games);
    return $gameIds;
}

$gameIdArr = array();
$tasks = Client_Service_TaskHd::getsUnionGamesHds();
foreach ($tasks as $value) {
    $ruleContent = json_decode($value['rule_content'], true);
    if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT) {
        $gameIds = getSubjectGameIds($value['subject_id']);
        $gameIdArr = array_merge($gameIdArr, $gameIds);
    }
}
$gameIdArr = array_unique($gameIdArr);
Async_Task::execute('Async_Task_Adapter_GameListData', 'updteGamesAcoupon', $gameIdArr);

echo CRON_SUCCESS;

$cache->set($lockName, 0, 60*60);
exit;
