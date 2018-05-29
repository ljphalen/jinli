<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务子类[返利活动]：登录客户端
 * @author fanch
 *
 */
class Task_Tasks_LoginClient extends Task_Tasks_Base{

    const USER_LOGIN_ONCE = 1;

    /**
     * 能否赠送
     * @return bool
     */
    protected function isSend(){
        $sendUser = $this->isSendOver();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}]是否参加过该任务",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $sendUser
        );
        $this->debug($debugMsg);
        if($sendUser) return false;

        $sendClient = $this->isSendClient();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}]请求的客户端是否允许参加该任务",
            'taskId' => $this->mTaskRecord['id'],
            'clientVersion' => $this->mRequest['clientVersion'],
            'result' => $sendClient
        );
        $this->debug($debugMsg);
        if(!$sendClient) return false;

        $sendUser = $this->isSendUser();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}是否允许参加该任务",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $sendUser
        );
        $this->debug($debugMsg);
        if(!$sendUser) return false;

        $firstLogin = $this->isFirstLogin();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}]是否是首次登录",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $firstLogin
        );
        $this->debug($debugMsg);
        if(!$firstLogin) return false;

        return true;
    }


    /**
     * 用户是否已赠送过-针对账号
     * @return bool
     */
    private function isSendOver(){
        $params = array();
        $params['uuid'] = $this->mRequest['uuid'];
        $params['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $params['sub_send_type'] = $this->mTaskRecord['id'];
        $sendResult = Client_Service_TicketTrade::getBy($params);
        if($sendResult){
            return true;
        }
        return false;
    }


    /**
     * 请求客户端是否可以参与该活动
     * @return bool
     */
    private function isSendClient(){
        $taskVersion = json_decode($this->mTaskRecord['game_version'], true);
        $clientVersion = $this->isClientVersion($taskVersion, $this->mRequest['clientVersion']);
        if(!$clientVersion){
            return false;
        }
        return true;
    }

    /**
     * 验证客户端版本
     * @param array   $taskVersion
     * @param string  $clientVersion
     * @return boolean
     */
    private function  isClientVersion($taskVersion, $clientVersion){
        if(!$taskVersion || !$clientVersion){
            return false;
        }

        if($taskVersion[1]){
            return true;
        }

        $clientVersion = Common::getClientVersion($clientVersion);
        $configVersion =  Common::getConfig('taskConfig', 'version');
        if($taskVersion[$configVersion[$clientVersion]]){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 首次登陆
     * @return bool
     * @throws Exception
     */
    private function isFirstLogin(){
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        $startTime = $task['hd_start_time'];
        $endTime = $task['hd_end_time'];
        $cache = Cache_Factory::getCache();
        $cacheKey = Util_CacheKey::getUserInfoKey($uuid) ; //获取用户的uuid
        $lastLoginTime = strtotime($cache->hGet($cacheKey, 'lastLoginTime' )); //最后登录时间
        $loginLogParams['create_time'][0] = array('>=', $startTime);
        $loginLogParams['create_time'][1] = array('<=', $endTime);
        $loginLogParams['uuid'] = $uuid;
        $everyLoginTotal = Account_Service_User::countUserLoginLog($loginLogParams);
        //判断该用户在活动期间是否是首次登陆
        if($lastLoginTime >= $startTime && $lastLoginTime <= $endTime && $everyLoginTotal == self::USER_LOGIN_ONCE){
            return true;
        }
        return false;
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
        return $msgTemplate[$good];
    }
}