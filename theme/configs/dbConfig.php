<?php

if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
    'test' => array(
        'default' => array(
            'adapter' => 'PDO_MYSQL',
            'host' => 'testpushdb01.mysql.aliyun.com',
            'username' => 'themew',
            'password' => 'sog958_132tiu',
            'dbname' => 'theme',
            'displayError' => 1
        )
    ),
    'product' => array(
        'default' => array(
            'adapter' => 'PDO_MYSQL',
            'host' => 'prodpubdb01.mysql.rds.aliyuncs.com',
            'username' => 'prodthemedbw',
            'password' => 'jiysd345',
            'dbname' => 'prodthemedb',
            'displayError' => 0
        )
    ),
    'develop' => array(
        'default' => array(
            'adapter' => 'PDO_MYSQL',
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname' => 'theme',
            'displayError' => 1
        )
    )
);
return defined('ENV') ? $config[ENV] : $config['product'];
