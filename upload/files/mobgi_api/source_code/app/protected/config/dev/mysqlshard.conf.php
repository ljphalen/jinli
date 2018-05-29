<?php
/**
 * User: Intril.Leng
 * Date: 13-4-16
 * Time: 上午9:53
 */
$mysqlShard['shardHost'] = array(
    'leaderboard_db' => array(
        '0-99' => array(
            'host' => '192.168.3.20',
            'login' => 'root',
            'password' => '12345678',
            'encoding' => 'utf8',
        ),  
    ),
    'player_db' => array(
        '0-9'  => array(
            'host' => '192.168.3.20',
            'login' => 'root',
            'password' => '',
            'encoding' => 'utf8',
        )
    ),
    'player_games_once_db' => array(
        '0-9'  => array(
            'host' => '192.168.2.32',
            'login' => 'root',
            'password' => 'cxycxy',
            'encoding' => 'utf8',
        )
    ),
    'player_achievements' => array(
        '0-9'  => array(
            'host' => '192.168.3.20',
            'login' => 'root',
            'password' => '12345678',
            'encoding' => 'utf8',
        )
    )
);

$mysqlShard['shardConfig'] = array(
    'leaderboard_db' => array(
        'databases'  => array(1, 100),
        'tables' => array(100, 100)
    ),
    'player_db' => array(
        'databases'  => array(1, 10),
        'tables' => array(10, 10)
    ),
    'player_games_once_db' => array(
        'databases'  => array(1, 10),
        'tables' => array(10, 10)
    ),
    'player_achievements' => array(
        'databases'  => array(1, 10),
        'tables' => array(10, 10)
    )
);