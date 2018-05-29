<?php
return array(
		//开发者注册的证件上传配置
		'DEV_GROUP'=>array(
			'maxSize'	=>1024*1024*20,           						//文件大小限制
			'allowExts'	=>array('jpg','gif','png','jpeg','pdf'),	//文件类型设置
			'isWater'	=>false        								//是否加水印
		),
		
		//后台管理上传配置
		'ADMIN_GROUP'=>array(
				'maxSize'	=>1024*1024*20,
				'allowExts'	=>array('jpg','gif','png','jpeg','pdf','txt','xls','xlsx','doc','docx','apk','rar','zip'),
				'isWater'	=>false
		),

		//产品截图图片
		'PIC'=>array(
				'maxSize'=>1024*1024*20,
				'allowExts'=>array('png','jpg'),
				'maxNum'=>10,
				'minNum'=>3,
		),
);