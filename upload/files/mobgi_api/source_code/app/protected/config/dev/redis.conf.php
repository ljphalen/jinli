<?php

/*
 * Redis相关配置
 */
//redis server
$rdconfig['REDIS_SERVER'] = array(
    array(
        'host' => '192.168.0.14',
        'port' => '6392'
    )
);


//cache redis server(只当缓存并没有数据持久化)
$rdconfig['CACHE_REDIS_SERVER_DEFAULT'] = array(//config,app,products
        'host' => '192.168.0.14',
        'port' => '6392'
);
$rdconfig['CACHE_REDIS_SERVER_1'] = array(//adinfo
        'host' => '192.168.0.14',
        'port' => '6393'
);
$rdconfig['CACHE_REDIS_SERVER_2'] = array(//channel,gameconfig,comstom INSTALL_REMIND,adpos
        'host' => '192.168.0.14',
        'port' => '6394'
);
$rdconfig['CACHE_REDIS_SERVER_3'] = array(//limit,plan
        'host' => '192.168.0.14',
        'port' => '6395'
);
$rdconfig['CACHE_REDIS_SERVER_4'] = array(//adlist
        'host' => '192.168.0.14',
        'port' => '6396'
);
$rdconfig['CACHE_REDIS_SERVER_5'] = array(//rtb product_adinfo
        'host' => '192.168.0.14',
        'port' => '6600'
);
$rdconfig['CACHE_REDIS_CHEAT_CONFIG'] = array(//adlist
        'host' => '192.168.0.14',
        'port' => '6500',
        'db'   => 0
);
$rdconfig['CACHE_REDIS_CHEAT_APP'] = array(//blackapp
        'host' => '192.168.0.14',
        'port' => '6500',
        'db'   => 11
);
$rdconfig['CACHE_REDIS_CHEAT_USER'] = array(//blackuser
        'host' => '192.168.0.14',
        'port' => '6500',
        'db'   => 10
);
$rdconfig['CACHE_REDIS_POS_KEY'] = array(//adlist
        'host' => '192.168.0.14',
        'port' => '6391',
        //'db'   => 10
);
$rdconfig['CACHE_REDIS_RTB_MONEY_KEY'] = array(//adlist
        'host' => '192.168.0.14',
        'port' => '6600',
        //'db'   => 10
);

$rdconfig['CACHE_REDIS_DIRECTIONAL_CONFIG_KEY'] = array(//adlist
        'host' => '192.168.0.14',
        'port' => '6500',
        //'db'   => 10
);

$rdconfig['IMPLANTABLE_REDIS_CACHE_DEFAULT'] = array(
        'host' => '192.168.0.14',
        'port' => '8001'
);

?>
