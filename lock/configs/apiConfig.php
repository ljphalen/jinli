<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		/*精灵锁屏*/
		'label_list_url'=>'http://locker.qiigame.com/locker/partner/getLabelList',
		'scene_list_url'=>'http://locker.qiigame.com/locker/partner/getSceneList',
		'scene_kernel_list_url'=>'http://locker.qiigame.com/locker/partner/getAllSceneKernelList',
		'scene_download_count'=>'http://locker.qiigame.com/locker/partner/sceneDownloadCount',
		'vendor_key'=>'5914564577a72feec4502d4dfb282a18',
			
		/*push接口*/
		'appid' => '27eeb8f91761475eb6ac0bc9db98777c',
		'password' => 'leiyong',
		'url' => 'http://push.gionee.com'
	),
	'product' => array(
		/*精灵锁屏*/
		'label_list_url'=>'http://locker.qiigame.com/locker/partner/getLabelList',
		'scene_list_url'=>'http://locker.qiigame.com/locker/partner/getSceneList',
		'scene_kernel_list_url'=>'http://locker.qiigame.com/locker/partner/getAllSceneKernelList',
		'scene_download_count'=>'http://locker.qiigame.com/locker/partner/sceneDownloadCount',
		'vendor_key'=>'5914564577a72feec4502d4dfb282a18',
			
		/*push接口*/
		'appid' => '27eeb8f91761475eb6ac0bc9db98777c',
		'password' => 'leiyong',
		'url' => 'http://push.gionee.com'
	),
	'develop' => array(
		/*精灵锁屏*/
		'label_list_url'=>'http://221.181.64.231:8090/locker/partner/getLabelList',
		'scene_list_url'=>'http://221.181.64.231:8090/locker/partner/getSceneList',
		'scene_kernel_list_url'=>'http://221.181.64.231:8090/locker/partner/getAllSceneKernelList',
		'scene_download_count'=>'http://221.181.64.231:8090/locker/partner/sceneDownloadCount',
		'vendor_key'=>'5914564577a72feec4502d4dfb282a18',
			
		/*push接口*/
		'appid' => '27eeb8f91761475eb6ac0bc9db98777c',
		'password' => 'leiyong',
		'url' => 'http://push.gionee.com'		
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
