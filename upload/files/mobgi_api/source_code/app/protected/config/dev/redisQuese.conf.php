<?php

//redis队列配置 
$redisQconfig['redisQconfig'] =array(
		'queues' => array('adAnalytics','testRedisQ'),
		'queuesConfig' => array(
				'adAnalytics' => array(
						'server'=>'192.168.2.44',//队列服务器地址
						'port'=>6379, //队列服务器端口
						'maxLength'=>2000000, //最大的队列长度
						'consumerMaxLength'=>500, //每次消费的最大值
						'connectType'=>0 // 0 短链接  1 长连接
				),			
				'testRedisQ' => array(
						'server'=>'192.168.2.44',//队列服务器地址
						'port'=>6379, //队列服务器端口
						'maxLength'=>2000000, //最大的队列长度
						'consumerMaxLength'=>500, //每次消费的最大值
						'connectType'=>0 // 0 短链接  1 长连接
				),
		)
);
