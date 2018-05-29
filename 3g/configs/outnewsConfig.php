<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *qq新闻接口
 */
$config['news']['qq'] = array(
	'1' => array(
		'name' => '腾讯•新闻',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=news&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'2' => array(
		'name' => '腾讯•财经',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=finance&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'3' => array(
		'name' => '腾讯•体育',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=sports&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'4' => array(
		'name' => '腾讯•娱乐',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=ent&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'5' => array(
		'name' => '腾讯•科技',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=tech&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'6' => array(
		'name' => '腾讯•时尚',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=lady&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'7' => array(
		'name' => '腾讯•图片',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=pic&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'8' => array(
		'name' => '腾讯•汽车',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=auto&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
	'9' => array(
		'name' => '腾讯•军事',
		'url'  => 'http://openapi.inews.qq.com/getNewsByChlidVerify?chlid=mil&refer=openapi_for_jinli&appkey=t5mGkB5ORnswFH6wINacVTf4vZMrWR&n=10',
	),
);
/**
 *搜狐新闻接口
 */
$config['news']['sohu'] = array(
	'201' => array(
		'name' => '搜狐•要闻',
		'url'  => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=1&page=1&picScale=3&showContent=1'
	),
	'202' => array(
		'name' => '搜狐•体育',
		'url'  => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=2&page=1&picScale=3&showContent=1',
	),
	'203' => array(
		'name' => '搜狐•娱乐',
		'url'  => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=3&page=1&picScale=3&showContent=1',
	),
	/*'204'=>array(
			'name' => '搜狐•微文',
			'url' => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=32&page=1&picScale=3&showContent=1',
	),*/
	'205' => array(
		'name' => '搜狐•军事',
		'url'  => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=5&page=1&picScale=3&showContent=1',
	),
	'206' => array(
		'name' => '搜狐•社会',
		'url'  => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=23&page=1&picScale=3&showContent=1',
	),
	/* '207'=>array(
			'name' => '搜狐•CBA',
			'url' => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=31&page=1&picScale=3&showContent=1',
	), */
	/* '208'=>array(
			'name' => '搜狐•NBA',
			'url' => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=15&page=1&picScale=3&showContent=1',
	), */
	'209' => array(
		'name' => '搜狐•女人',
		'url'  => 'http://api.k.sohu.com/api/open/channel/newsList.go?channelId=12&page=1&picScale=3&showContent=1',
	),
);

/**
 *凤凰新闻接口
 */
$config['news']['ifeng'] = array(
	'301' => array(
		'name' => '凤凰•头条',
		'url'  => 'http://api.3g.ifeng.com/clientnews_jinli?channelid=SYLB10'
	),
	'302' => array(
		'name' => '凤凰•娱乐',
		'url'  => 'http://api.3g.ifeng.com/clientnews_jinli?channelid=YL53',
	),
	'303' => array(
		'name' => '凤凰•图片',
		'url'  => 'http://api.3g.ifeng.com/clientnews_jinli?channelid=TP10',
	),
	'304' => array(
		'name' => '凤凰•财经',
		'url'  => 'http://api.3g.ifeng.com/clientnews_jinli?channelid=CJ33',
	),
	'305' => array(
		'name' => '凤凰•科技',
		'url'  => 'http://api.3g.ifeng.com/clientnews_jinli?channelid=KJ123',
	),
	'306' => array(
		'name' => '凤凰•历史',
		'url'  => 'http://api.3g.ifeng.com/clientnews_jinli?channelid=LS153',
	),
);


/**
 *新浪新闻接口
 */
$config['news']['sina'] = array(
	'501' => array(
		'name' => '新浪•底色',
		'url'  => 'http://api.dp.sina.cn/interface/i/hdpic/gionee_album.php?action=column&cid=20&wm=3164_0012'
	),
	'502' => array(
		'name' => '新浪•视界',
		'url'  => 'http://api.dp.sina.cn/interface/i/hdpic/gionee_album.php?action=column&cid=13&wm=3164_0012',
	),
	'503' => array(
		'name' => '新浪•奇趣',
		'url'  => 'http://api.dp.sina.cn/interface/i/hdpic/gionee_album.php?action=column&cid=16&wm=3164_0012',
	),
	'504' => array(
		'name' => '新浪•女性图片',
		'url'  => 'http://api.dp.sina.cn/interface/i/hdpic/gionee_album.php?action=album&ch=3&wm=3164_0012',
	),
	'505' => array(
		'name' => '新浪•博客图片',
		'url'  => 'http://api.dp.sina.cn/interface/i/hdpic/gionee_album.php?action=album&ch=54&wm=3164_0012',
	),
	'506' => array(
		'name' => '新浪•体育',
		'url'  => 'http://api.dp.sina.cn/interface/i/hdpic/gionee_album.php?action=album&ch=2&wm=3164_0012',
	),
	'507' => array(
		'name' => '凤凰•历史',
		'url'  => 'http://mpp.trends.com.cn/rss/dodata.action?publicationID=1341&type=4',
	),
);

return $config;