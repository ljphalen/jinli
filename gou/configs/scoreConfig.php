<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
    array(
        'id'        => 1,                   //编码    (required)
        'adapter'   => 'mrqd',              //适配器  (required)
        'title'     => '签到',               //名称   (required)
        'subtitle'  => '连续三天额外奖励50分',  //副标题
        'score'     => 50,                  //分值   (required)
        'limit'     => 1,                   //每日限制次数(required), 0表示不分日期且仅有一次
        'upto'      => array(               //累计规则
            array(
                'id'        => 11,
                'title'     => '累计签到15次',
                'score'     => 1000,
                'total'     => 15,      //累计total, 即可获取该积分
            ),
        ),
        'runon'     =>array(            //连续规则
            array(
                'id'        => 12,
                'title'     => '每连续登录3次',
                'score'     => 50,
                'total'     => 3,
            )
        )
    ),
    array(
        'id'        => 2,
        'adapter'   => 'mrkj',
        'title'     => '砍价',
        'subtitle'  => '每日最高奖励50分',              //副标题
        'score'     => 25,
        'limit'     => 2,               //一天只能砍价2次
        'upto'      => array(
            array(
                'id'        => 21,
                'title'     => '累计砍价30次',
                'score'     => 1000,
                'total'     => 30,
            ),
        )
    ),
    array(
        'id'        => 3,
        'adapter'   => 'mrfxkj',
        'title'     => '分享砍价商品',
        'subtitle'  => '每日最高奖励120分',              //副标题
        'score'     => 30,
        'limit'     => 4,
        'upto'      => array(
            array(
                'id'        => 31,
                'title'     => '累计分享砍价60次',
                'score'     => 1000,
                'total'     => 60,
            ),
        )
    ),
    array(
        'id'        => 4,
        'adapter'   => 'mrhykj',
        'title'     => '好友帮忙砍价',
        'subtitle'  => '每日最高奖励120分',              //副标题
        'score'     => 30,
        'limit'     => 4,
        'upto'      => array(
            array(
                'id'        => 41,
                'title'     => '累计邀请好友砍价60次',
                'score'     => 1000,
                'total'     => 60,
            ),
        )
    ),
    array(
        'id'        => 5,
        'adapter'   => 'mrfxgwdt',
        'title'     => '分享购物大厅给好友',
        'subtitle'  => '每日最高奖励160分',              //副标题
        'score'     => 40,
        'limit'     => 4,
    ),
    array(
        'id'        => 6,
        'adapter'   => 'mrfxlj',
        'title'     => '分享购物、知物链接',
        'subtitle'  => '每日最高奖励100分',              //副标题
        'score'     => 20,
        'limit'     => 5,
    ),
    array(
        'id'        => 7,
        'adapter'   => 'gzwx',
        'title'     => '关注购物大厅微博',
        'subtitle'  => '',              //副标题
        'score'     => 50,
        'limit'     => 0,
    ),
);
return $config;