<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 登录游戏活动-首次登录
 * @author zzw
 */
class Task_Rules_LoginGameFirst extends Task_Rules_LoginGameBase {
    /**
     * 构建搜索，用来检查该用户是否已经赠送过奖励了
     * @return boolean
     */
    protected function onBuildQueryForSendOver() {
        $params = array();
        $params['uuid'] = $this->mRequest['uuid'];
        $params['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $params['sub_send_type'] = $this->mTaskRecord['id'];
        return $params;
    }
};
