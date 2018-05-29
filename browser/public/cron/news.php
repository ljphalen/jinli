<?php
include 'common.php';
/**
 *获取新闻
 */

$newstype = Common::getConfig('apiConfig', 'news');
$rand = rand(2, 5);
foreach ($newstype as $key=>$value) {
	$content = file_get_contents($value['url']);
	if ($content) {
		$rss = Util_XML2Array::createArray($content);
		$items = $rss['rss']['channel']['item'];
		if(is_array($items)) {
			$data = array();
			foreach ($items as $k=>$v) {
				$data[$k]['id'] = '';
				$data[$k]['sort'] = 0;
				$data[$k]['type_id'] = $key;
				$data[$k]['title'] = $v['title'];
				$data[$k]['url'] = $v['link'];
				$data[$k]['img'] = '';				
				$data[$k]['ontime'] = strtotime($v['pubDate']);
				$data[$k]['content'] = $v['description'];
				$data[$k]['status'] = 0;
				$data[$k]['istop'] = 0;
				$data[$k]['start_time'] = Common::getTime();
			}
			//清空原数据
			Browser_Service_News::deleteByType($key);
			
			//插入数据
			Browser_Service_News::batchAddNews($data);
			
			if($key == 1) {
				//头条设置一条新闻置顶
				list($total, $topnews) = Browser_Service_News::getListByType($key);
				$topnews = Common::resetKey($topnews, 'id');
				$ids = array_slice(array_keys($topnews), 0, 1);
				Browser_Service_News::updateTopById($ids[0], 1);
			}else{
				
				if($key == $rand) {
					//更新新闻显示状态
					list($total, $news) = Browser_Service_News::getListByType($key);
					$news = Common::resetKey($news, 'id');
					$ids = array_slice(array_keys($news), 0, 2);
					Browser_Service_News::updateStatusByIds($ids, 1);
					
					//将第一条置顶
					Browser_Service_News::updateTopById($ids[0], 1);
				}else{
					//更新新闻显示状态
					list($total, $news) = Browser_Service_News::getListByType($key);
					$news = Common::resetKey($news, 'id');
					$ids = array_slice(array_keys($news), 0, 1);
					Browser_Service_News::updateStatusByIds($ids, 1);
				}
			}
		}
	}
}






