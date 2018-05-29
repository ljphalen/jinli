<?php

//redis队列配置 
$redisQconfig['redisQconfig'] =array(
		'queues' => array('adAnalytics','testRedisQ'),
		'queuesConfig' => array(
				'adAnalytics' => array(
						'server'=>'redis1.ad.ids.com',//队列服务器地
						'port'=>6400, //队列服务器端口
						'maxLength'=>2000000, //最大的队列长度
						'consumerMaxLength'=>500, //每次消费的最大值
						'connectType'=>0 // 0 短链接  1 长连接
				),			
				'testRedisQ' => array(
						'server'=>'redis1.ad.ids.com',//队列服务器地址
						'port'=>6400, //队列服务器端口
						'maxLength'=>2000000, //最大的队列长度
						'consumerMaxLength'=>500, //每次消费的最大值
						'connectType'=>0 // 0 短链接  1 长连接
				),
		)
);
