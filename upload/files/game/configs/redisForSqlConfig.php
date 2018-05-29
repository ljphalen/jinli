<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$config = array (
		'test' => array (
					'host' => '127.0.0.1',
					'port' => '6379',
    		        'write' => array (
    		                'host' => '127.0.0.1',
    		                'port' => '6379'
    		        ),
    		        'read' => array (
    		                '1' => array (
    		                        'host' => '127.0.0.1',
    		                        'port' => '6379'
    		                ),
    		                '2' => array (
    		                        'host' => '127.0.0.1',
    		                        'port' => '6379'
    		                ),
    		                '3' => array (
    		                        'host' => '127.0.0.1',
    		                        'port' => '6379'
    		                )
    		        ),
					'key-prefix'=>'game'
		),
		'product' => array (
					'host' => '10.168.79.215',
					'port' => '6779',
					'key-prefix'=>'game'
		),
		'develop' => array (
					'host' => '127.0.0.1',
					'port' => '6379',
    		        'write' => array (
    		                'host' => '127.0.0.1',
    		                'port' => '6379'
    		        ),
    		        'read' => array (
    		                '1' => array (
    		                        'host' => '127.0.0.1',
    		                        'port' => '6379'
    		                ),
    		                '2' => array (
    		                        'host' => '127.0.0.1',
    		                        'port' => '6379'
    		                ),
    		                '3' => array (
    		                        'host' => '127.0.0.1',
    		                        'port' => '6379'
    		                )
    		        ),
					'key-prefix'=>'game'
		) 
);
return defined('ENV') ? $config[ENV] : $config['product'];
