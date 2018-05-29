<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config = array();
$config[] = array(
                'name' => '消息管理',
                'items'=>array(
                                'msgMgr'
                )
);

$config[] = array(
                'name' => '用户管理',
                'items'=>array(
                                'userMgr'
                )
);

$config[] = array(
                'name' => '菜单管理',
                'items'=>array(
                                'menuMgr'
                )
);

$config[] = array(
                'name' => '礼包管理',
                'items'=>array(
                                'giftMgr'
                )
);

$config[] = array(
                'name' => '素材管理',
                'items'=>array(
                                'materialMgr'
                )
);

$view = array(
		'msgMgr'=>array('自定义回复', '/Admin/Keyword/index#J_hash_reply'),
        'userMgr'=>array('用户管理', '/Admin/Weixinuser/index'),
        'menuMgr'=>array('自定义菜单', '/Admin/Menu/index'),
        'giftMgr'=>array('礼包管理', '/Admin/Gift/index'),
	    'materialMgr'=>array('素材库', '/Admin/Material/index'),
);

return array($config, $view);
