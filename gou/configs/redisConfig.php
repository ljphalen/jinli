<?php
$config = array (
		'test' => array (
            'host' => '10.200.204.105',
            'port' => '6379'
		),
		'product' => array (
            'host' => '10.200.54.223',
            'port' => '6779'
        ),
		'develop' => array (
            'host' => '127.0.0.1',
            'port' => '6379'
		)
);
return defined('ENV') ? $config[ENV] : $config['product'];
