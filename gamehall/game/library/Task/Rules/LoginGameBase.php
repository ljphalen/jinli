<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 登录游戏活动-基类
 * @author zzw
 */
abstract class Task_Rules_LoginGameBase extends Task_Rules_Base {

    /**
     * 构建搜索，用来检查该用户是否已经赠送过奖励了
     * @return boolean
     */
    abstract protected function onBuildQueryForSendOver();

    /**
     * 解析执行规则
     */
    public function onCaculateGoods() {
        $result = $this->filterUserByUuid();
        if (!$result) { return array(); }

        $result = $this->canPartiActivity();
        if (!$result) { return array(); }

        if ($this->isSendOver()) {
            $this->debug(array('msg' => "用户[{$this->mRequest['uuid']}]已经赠送过奖励了"));
            return array();
        }

        $sendGoods = $this->caculateGoodsInternal();
        $this->info(array('msg' => "用户[{$this->mRequest['uuid']}]本次任务赠送的物品,任务ID[{$this->mTaskRecord['id']}]"));
        return $sendGoods;
    }

    /**
     * 检查该用户是否已经赠送过奖励了
     * @param array $task   活动配置
     * @param string $uuid  玩家ID
     * @return boolean
     */
    private function isSendOver() {
        $params = $this->onBuildQueryForSendOver();
        $sendResult = Client_Service_TicketTrade::getBy($params);
        if($sendResult){
            return true;
        }
        return false;
    }
};
