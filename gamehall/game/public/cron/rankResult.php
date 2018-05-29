<?php
include 'common.php';
/**
 * 从BI拉取游戏大厅排行榜 -- 下载最多(旧)
 */

function computeGameDownloads($biResultData){
    $gameDownloads = $games = array();
    foreach($biResultData as $key=>$value){
        $games[$value['GAME_ID']][] = $value;
    }

    foreach($games as $key=>$value){
        $downloads = $dayId = $crtTime = 0;
        foreach($value as $k=>$v){
            $downloads += $v['DL_TIMES'];
            $dayId = $v['DAY_ID'];
            $crtTime = $v['CRT_TIME'];
        }
        $gameDownloads[$key]['DL_TIMES'] = $downloads;
        $gameDownloads[$key]['GAME_ID'] = $key;
        $gameDownloads[$key]['DAY_ID'] = $dayId;
        $gameDownloads[$key]['CRT_TIME'] = $crtTime;
    }

    return $gameDownloads;
}

function updateGameRankDownloads($gameDownloads){
    if(!$gameDownloads){
        return;
    }

    foreach($gameDownloads as $k=>$v){
        $ret = Client_Service_RankResult::getBy(array('GAME_ID'=>$v['GAME_ID']));
        if($ret) {
            Client_Service_RankResult::updateBy(array('DAY_ID'=>$v['DAY_ID'],'DL_TIMES'=>$v['DL_TIMES'],'CRT_TIME'=>$v['CRT_TIME']),array('GAME_ID'=>$v['GAME_ID']));
        } else {
            $insertRankData = array(
                    'DAY_ID' => $v['DAY_ID'],
                    'GAME_ID' => $v['GAME_ID'],
                    'DL_TIMES' => $v['DL_TIMES'],
                    'CRT_TIME' => $v['CRT_TIME'],
            );
            Client_Service_RankResult::addRank($insertRankData);
        }
    }
}

function getGameDownloads($biResultData){
    if(!$biResultData){
        return;
    }

    $gameDownloads = computeGameDownloads($biResultData);
    updateGameRankDownloads($gameDownloads);
}


function sendMail(){
    $to =array(
            'huyk@gionee.com',
            'wanghj@gionee.com',
            'jiangjc@gionee.com',
            'xiehx@gionee.com',
            'liuyp@gionee.com',
            'fanch@gionee.com',
            'lichanghua@gionee.com',
            'luojiapeng@gionee.com',
            'panfei@gionee.com',
            'huangab@gionee.com',
            'chenzhan@gionee.com',
            'guohb@gionee.com',
    );
    $title = "【下载最多无数据】";
    $body = date("Y-m-d H:i:s",Common::getTime())."当前从BI获取数据为空，请BI确认！"."<br><br><br>";
    $body.="李畅华"."<br>";
    $body.="深圳市金立通信设备有限公司"."<br>";
    $body.="移动互联网运营部"."<br>";
    $body.="地址:深圳市福田区深南大道7008号阳光高尔夫大厦15楼 "."<br>";
    $body.="MP：13691655852"."<br>";
    foreach($to as $key=>$value){
        Common::sendEmail($title, $body , $value, $author, $type = 'HTML');
    }
}

function getBiDataCondition(){
    $currentTime  = time();
    $end_time =  date('Ymd',$currentTime);
    $start_time = date('Ymd', ($currentTime - 30 * 24 * 3600));
    
    $params = array();
    $params['DAY_ID'][] = array('>=', $start_time);
    $params['DAY_ID'][] = array('<=', $end_time);
    return $params;
}

function main(){
    $params = getBiDataCondition();
    
    $page = 1;
    do {
        //获取本地计算表的数据（每次取100条数据）
        list($total,$getBiResultData) = Client_Service_BIRank::getList($page , 100, $params);
        getGameDownloads($getBiResultData);
        $page++;
    } while ($total>(($page -1) * 100));
    
    $dayId = Client_Service_RankResult::getLastDayId();
    if(!$dayId){
        if(Util_Environment::isOnline()){
            sendMail();
        }
        return;
    } 
    
    //清除1天前的数据
    $beforDay = strtotime($dayId) - 1 * 24 * 3600;
    $beforDay = date('Ymd', $beforDay);
    if($beforDay){
        $search = array();
        $search['DAY_ID'] = array('<=', $beforDay);
        Client_Service_RankResult::deleteBy($search);
    }
}

main();
echo CRON_SUCCESS;