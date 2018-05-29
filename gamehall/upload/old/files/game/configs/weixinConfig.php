<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
    'test' => array(
        'appID' => 'wxd9dc4d15229198ff',
        'appSecret' => 'd1ad0958d0002148694ec503bd785481'
    ),
    'product' => array(
        'appID' => 'wxfdabb600b600b9b1',
        'appSecret' => '818bb05e681318bf936675588dfbfcd8'
    ),
    'develop' => array(
        'appID' => 'wxd9dc4d15229198ff',
        'appSecret' => 'd1ad0958d0002148694ec503bd785481'
    ),
);
return defined('ENV') ? $config[ENV] : $config['product'];