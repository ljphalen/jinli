<?php
include 'common.php';
/**
 *获取新闻
 */

$newstype = Common::getConfig('apiConfig', 'news');

foreach ($newstype as $key=>$value) {
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
				$data[$k]['url'] = $v['link'];
				$data[$k]['content'] = $v['description'];
				$data[$k]['pub_time'] = strtotime($v['pubDate']);
				$data[$k]['create_time'] = Common::getTime();
				$data[$k]['img'] = $v['origimg'];
			}
		//清空原数据
		Gionee_Service_NewsTemp::deleteByType($key);
		//插入数据
		Gionee_Service_NewsTemp::batchAddNewsTemp($data);
		}
	}
}

//更新新闻

foreach ($newstype as $keys=>$values) {
	list(,$news)  = Gionee_Service_NewsTemp::getListByType($keys);
	if($news) {
		$newsdata = array();
		foreach ($news as $ke=>$val) {
			$newsdata[$ke]['id'] = '';
			$newsdata[$ke]['sort'] = 0;			
			$newsdata[$ke]['type_id'] = $keys;
			$newsdata[$ke]['title'] = $val['title'];
			$newsdata[$ke]['url'] = $val['url'];
			$newsdata[$ke]['img'] = $val['img'];
			$newsdata[$ke]['ontime'] = $val['pub_time'];
			$newsdata[$ke]['content'] = $val['content'];
			$newsdata[$ke]['status'] = 0;
			$newsdata[$ke]['istop'] = 0;
			$newsdata[$ke]['start_time'] = Common::getTime();
		}
		//清空原数据
		Gionee_Service_News::deleteByType($keys);
		
		//插入数据
		Gionee_Service_News::batchAddNews($newsdata);
		
		//设置显示状态
		$filter = Gionee_Service_Config::getValue('gionee_news_filter');
		$number = Gionee_Service_Config::getValue('gionee_news_number');
		
		list($total, $news) = Gionee_Service_News::getListByType($keys);
		$news = Common::resetKey($news, 'id');
		//默认为按发布时间显示4条
		$newsfilter = $filter ? $filter : 2;
		$newsnumber = $number ? $number : 4;
		
		//总条数小于设置的显示数
		if($total < $newsnumber) $newsnumber = $total;
		
		//已置顶的条数
		list($toptotal, )  = Gionee_Service_News::getTopNews($key);
		
		//需要更新状态的总条数为设置的显示条数减掉已置顶的条数
		$newsnumber = $newsnumber - $toptotal;
		
		if ($newsnumber > 0) {
			if ($filter == 1) {
				//随机
				$ids = array_rand($news, intval($newsnumber));
				if (!is_array($ids)) $ids = array($ids);			
			}else{
				//按发布时间显示
				$ids = array_slice(array_keys($news), 0, $newsnumber);			
			}
			
			//更新新闻显示状态
			Gionee_Service_News::updateStatusByIds($ids, 1);
		}
	}
}






