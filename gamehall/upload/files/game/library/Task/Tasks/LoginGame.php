<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务子类[返利活动]：登录游戏
 * @author fanch
 *
 */
class Task_Tasks_LoginGame extends Task_Tasks_Base{

    /**
     * 能否满足赠送
     * @return bool
     */
    protected function isSend(){
        $sendCondition = $this->isSendCondition();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}]是否满足登陆游戏中赠送条件",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $sendCondition
        );
        $this->debug($debugMsg);
        if(!$sendCondition) return false;
       return parent::isSend();
    }

    /**
     * 登陆游戏-中具体的赠送条件判断
     * @return array
     */
    private function isSendCondition(){
        $task = $this->mTaskRecord;
        $type = $task['condition_type'];
        $params = array();
        switch($type){
            case Client_Service_TaskHd::RULES_LOGIN_GAME_FIRST:
                $params = $this->isFirst();
                break;
            case Client_Service_TaskHd::RULES_LOGIN_GAME_DAILY:
                $params = $this->isDaily();
                break;
        }
        $sendResult = Client_Service_TicketTrade::getBy($params);
        if($sendResult){
            return false;
        }
        return true;
    }

    /**
     * 每天-针对用户
     * @return array
     */
    private function isDaily(){
        $params = array();
        $params['uuid'] = $this->mRequest['uuid'];
        $params['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $params['sub_send_type'] = $this->mTaskRecord['id'];
        $currentTime = date('Y-m-d',Common::getTime());
        $startTime = strtotime($currentTime.' 00:00:00');
        $endTime = strtotime($currentTime.' 23:59:59');
        $params['consume_time'][0] = array('>=', $startTime);
        $params['consume_time'][1] = array('<=', $endTime);
        return $params;
    }

    /**
     * 首次登陆-针对用户
     * @return array
     */
    private function isFirst(){
        $params = array();
        $params['uuid'] = $this->mRequest['uuid'];
        $params['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $params['sub_send_type'] = $this->mTaskRecord['id'];
        return $params;
    }

    /**
     * 消息模板
     */
    protected function onCreateMessage($good, $data){
        $time = Common::getTime();
        $data['denomination'] = number_format($data['denomination'], 2, '.', '');
        $tips = $this->isGameVoucher() ? '游戏券' : 'A券';
        $msgTemplate = array(
            'Acoupon'=>array(
                'type' =>  103,
                'top_type' =>  100,
                'totype' =>  1,
                'status' =>  0,
                'start_time' =>  $time,
                'end_time' =>  strtotime('2050-01-01 23:59:59'),
                'title' => '您获得'. $data['denomination'] . $tips,
                'msg' => '恭喜，您已完成' . $data['task_name'] . '活动，获得' . $data['denomination'] . $tips . '奖励！请在有效期内使用！',
                'sendInput' => $data['uuid']
            ),
        );
        $this->saveNewTicket($data['denomination'], $data['uuid']);
        return $msgTemplate[$good];
    }

    private function saveNewTicket($ticket, $uuid) {
        $ticketValues = array();
        $ticketValues[] = $ticket;
        $cache = Cache_Factory::getCache();
        if ($cache->get(Util_CacheKey::SDK_TICKET_LOGIN.$uuid)) {
            $oldTicket = json_decode($cache->hGet(Util_CacheKey::SDK_TICKET_LOGIN, $uuid), true);
            $ticketValues = array_merge($oldTicket, $ticketValues);
        }
        $cache->set(Util_CacheKey::SDK_TICKET_LOGIN.$uuid, true, 2);
        $cache->hSet(Util_CacheKey::SDK_TICKET_LOGIN, $uuid, json_encode($ticketValues), 300);
    }
}