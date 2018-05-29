<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
    'test' => array(
        'socket'=>array(
            'ip'=>'127.0.0.1',
            'port' => '8088',
        ),
    ),
    'product' => array(
        'socket'=>array(
            'ip'=>'10.165.102.172',
            'port' => '8088',
        ),
    ),
    'develop' => array(
        'socket'=>array(
            'ip'=>'127.0.0.1',
            'port' => '8088',
        ),
    )
);
return defined('ENV') ? $config[ENV] : $config['product'];
