
---成长值
CREATE TABLE IF NOT EXISTS `t` (
  `userId` varchar(96) NOT NULL DEFAULT '',
  `sum` decimal(36,2) DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		---------支付提供的总消费记录---------
		
		---------支付提供的总消费记录---------
		
--------支付提供trade_time之前的记录，游戏大厅自己统计trade_time及之后的记录，因为上线过程中还有用户消费
create view t_view as SELECT `uuid`, sum(`money`) as `expr` FROM `game_client_money_trade` WHERE `event`=1 and `trade_time`>1444233600 group by `uuid`;
update t, t_view set t.`sum` = t_view.`expr`+t.`sum` where t.userId=t_view.uuid;
drop view t_view;
update game_user_info, t set game_user_info.vip_mon_expr = t.`sum` where game_user_info.uuid=t.userId;
drop table t;

---导入成长值记录
INSERT INTO `game_user_vip_expr`(`uuid`, `type`, `add_expr`, `logs`, `create_time`) 
SELECT trade.uuid, 1 as type, trade.money as add_expr, trade.api_key as logs, trade.trade_time as create_time FROM `game_client_money_trade` as trade WHERE trade.event=1;

---导入用户玩游戏记录
CREATE TABLE IF NOT EXISTS `game_user_game_log2` (
  `uuid` varchar(50) NOT NULL,
  `api_key` varchar(50) NOT NULL,
  `consume_time` int(10) DEFAULT '0',
  PRIMARY KEY (`uuid`,`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
create view ttt_view as (SELECT * FROM  `game_client_money_trade` WHERE  `event` =1 ORDER BY `trade_time` DESC);
INSERT INTO `game_user_game_log2` SELECT uuid, api_key, trade_time FROM ttt_view GROUP BY `uuid` ,  `api_key`;
drop view ttt_view;
TRUNCATE TABLE `game_user_game_log`;
INSERT INTO `game_user_game_log` (SELECT log.uuid, games.id, log.consume_time, log.consume_time FROM game_user_game_log2 log inner join game_resource_games games on log.api_key=games.api_key);
drop table game_user_game_log2;

