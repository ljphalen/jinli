<?php
include 'common.php';
die('lock');
//数据融合脚本
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

/**
 * 添加（上线）游戏
 */
function start($appid) {
   	$language = array(
		'1' => "中文",
		'2' => "英文",
		'3' => "其他",
	);

   	//获取数据
   	$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
    $url = $urls[1] . '?appid='.$appid;
   	$curl = new Util_Http_Curl($url);
   	$result = $curl->get();
   	$base_tmp =  $tmp = $category = $simg = $device = $labels = array();
   	$tmp = json_decode($result,true);
   	$result = $tmp['data'];
   	if (!$result) return '-1';
   	//数据写入    	
   	if($result){
   		//游戏基本信息
   		$img = explode('|',$result['icon']);
   		$base_tmp = array(
   				'appid'=> $result['appid'],
   				'apkid'=> $result['apkid'],
   				'name'=> $result['name'],
   				'resume'=> htmlentities($result['resume']),
   				'label'=> $result['label'],
   				'img'=> $img[0],
   				'mid_img'=> $img[1],
   				'big_img'=> $img[2],
   				'language'=> $language[$result['language']] ,
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
			);
   		
   		//游戏截图缩略图
   		$simg = explode('|',$result['imgs']);
   		//游戏分类
   		$category = explode('-',$result['category']);
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
   	if($game)  {
   		//旧的就更新游戏
   		$ret = Dev_Service_Sync::updateGame($base_tmp, $simg, $game['id'], $category, $labels, $versions, $diffpackages, $resolution);
   	} else {
   		//新的游戏直接添加
   		$ret = Dev_Service_Sync::addNewGame($base_tmp, $simg, $category, $labels, $versions, $diffpackages, $resolution);
   	}
   	
  return ($ret) ? '1' : '0';
}


//数据融合处理
set_time_limit(0);
$maxApp = 1420;
for($i =1; $i<=$maxApp; $i++){
   	$ret = start($i);
   	
   	switch ($ret) {
   	case '-1':
   		echo "app:$i--->not found\r\n";
   		break;
   	case '0':
   		echo "app:$i--->fail\r\n";
   		break;
   	case '1':
   		echo "app:$i--->ok\r\n";
   		break;
   	}
   	
   	if(($i%50) ==0) sleep(2);
 }
   
