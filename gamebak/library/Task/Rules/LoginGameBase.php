<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 登录游戏活动-基类
 * @author zzw
 */
abstract class Task_Rules_LoginGameBase extends Task_Rules_Base {
    
    /**
     * 构建搜索，用来检查该用户是否已经赠送过奖励了
     * @param array $task   活动配置
     * @param string $uuid  玩家ID
     * @return boolean
     */
    abstract protected function onBuildQueryForSendOver($task, $uuid);
    
    /**
     * 解析执行规则
    */
    public function onCaculateGoods() {
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        $taskId = $task['id'];
        
        $result = $this->filterUserByUuid($task, $uuid);
        if (!$result) { return array(); }

        $result = $this->canPartiActivity($task, $this->mRequest['apiKey']);
        if (!$result) { return array(); }
        
        if ($this->isSendOver($task, $uuid)) { 
            $this->debug(array('msg' => "用户[{$uuid}]已经赠送过奖励了"));
            return array(); 
        }
        
        $sendGoods = $this->caculateGoodsInternal($task, $uuid);
        $this->info(array('msg' => "用户[{$uuid}]本次任务赠送的物品,任务ID[{$taskId}]"));
        return $sendGoods;
    }
    
    /**
     * 检查该用户是否已经赠送过奖励了
     * @param array $task   活动配置
     * @param string $uuid  玩家ID
     * @return boolean
     */
    private function isSendOver($task, $uuid)
    {
        $params = $this->onBuildQueryForSendOver($task, $uuid);
        $sendResult = Client_Service_TicketTrade::getBy($params);
        if($sendResult){
            return true;
        }
        return false;
    }
};
