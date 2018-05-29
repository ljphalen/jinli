<?php
return array (
		'APKTOOL' => '/usr/bin/',		//aapt默认安装路径，Data/Bin目录下的版本仅支持Centos x86_64，其它版本请自行安装
		'MAXSIZE' => 1024*1024*300,		//300M
		'MAX_BSDIFF_SIZE' => 1024*1024*50,		//50M以上的应用不生成差分包
		'EXTS' => array('apk'),
		'PATH' => '/tmp/',
		
		'FILE_PATH_DIR' => ATTACH_PATH,          //应用文件存放路径，保留反斜杠
		'FILE_PATH_URL' => 'http://dev.localhost.gionee.com/Data/Attachments/',	  //应用文件访问URL，保留反斜杠
		'GAMEDL_PATH_URL' => '/Data/Attachments/',    //游戏apk跟差分包文件访问URL，保留反斜杠
		'JAVA_BIN_PATH' => '/usr/lib/jvm/java/bin',   //java bin 目录 获取apk签名需要
		'ONLINE_OFFLINE_REDIS_QUEUE_KEY' => 'online_offline_redis_queue', //上下线队列
						
		'ICONEXT' => array (
				'png',
				'jpg',
				'9pg',
				'jpeg'
		), // 图标文件后缀类型
		'RES' => 'res/', // Apk资源目录
		'VALUES' => 'values/', // Apk字符串所在目录
		'DRAWABLE' => 'drawable/', // 图标所在目录
		'SDK_VER' => array ( // SDK适应版本记录表对应字段
				3 => '1.5',
				4 => "1.6",
				5 => "2.0",
				6 => '2.0.1',
				7 => "2.1.x",
				8 => '2.2.x',
				9 => '2.3, 2.3.1, 2.3.2',
				10 => '2.3.3, 2.3.4',
				11 => "3.0.x",
				12 => "3.1.x",
				13 => "3.2",
				14 => "4.0, 4.0.1, 4.0.2",
				15 => "4.0.3, 4.0.4",
				16 => "4.1, 4.1.1, 4.1.2",
				17 => "4.2.x",
				18 => "4.3",
				19 => "4.4",
		),
		'SCREEN' => array ( // SDK适应版本记录表对应字段
				3 => 'res_240_320',
				4 => "res_320_480",
				5 => "res_480_800",
				6 => 'res_480_854' 
		) 
);
