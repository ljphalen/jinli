<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
		'test' => array('pushTokenUrl' => 'http://t-push.gionee.com:8001/sas/requestToken',
		                'pushServiceUrl' => 'http://t-push.gionee.com:8001/push_service?v=1',
                        'applicationID'=>'e107004753164fd4b92d2d526c6615fa',
                        'passwd'=>'123456'
		),
		'develop' => array('pushTokenUrl' => 'http://pushapi.gionee.com:8001/sas/requestToken',
		                   'pushServiceUrl' => 'http://pushapi.gionee.com:8001/push_service?v=1',
		                   'applicationID'=>'e107004753164fd4b92d2d526c6615fa',
                           'passwd'=>'yxdt6615fa',
		),
		'product' =>  array('pushTokenUrl' => 'http://pushapi.gionee.com:8001/sas/requestToken',
		                   'pushServiceUrl' => 'http://pushapi.gionee.com:8001/push_service?v=1',
		                   'applicationID'=>'e107004753164fd4b92d2d526c6615fa',
                           'passwd'=>'yxdt6615fa',
		),					
);
return defined('ENV') ? $config[ENV] : $config['product'];