<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 登录游戏活动-每日登录
 * @author zzw
 */
class Task_Rules_LoginGameDaily extends Task_Rules_LoginGameBase {
    /**
     * 构建搜索，用来检查该用户是否已经赠送过奖励了
     * @param array $task   活动配置
     * @param string $uuid  玩家ID
     * @return boolean
     */
    protected function onBuildQueryForSendOver($task, $uuid) {
        $params = array();
        $params['uuid'] = $uuid;
        $params['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $params['sub_send_type'] = $task['id'];
        $currentTime = date('Y-m-d',Common::getTime());
        $startTime = strtotime($currentTime.' 00:00:00');
        $endTime = strtotime($currentTime.' 23:59:59');
        $params['consume_time'][0] = array('>=', $startTime);
        $params['consume_time'][1] = array('<=', $endTime);
        return $params;
    }
};
