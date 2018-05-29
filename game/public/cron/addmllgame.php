<?php
include 'common.php';
/**
 *添加游戏库中已上线的游戏到联通免流量内容库
 *
 */
$page = 1;
do {
	//只扫上线的游戏
	list($total, $games) = Resource_Service_Games::getList($page , 100, array('status'=> '1'));
	if(!$games) break;
	foreach($games as $key=>$value){
		//添加游戏到联通免流量
		$gid = $value['id'];
		$gameInfo = Resource_Service_Games::getGameAllInfo(array('id'=>$gid), false, false);
		$request = Freedl_Service_Cugd::fillData($gameInfo);
		$ret = Api_Freedl_Cu_Gd::addcontent($request);
		if($ret['errcode'] == '0') {
			//构造联通免流量资源库数据
			$data = array(
					'game_id'=> $gid,
					'app_id' => $gameInfo['appid'],
					'version' => $gameInfo['version'],
					'version_code' => $gameInfo['version_code'],
					'content_id' => $ret['resultData']['contentId'],
					'cu_status' => 1,
					'game_status' => 1,
					'create_time' => Common::getTime(),
			);
			//增加记录到免流量资源表
			Freedl_Service_Cugd::addCugd($data);
		}
		echo "CU_ADDGAME-game:{$gid}-ok\r\n";
	}
	$page++;
	sleep(1);
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;