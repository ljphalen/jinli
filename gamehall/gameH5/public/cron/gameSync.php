<?php
include 'common.php';
/**
 *刷新游戏缓存数据-2015-02-09
 *fanch
 */

  function getData($data, $id) {
	 $tmp = array();
	 $labs = explode('|',$data);
	 foreach($labs as $key=>$value){
		 if($value){
			 $tmp[] = $id.'|'.$value;
		 }
	 }
	 return $tmp;
  }

   function syncOn($appId){
   		$log = array('act' => '1');
		$info['id']= $appId;
		//语言
		$setLanguage = array(
				'1' => "中文",
				'2' => "英文",
				'3' => "其他",
		);
		
		
		//获取数据
		$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
		$url = $urls[1] . '?appid='.$info['id'];
		$curl = new Util_Http_Curl($url);
		$result = $curl->get();
		$base_tmp =  $tmp = $category = $simg = $device = $labels = array();
		$tmp = json_decode($result,true);
		$result = $tmp['data'];
	
		//数据写入
		if($result){
			$log['app_id'] =  $result['appid'];
			//游戏基本信息
			$img = explode('|',$result['icon']);
			$base_tmp = array(
					'appid'=> $result['appid'],
					'apkid'=> $result['apkid'],
					'name'=> $result['name'],
					'resume'=> htmlentities($result['resume']),
					'label'=> htmlentities($result['label']),
					'img'=> $img[0],
					'mid_img'=> $img[1],
					'big_img'=> $img[2],
					'language'=> $setLanguage[$result['language']] ,
					'package'=> $result['package'],
					'packagecrc'=> crc32(trim($result['package'])),
					'price'=> $result['price'],
					'company'=> $result['author_name'],
					'descrip'=> htmlentities($result['descrip']),
					'tgcontent'=> htmlentities($result['tgcontent']),
					'create_time'=> $result['create_time'],
					'online_time' => $result['online_time'],
					'status'=> ($result['status'] == 3 ? 1 : 0),//3为已上线，否则未上线
					'hot'=> $result['hot'],
					'cooperate'=> $result['cooperate'],
					'developer'=> $result['developer'],
					'certificate'=> $result['safeInfos'] ? serialize($result['safeInfos']) : '' ,
					'secret_key'=> trim($result['secret_key']),
					'api_key'=> trim($result['api_key']),
					'agent'=> trim($result['agent']),
					'webp'=> $result['webp'],
			);
	
			//游戏截图缩略图
			$simg = explode('|',$result['imgs']);
			//游戏分类-旧版
			//$category = explode('-',$result['category']);
	
			//游戏主分类-v1.5.6
			$category['mainParent'] = $result['category_p'];
			$category['mainSub'] = $result['category_p_son'];
			//游戏次分类-v1.5.6
			$category['lessParent'] = $result['category_s'];
			$category['lessSub'] = $result['category_s_son'];
	
			//游戏分辨率
			$resolution = $result['resolution'];
	
			//游戏标签属性
			/**
			*network_type    [联网类型]
			*character       [游戏特色]
			*billing_model   [资费方式]
			*detail_category [详细分类]
			*level           [游戏评级]
			*about           [内容题材]
			*style           [画面风格]
			*/
			$labelConfig =  Common::getConfig("apiConfig", 'label');
			$labelConfig = (ENV == 'product') ? $labelConfig['product'] : $labelConfig['test'];
	
			$network_type = getData($result['network_type'], $labelConfig['network_type']);
			$character = getData($result['character'], $labelConfig['character']);
			$detail_category = getData($result['detail_category'], $labelConfig['detail_category']);
			$level = getData($result['level'], $labelConfig['level']);
			$about = getData($result['about'], $labelConfig['about']);
			$style = getData($result['style'], $labelConfig['style']);
	
	
			//合并处理
			$labels = array_merge($network_type, $character,$detail_category,$level,$about,$style);
			//游戏版本信息
			$versions = $result['versions'];
			//游戏的查分信息
			$diffpackages = $result['diffpackages'];
		}
	
		//检查游戏是否为新的
		$game = Resource_Service_Games::getBy(array('package'=>trim($result['package'])));
     	//游戏id或无
		$log['game_id'] = $game ? $game['id'] : 0;
		if($game)  {
		//旧的就更新游戏
		//获取该游戏当前的下载量
		$downloads = Client_Service_WeekRank::getRankGameId($game['id']);
		$base_tmp['downloads'] = $downloads['DL_TIMES'];
			$ret = Dev_Service_Sync::updateGame($base_tmp, $simg, $game['id'], $category, $labels, $versions, $diffpackages, $resolution);
			//更新讯搜索引
			$data = array(
			'gameId'=>$game['id'],
			'gameName'=>$game['name'],
			'resume'=>$game['resume'],
			'label'=>$game['label']
		);
		Api_XunSearch_Search::updateIndex($data);
		} else {
		//新的游戏直接添加
		$base_tmp['downloads'] = 0;
				$ret = Dev_Service_Sync::addNewGame($base_tmp, $simg, $category, $labels, $versions, $diffpackages, $resolution);
				//增加迅搜索引
				$data = array(
				'gameId'=>$ret,
				'gameName'=>$base_tmp['name'],
				'resume'=>$base_tmp['resume'],
				'label'=>$base_tmp['label']
				);
				Api_XunSearch_Search::addIndex($data);
				}
				$log['message'] ="app:{$result['appid']}上线同步";
				if ($ret) {
				$log['status'] = 1;
				//写入同步记录
				Resource_Service_Sync::add($log);
				echo 'ok';
				}
   }


$page = 1;
do {
    //只扫上线的游戏
	list($total, $games) = Resource_Service_Games::getList($page , 100, array('status'=>1));
	if(empty($games)) exit("not found data\r\n");
	foreach ($games as $value){
			//同步应用
			if($value['appid']){
				syncOn($value['appid']);
			}
	  echo "-game-sync-ok:{$value['id']}\r\n";
	}
	$page++;
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;
