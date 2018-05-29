<?php
/**
 * 全局配置文件
 * 该配置文件为配置文件的入口文件，只用来导入其它扩展配置文件，非 debug 模式下会编译到 runtime 中
 * @link http://doc.thinkphp.cn/manual/extend_config.html
 */
return array(
		'LOAD_EXT_CONFIG' => array(
				'database',
				'router',
				'module',
				'system',
				'api',
				'url',
				'email',
				'apk'=>"apk",
				'upload',
				"ftp",
		        "redis",
		        'testin',
				"signature"
		),
);
