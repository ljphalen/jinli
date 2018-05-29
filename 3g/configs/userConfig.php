<?php

return array(

	//用户等级
	'rank'               => array(
		'1' => array(
			'1'  => array('level' => 1, 'name' => '实习生', 'range' => '0-4'),
			'2'  => array('level' => 2, 'name' => '试用期', 'range' => '5-134'),
			'3'  => array('level' => 3, 'name' => '职场新人', 'range' => '135-404'),
			'4'  => array('level' => 4, 'name' => '助理', 'range' => '405-719'),
			'5'  => array('level' => 5, 'name' => '见习主管', 'range' => '720-1439'),
			'6'  => array('level' => 6, 'name' => '主管', 'range' => '1440-2879'),
			'7'  => array('level' => 7, 'name' => '初级经理', 'range' => '2880-#'),
		),
	),
	//等级分组
	'level_group_name'   => array(
		'1' => '职场称谓',
	),
	//活动分类
	'action_type'        => array(
		'1' => array('key' => 'signin', 'val' => '打卡'),
		'2' => array('key' => 'product', 'val' => '生产类商品'),
		'3' => array('key' => 'cosume', 'val' => '消费类商品'),
	),
	//签到基本配置
	'signin'             => array(
		'name'         => '打卡',
		'scores'       => '5',
		'continueDays' => '7',
		'rewards'      => '20',
		'status'       => '1'
	),
	//用户操作事件
	'event'              => array(
		'1' => array('tag' => 'signin', 'name' => '签到', 'status' => 1, 'rule' => 'innerMsg,scoreLog'),
		'2' => array('tag' => 'ads', 'name' => '看图', 'status' => 1, 'rule' => 'innerMsg, scoreLog'),
		'3' => array('tag' => 'fare', 'name' => '兑换话费', 'status' => 1, 'rule' => 'innerMsg,scoreLog,msg'),
		'4' => array('tag' => 'incr', 'name' => '增加积分', 'status' => 1, 'rule' => 'innerMsg,scoreLog'),
		'5' => array('tag' => 'desc', 'name' => '减少积分', 'status' => 0, 'rule' => 'innerMsg,scoreLog'),
	),
	//订单状态
	'statusFlag'         => array(
		'-1' => '取消',
		'0'  => '待处理',
		'1'  => '已完成',
		'2'  => '处理中',
	),
	//支付状态
	'pay_status'         => array(
		'-1' => '支付失败',
		'0'  => '待支付',
		'1'  => '已支付',
		'2'  => '支付中',
		'3'  => '退还积分'
	),
	//发货状态（目前并无用到，主要针对后者可能的实物商品）
	'shipping_status'    => array(
		'0'  => '待处理',
		'1'  => "已完成",
		'2'  => '配送中',
		'3'  => '已发货',
		'-1' => '取消配送',
	),
	//积分变动标识
	/**
	 * 设置规则为1 开头为用户动作赚取的积分动作  2 开头为 对用户赠送的积分动作 3 开头的key 为用户消费积分的动作行为
	 */
	'actions'            => array(
		'101' => '签到',
		'102' => '看图',
		'103' => '下载游戏',
		'104' => '抽奖送金币',
		'105' => '关注微信送积分',
		'106' => '建设中',
		'107'=>'答题',
		'201' => '连续签到奖励',
		'202' => '单天连续操作奖励',
		'203' => '中奖发放',
		'204' => '升级奖励',
		'205' => '返还冻结积分',
		'206' => '管理员赠送',
		'207' => '发现宝贝',
		'208' => '浏览器在线奖励',
		'209' => '浏览器版本升级奖励',
		'210' => '端午活动奖励',
		'211'=>	'答题奖励',
		'212'=>	'答题额外奖励',
		'213'=>	'七夕活动奖励',
		'214'=>	'十一活动奖励',
		'215'	=>'周年庆活动奖励',
        '216'	=>'双十一活动奖励',
		'301' => '兑换话费',
		'302' => '兑换流量',
		'303' => '抽奖消耗',
		'304' => '兑换礼品',
		'305' => '兑换购物券',
		'306' => '抽奖冻结',
		'307' => '赠送抽奖机会',
		'308' => '兑换Q币',
		'309' => '兑换通话时长',
		'310' => '实物兑换',
		'311' => '夺宝消耗',
		'312'=>'系统回收',
		'401' => '扣除冻结积分',
		'501'	=>'账号充值',
	),
	'activity_name'=>'双十一秒杀活动',
	//抽奖活动中奖品的类型
	'prize_types'        => array(
		'1' => '赠送金币',
		/* 	'2'		=>'赠送话费',
			'3'		=>'赠送购物券' */
	),
	//广告位标识
	'pos_identifiers'    => array(
		'index'   => array(
			'name'   => '首页',
			'prefix' => 'IX',
		),
		'produce' => array(
			'name'   => '产生积分列表页',
			'prefix' => 'PD',
		),
		'cosume'  => array(
			'name'   => '消耗积分列表页',
			'prefix' => 'CS',
		),
		'detail'  => array(
			'name'   => '消耗积分详情页',
			'prefix' => 'DT',
		),
		'success' => array(
			'name'   => '积分兑换成功页',
			'prefix' => 'SC',
		)
	),
	//广告位标识

	//广告类型
	'ads_type'           => array(
		'1' => array('name' => '图片类广告', 'tag' => 'IMG'),
		'2' => array('name' => '文字类广告', 'tag' => 'WORD'),
		'3' => array('name' => '图文类广告', 'tag' => 'MIX')
	),
	//充值状态
	'recharge_status'    => array(
		'-1' => '充值失败',
		'0'  => '充值中',
		'1'  => '充值成功'
	),
	'virtual_type_list'  => array(
		'1'   => '兑换话费',
		'2'   => '购物券',
		'3'   => '流量包',
		'4'   => 'Q币',
		'5'	=>'实物兑换',
		'999' => '兑换通话时长',
	),
	'ofpay_api_log'      => array(
		'1' => '话费充值',
		'2' => '购物券',
		'3' => '手机流量包',
		'4' => 'Q币',
	),
	'innermsg_type_list' => array(
		'1'   => '兑换话费',
		'2'   => '购物券',
		'3'   => '流量包',
		'4'   => 'Q币',
		'5'   => '实物兑换',
		'6' 	=>'经验升级',
		'7'	=>'每日登陆',
		'8'	=>'用户反馈',
		'9'	=>'专题互动',
		'10'	=>'反馈被采纳',
		'11'	=>'赠送通话时长',
		'12'	=>'活动金币',
		'13'=>'发放书券',
		'14'=>'管理员赠送金币',
		'15'=>'七夕活动奖品',
		'16'=>'国庆活动奖励',
		'17'=>'周年庆活动奖励',
        '18'=>'双十一活动奖励',
		'888' => '返还金币',
		'999' => '兑换通话时长',
	),
	'express_name_list'  => array(
		'1'  => '顺丰',
		'2'  => 'EMS',
		'3'  => '中通快递',
		'4'  => '圆通速递',
		'5'  => '申通',
		'6'  => '天天快递',
		'7'  => '韵达',
		'8'  => '宅急送',
		'9'  => '德邦',
		'10' => '速尔'
	),

	'gee_test'=>array(
			'captcha_id'				=>'e4f94fade04c00aaae00afbed5b8115d',
			'private_key'			=>'02a2829ec6a81cd43ee4e9f0da0f3b9a',
),

	'experience_level_data'=>array(
		'1'=>array('level'=>1,'range'=>'0-4'),
		'2'=>array('level'=>2,'range'=>'5-134'),
		'3'=>array('level'=>3,'range'=>'135-404'),
		'4'=>array('level'=>4,'range'=>'405-719'),
		'5'=>array('level'=>5,'range'=>'720-1439'),
		'6'=>array('level'=>6,'range'=>'1440-2879'),
	),
	
'exprience_rewards_type'=>array(
		'1'=>'赠送看图机会',
		'2'=>'赠送抽奖机会',
		'3'=>'赚送通话时长',
),
		
	'experience_activity_type'=>array(
			'1'=>'每日登陆',
			'2'=>'用户反馈',
			'3'=>'参与专题互动',
			'4'=>'反馈意见被采纳',
		),
		
		'gou_recharge_url'=>array(
				'test'	=>'http://t-channel.game.gionee.com:8008/gionee-wallet/walletService.do',
				'develop'=>'http://t-channel.game.gionee.com:8008/gionee-wallet/walletService.do',
				'product'		=>'http://wallet.gionee.com:8088/gionee-wallet/walletService.do'
	),

);