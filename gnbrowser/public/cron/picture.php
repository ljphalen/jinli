<?php
include 'common.php';
/**
 *获取新闻
 */

$picturetype = Common::getConfig('apiConfig', 'picture');

foreach ($picturetype as $key=>$value) {
	if($value['url']) {
		$content = file_get_contents($value['url']);
		if ($content) {
			$rss = Util_XML2Array::createArray($content);
			$items = $rss['rss']['channel']['item'];
			if(is_array($items)) {
				$data = array();
				foreach ($items as $k=>$v) {
					$data[$k]['id'] = '';
					$data[$k]['type_id'] = $key;
					$data[$k]['title'] = $v['title'];
					$data[$k]['img'] = $v['description']['img']['@attributes']['src'];
					$data[$k]['url'] = $v['link'];
					$data[$k]['status'] = 0;
					$data[$k]['istop'] = 0;
					$data[$k]['pub_time'] = strtotime($v['pubDate']);
					$data[$k]['start_time'] = Common::getTime();
					$data[$k]['sort'] = 0;
					
				}
			//清空原数据
			Gionee_Service_Picture::deleteByType($key);
			//插入数据
			Gionee_Service_Picture::batchAddPicture($data);
			}
		}
	}
}

//更新显示状态

foreach ($picturetype as $keys=>$values) {
	list(,$pictures)  = Gionee_Service_Picture::getListByType($keys);
		
		//设置显示状态
		$filter = Gionee_Service_Config::getValue('gionee_picture_filter');
		$number = Gionee_Service_Config::getValue('gionee_picture_number');
		
		list($total, $pictures) = Gionee_Service_Picture::getListByType($keys);
		$pictures = Common::resetKey($pictures, 'id');
		//默认为按发布时间显示2条
		$picfilter = $filter ? $filter : 2;
		$picnumber = $number ? $number : 2;
		
		//总条数小于设置的显示数
		if($total < $picnumber) $picnumber = $total;
		
		//该分类下已置顶的条数
		list($toptotal, ) = Gionee_Service_Picture::getTopPicture($key);
		
		//需要更新状态的总条数为设置的显示条数减掉已置顶的条数
		$picnumber = $picnumber - $toptotal;
		
		if($picnumber > 0) {
			if ($filter == 1) {
				//随机
				$ids = array_rand($pictures, intval($picnumber));
				if (!is_array($ids)) $ids = array($ids);			
			}else{
				//按发布时间显示
				$ids = array_slice(array_keys($pictures), 0, $picnumber);			
			}
			
			//更新新闻显示状态
			if($ids) Gionee_Service_Picture::updateStatusByIds($ids, 1);
		}
}






