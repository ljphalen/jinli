<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
    'test' => array(
        'baidu' => array(
            'apiKey' => '5U4B33TI0XHsz5lotmxzrYfG',
            'secretKey' => 'PlvkXL2CdaEkuglW4ZufS9IWrg9waHou',
        )
    ),
    'product' => array(
        'baidu' => array(
            'apiKey' => '5U4B33TI0XHsz5lotmxzrYfG',
            'secretKey' => 'PlvkXL2CdaEkuglW4ZufS9IWrg9waHou',
        )
    ),
    'develop' => array(
        'baidu' => array(
            'apiKey' => 'v0Vod3TVOjdLdftQICpesQBB',
            'secretKey' => '7z1u2GLZA6B6bgqUxcDin5PN4MGWGo2w',
        )
    )
);
return defined('ENV') ? $config[ENV] : $config['product'];
