<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//赠送事件与任务配置
$config['route'] = array(
    'login_client' => array(
        //task['htype'] => taskclass
        1 => 'Task_Tasks_LoginClient',
    ),
    'login_game' => array(
        2 => 'Task_Tasks_LoginGame',
    ),
    'user_consume' => array(
        //task['htype'] => taskclass
        3 => 'Task_Tasks_UserConsume'
    ),
    'user_payment' => array(
        4 => 'Task_Tasks_UserPayment'
    ),
);

//任务与规则配置
$config['rule'] = array(
    'Task_Tasks_LoginClient' => 'Task_Rules_FixedDenom', //固定额赠送规则
    'Task_Tasks_LoginGame' => 'Task_Rules_FixedDenom', //固定额赠送规则
    'Task_Tasks_UserConsume' => 'Task_Rules_Consume', //消费赠送规则
    'Task_Tasks_UserPayment' => 'Task_Rules_Payment', //充值赠送规则
);

//物品与消息类型配置
$config['message'] = array(
    'Acoupon' => '103'
);

//游戏版本配置参数
$config['version'] = array(
    '1.5.4' => 2,
    '1.5.5' => 3,
    '1.5.6' => 4,
    '1.5.7' => 5
);
return $config;
