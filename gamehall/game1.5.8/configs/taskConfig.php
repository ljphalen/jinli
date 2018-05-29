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
		'Task_Tasks_LoginClient' => array(
				//task['condition_type']=>ruleClass
				1 => 'Task_Rules_LoginClientFirst', //登陆客户端-首次登陆
		),
        'Task_Tasks_LoginGame' => array(
                1 => 'Task_Rules_LoginGameFirst',        // 首次登陆游戏
                2 => 'Task_Rules_LoginGameDaily',        // 每天首次登陆游戏
        ),
		'Task_Tasks_UserConsume' => array(
		       //task['condition_type']=>ruleClass
		       2 =>  'Task_Rules_ConsumeSingleOld', //消费返利-单次消费（旧）
		       3 =>  'Task_Rules_ConsumeTotalOld', //消费返利-累计消费(旧) 
		       5 =>  'Task_Rules_ConsumeSingle', //消费返利-单次消费(新)
		       6 =>  'Task_Rules_ConsumeDailyFirst', //消费返利-每天首次消费(新)
		       7 =>  'Task_Rules_ConsumeTotal', //消费返利-累计消费(新)
		),
        'Task_Tasks_UserPayment' => array(
                1 => 'Task_Rules_PaymentDailyOld',   // 每天首次充值（单区间-百分比）,旧方式
                2 => 'Task_Rules_PaymentDaily',   // 每天首次充值（多区间-百分比） 
                3 => 'Task_Rules_PaymentOnce',       // 单次充值（多区间-百分比）
                4 => 'Task_Rules_PaymentSum',        // 累计充值（多区间-百分比）
        )
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
