<?php
include 'common.php';
/**
 * game/game_news
 * 检测新闻数据库图片本地化处理
 */
$fwan = new Api_Fwan_Game();
$attachRoot = Common::getAttachPath() . "/news/" . date('Ymd') . "/";

$savePath = Common::getConfig("siteConfig", "attachPath");
$savePath = $savePath . "news/" . date('Ymd');
//检索条件
$search = array( 
		'ntype' => 1,
		'ctype' => 1,
		'collect' => 0 
);
//每次取一条记录操作。
list (, $news) = Client_Service_News::getList(1, 1, $search);

if (empty($news)) exit;

//数据转换
foreach ($news as $value) {
	echo $value['id'] . " ";
	try {
		//缩略图处理
		if (!empty($value['thumb_img'])) {
			//缩略图可以为空
			$attachThumb =  "/news/" . date('Ymd') . "/";
			$saveThumb = $fwan->downloadThumb($value['thumb_img'], $savePath, $attachThumb);
		} else {
			$saveThumb = $value['thumb_img'];
		}
		
		//内容处理
		$content = stripslashes($value['content']);
		$sourceImgs = $fwan->getImgs($content);
		if (!empty($sourceImgs)) {
			$replaceImgs = $fwan->downloadImgs($sourceImgs, $savePath, $attachRoot);
			//图片替换
			$saveContent = $fwan->replaceImgs($sourceImgs, $replaceImgs, $content);
		} else {
			$saveContent = $content;
		}
		
		//更新数据
		$ret = Client_Service_News::updateNews(array( 
				'content' => $saveContent,
				'thumb_img' => $saveThumb,
				'collect' => 1 
		), $value['id']);
		
		if (!ret) throw new Exception("新闻{$value['id']}，更新失败。");
	} catch (Exception $e) {
		Common::log(array( 
				$e->getCode(),
				$e->getMessage() 
		), 'collect.log');
	}
}

echo CRON_SUCCESS."\n";
