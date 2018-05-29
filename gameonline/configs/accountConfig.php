<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
				'AppId'  => 'AD9C87D372AA4D848A49723DF8BF8655',
				'AppKey' => '966E846C6FD543E59BDE4C9CCAF1554D',
				'AppName' => '游戏大厅',
				'AssocType' => 'gionee',
			),
	'product' => array(
				'AppId'  => 'FEA742E142404CA8A663440A8E8D6500',
				'AppKey' => '73067068424B4DD79C6D8EBEFAF5BBE0',
				'AppName' => '游戏大厅',
				'AssocType' => 'gionee',
			),
			
	'develop' => array(
				'AppId'  => 'AD9C87D372AA4D848A49723DF8BF8655',
				'AppKey' => '966E846C6FD543E59BDE4C9CCAF1554D',
				'AppName' => '游戏大厅',
				'AssocType' => 'gionee',
			)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];