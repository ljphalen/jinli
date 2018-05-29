<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
    'test' => array(
        'appID' => 'wxd9dc4d15229198ff',
        'appSecret' => 'd1ad0958d0002148694ec503bd785481'
    ),
    'develop' => array(
        'appID' => 'wx56e1481ba72ca1d3',
        'appSecret' => 'ac684e82633dc61e1f9eacb994aa2a43'
    ),
    'product' => array(
        'appID' => 'wxd9dc4d15229198ff',
        'appSecret' => 'd1ad0958d0002148694ec503bd785481'
    ),
);
return defined('ENV') ? $config[ENV] : $config['product'];