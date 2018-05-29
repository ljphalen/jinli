<?php
include 'common.php';
/**
 *导出游戏内容到文本
 */
header("Content-type: text/html; charset=utf-8");
$attachPath =  Common::getAttachPath();
//获取所有游戏
list($gtotal, $ggames) = Resource_Service_Games::getAllResourceGames();
//获取所有分类-通过
list(, $categorys) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>1,'status'=>1));
$categorys = Common::resetKey($categorys, 'id');
//游戏所有标签分类-通过
list(, $lab_categorys) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>8,'status'=>1));
$lab_categorys = Common::resetKey($lab_categorys, 'id');
//游戏所有标签分类 值-通过
list(, $labels) = Resource_Service_Label::getAllSortLabel();
$lab_value = array();
foreach($labels as $key=>$value){
	$lab_value[$value['btype']][] = $value;
}
//所有系统版本属性值
list(, $sys_version) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>5,'status'=>1));
$sys_version = Common::resetKey($sys_version, 'id');
//所有分辨率属性值
list(, $resolution) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>4,'status'=>1));
$resolution = Common::resetKey($resolution, 'id');

foreach($ggames as $key => $value){
	$page = array();
	$gid = $value['id'];
	//游戏内容-通过
	$info = Resource_Service_Games::getResourceGames($gid);
	array_push($page, array("游戏名称:" ,  $info['name']));
	//游戏包名
	array_push($page, array("游戏包名:" ,  $info['package']));
	//游戏分类-通过
	$category_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>$gid));
	$category_title = gameData($categorys,$category_ids,'category_id');
	$category_title = implode(',', $category_title);
	array_push($page, array("游戏分类:" ,  $category_title ));

	//该游戏所有标签ID
	$game_labels = Resource_Service_Games::getIdxLabelByGameId($gid);
	$game_labels = Common::resetKey($game_labels, 'label_id');
	$game_labels = array_keys($game_labels);
	//完成游戏标签分类值组装
	$labelsArr = array();
	foreach($lab_categorys as $key=>$value){
		$str = '';
		$valueArr = array();
		foreach($lab_value[$value['id']] as $k=>$v){
			if(in_array($v['id'], $game_labels))	$valueArr[] = $v['title'];
		}
		$str .= implode(',', $valueArr) ;
		array_push($page, array($value['title'] , $str));
	}

	//计费方式
	list(, $price) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>3,'status'=>1));
	$price = Common::resetKey($price, 'id');
	$price_title  = $price[$info['price']]['title'];
	array_push($page, array("计费方式:" ,  $price_title ));
	//来源
	$source = $info['company'];
	array_push($page, array("来源:" ,  $source ));
	//语言
	$language = $info['language'];
	array_push($page, array("语言:" ,  $language ));
	//简述
	$resume = $info['resume'];
	array_push($page, array("简述:" ,  $resume ));
	//热词
	$hotkey = $info['label'];
	array_push($page,  array("热词:" ,  $hotkey ));
	//应用介绍
	$descrip = $info['descrip'];
	array_push($page, array("介绍:" ,  $descrip ));
	//图标
	$icon = $info['img'];
	array_push($page,  array("本地图标:" ,  $icon ));
	array_push($page, array("线上图标:" ,  $attachPath . $icon ));
		
	//应用截图
	array_push($page,  array("游戏截图列表:"));
	array_push($page, array("序号","本地","线上"));
	list(, $gimgs) = Resource_Service_Img::getList(0, 20, array('game_id'=>$gid));
	foreach($gimgs as $key => $value){
		$imgs = array($key, $value['img'],$attachPath . $value['img']);
		array_push($page,  $imgs);
	}
	array_push($page, array("游戏版本列表:"));
	array_push($page, array("版本","Version Code","大小","本地","线上","支持最低系统版本","最小分辨率","最大分辨率"));
	//应用版本
	$versions = Resource_Service_Games::getIdxVersionByGameId($gid);
	foreach ($versions as $value){
		$verData = array($value['version'] ,
				$value['version_code'] ,
				$value['size'] . "M" ,
				str_replace('http://gamedl.gionee.com', '', $value['link']) ,
				$value['link'] ,
				$sys_version[$value['min_sys_version']]['title'] ,
				$resolution[$value['min_resolution']]['title'] ,
				$resolution[$value['max_resolution']]['title']) ;
		array_push($page, $verData);
	}
	$filename = $info['id'] . '-' . $info['name'];
	writeFile($filename, $page);
}
echo "游戏资料导出完毕。\r\n";
//============================================================//
function gameData(array $source, array $data ,$type) {
	$tmp = array();
	foreach($data as $key=>$value){
		$tmp[] = $source[$value[$type]]['title'];
	}
	return $tmp;
}

 function writeFile($filename, $page){
	$saveFile = Common::getConfig('siteConfig', 'logPath') . '/doc/' . $filename. '.xls';
	$xls = new Util_Excel('UTF-8', false, $filename);
	$xls->addArray($page);
	$xlsData = $xls->saveXML();
	file_put_contents($saveFile, $xlsData);
	echo '成功写入'.$filename ."\r\n";
	sleep(10);
}