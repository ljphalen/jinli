<?php
include 'common.php';
/**
 *检测联通免流量游戏,同步状态到本地联通内容库状态 10分钟主动查一次
 *
 */
$cache = Cache_Factory::getCache();
$lock = $cache->get("_cron_freedl_custatus_lock");
if($lock) exit("nothing to do.\n");
$cache->set("_cron_freedl_custatus_lock", 1, 11*60); //11分钟过期

function onlineGame($value, $type){
	//首先查找contentId上线标识状态online_flag=1的记录，记录id
	$onlineCugame = Freedl_Service_Cugd::getBy(array('content_id'=>$value['content_id'], 'online_flag'=>1));
	//获取游戏内容库最新游戏数据,设置最新的游戏内容状态
	$game = Resource_Service_Games::getGameAllInfo(array('id'=> $value['game_id']), false, false);
	//联通审核通过的上线版本跟在线上的版本相同才能时内容库处于上线状态
	$game_status = ($game['version_code'] == $value['version_code']) ? 1 : 0;

	if($onlineCugame){
		//需要变更的数据
		$data = array(
				'version'=>$value['version'],
				'version_code'=>$value['version_code'],
				'cu_status'=> $type,
				'cu_online_time'=>Common::getTime(),
				'game_status' => $game_status,
				'create_time'=> $value['create_time']
		);
		//更新线上数据版本信息为上线
		Freedl_Service_Cugd::updateCugd($data, array('id'=>$onlineCugame['id']));
		//清理掉进行审核的数据,保持之前免流量游戏的状态
		Freedl_Service_Cugd::deleteCugd(array('id'=>$value['id']));
	} else {
		//更新待审核的记录为通过审核状态
		Freedl_Service_Cugd::updateCugd(array('cu_status'=> $type, 'online_flag'=>1, 'game_status' => $game_status, 'cu_online_time'=>Common::getTime()), array('id'=>$value['id']));
	}
}

$page = 1;
do {
	list($total, $items) = Freedl_Service_Cugd::getList($page, 100, array('cu_status' => 1)); //每次获取10条消息做处理
	if(empty($items)) exit("not found data\r\n");
	foreach ($items as $value){
		$result = Api_Freedl_Cu_Gd::getstatus($value['content_id']);
		if($result['errcode'] == 0){
			$status = $result['resultData']['status'];
			switch ($status){
				case 'checked': //审核通过
					//调用游戏上线接口，完成免流量联通上线，同步变更免流量基本信息
					onlineGame($value, 2);
					break;
				case 'checkfailed':
					//更新审核不同通过状态
					Freedl_Service_Cugd::updateCugd(array('cu_status'=>3), array('id'=>$value['id']));
					break;
				case 'valid':
					//更新审核记录为上线状态
					onlineGame($value, 4);
					break;
				case 'invalid':
					//下线
					//首先查找contentId上线标识状态online_flag=1的记录
					$cugame = Freedl_Service_Cugd::getBy(array('content_id'=>$value['content_id'], 'online_flag'=>1));
					//更新联通资源状态为下线，游戏记录为下线状态
					if ($cugame['id']) Freedl_Service_Cugd::updateCugd(array('cu_status'=>5), array('id'=>$cugame['id']));
					break;
			}
		}
		echo "cugdgame-id:{$value}\r\n";
	}
	$page++;
	sleep(1);
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;

$cache->set("_cron_freedl_custatus_lock", 0, 11*60); //11分钟过期
exit;