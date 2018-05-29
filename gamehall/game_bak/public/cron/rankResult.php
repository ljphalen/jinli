<?php
include 'common.php';
/**
 * 从BI拉取游戏大厅排行榜 -- 下载最多(旧)
 */
$curr_time = strtotime(date('Y-m-d H:i:s',time()));
$end_time = strtotime(date('Y-m-d',time()));      
$start_time = date('Ymd', ($end_time - 30 * 24 *3600));

//获取bi新加入的数据
$params['DAY_ID'][0] = array('>=', $start_time);
$params['DAY_ID'][1] = array('<=', $end_time);
$biResultData = Client_Service_Rank::getsBy($params);


$page = 1;
$games = $gameDownloads = $firstResultData = $gamesIds = $insertRank = $dayId = $crtTime = array();

if($biResultData){
	foreach($biResultData as $key=>$value){
       $games[$value['GAME_ID']][] = $value['DL_TIMES']; 
       $gamesIds[] = $value['GAME_ID'];
       $firstResultData[$value['GAME_ID']]  = $value;
       $dayId[$value['GAME_ID']] = $value['DAY_ID'];
       $crtTime[$value['GAME_ID']] = $value['CRT_TIME'];
	}
	
	foreach($games as $key=>$value){
		$downloads = 0;
		foreach($value as $k=>$v){
			$downloads += $v;
		}
		$gameDownloads[$key] = $downloads;
	}
		
	//如果是第一次就直接添加 
	$rankGames = Client_Service_RankResult::getLastDayId();
	if(!$rankGames){
		foreach($firstResultData as $k=>$v){
			$insertRank = array(
					'DAY_ID' => $v['DAY_ID'],
					'GAME_ID' => $v['GAME_ID'],
					'DL_TIMES' => $gameDownloads[$v['GAME_ID']] ? $gameDownloads[$v['GAME_ID']] : 0,
					'CRT_TIME' => $v['CRT_TIME'],
			);
			Client_Service_RankResult::addRank($insertRank);
		}
		exit;
	}
	
	do {
		//获取本地计算表的数据（每次取100条数据）
		list($total, $rankGames) = Client_Service_RankResult::getList($page , 100);
		if($rankGames){
			foreach($rankGames as $k1=>$v1){
				if(in_array($v1['GAME_ID'], $gamesIds)) {
					Client_Service_RankResult::updateBy(array('DAY_ID'=>$dayId[$v1['GAME_ID']],'DL_TIMES'=>$gameDownloads[$v1['GAME_ID']],'CRT_TIME'=>$crtTime[$v1['GAME_ID']]),array('GAME_ID'=>$v1['GAME_ID']));
				} else {
					$insertRank = array(
							'DAY_ID' => $v1['DAY_ID'],
							'GAME_ID' => $v1['GAME_ID'],
							'DL_TIMES' => $v1['DL_TIMES'],
							'CRT_TIME' => $v1['CRT_TIME'],
					);
					Client_Service_RankResult::addRank($insertRank);
				}
			}
			
			$page++;
			sleep(1);
		}
	} while ($total>(($page -1) * 100));

}
echo CRON_SUCCESS;