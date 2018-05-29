<?php
include 'common.php';
/**
 *update game_resource_games version_code
 */

$webroot = Common::getWebRoot();
list($total,$games) = Resource_Service_Games::getAllResourceGames();
$dataPath = Common::getConfig('siteConfig', 'dataPath');
$file = $dataPath.'/game-score2.csv';
//echo $path;

$spl_obj = new SplFileObject($file, 'rb');
$spl_obj->seek(filesize($file));
$count = $spl_obj->key();//总行数
$perPage = 100;
$maxPage = ceil($count / $perPage);
for($page = 1; $page <= $maxPage; $page++){
	_getFilePartData($spl_obj, $page, $perPage);//每次读取100行
}

function _getFilePartData($spl_obj, $page = 1, $limit=100) {
		$start = ($page - 1) * $limit;
		$spl_obj->seek($start);// 转到第N行, seek方法参数从0开始计数
		for($i = 0; $i < $limit; $i++) {
			$score_info = $scorelog = $contents = array();
			$content= $spl_obj->current();//获取当前行
			$contents = explode(',', $content);
			if (empty($contents[0])) continue; //数据非法跳过
			$row = Resource_Service_Games::getBy(array('appid' => $contents[0]));
			if(!$row) continue; //添加过的数据跳过。
			
			//添加该评分
			$score_info = array(
					'id'=>'',
					'game_id'=>$row['id'],
					'score'=>Resource_Service_Score::avgScore($contents[2], 1),
		     		'total'=>$contents[2],
					'number'=>1,
					'update_time'=>Common::getTime(),
			);
			echo $row['id'] . " done .\n";
			$ret = Resource_Service_Score::add($score_info);
			
			//评分日志
			/*
	        $scorelog = array(
				'id'=>'',
				'game_id'=>$row['id'],
				'score'=>$contents[2],
				'user'=>'admin',
				'imei'=>'',
				'nickname'=>'admin',
			 	'model'=>'',
	  		    'stype'=>1,
				'version'=>'',
				'android'=>'',
				'sp'=>'',
				'create_time'=>Common::getTime(),
		    );
	        $ret = Resource_Service_Score::addLog($scorelog);*/
	        
			$spl_obj->next();// 下一行
		}
	}


echo CRON_SUCCESS;