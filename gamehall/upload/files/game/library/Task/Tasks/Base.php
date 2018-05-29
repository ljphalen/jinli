<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务基类
 * @author fanch
 *
 */
abstract class Task_Tasks_Base extends Util_LogForClass{

    protected $mRuleInstance = array();
    protected $mRequest = array();
    protected $mTaskRecord = array();
    private $mSendGoods = array();
    private $mSendResult = array();

    public function __construct($task, $request){
        parent::__construct("task.log", get_class($this));
        $this->mRequest = $request;
        $this->mTaskRecord  = $task;
        $this->initRuleInstance();
    }

    /**
     * 任务消息
     * @param $good
     * @param $data
     * @return mixed
     */
    abstract protected function onCreateMessage($good, $data);

    /**
     * 执行方法体
     * @return mixed
     */
     public function run(){
         $this->caculateGoods();
         $this->grantPrizes();
         $this->sendMessage();
     }

    /**
     * 计算赠送物品
     * @return array
     */
    protected function caculateGoods(){
        if(!$this->isSend()){ return; }
        $ruleObj = $this->mRuleInstance;
        $this->mSendGoods = $ruleObj->onCalcSendGoods();
    }

    /**
     * 是否满足赠送
     * @return bool
     */
    protected function isSend(){
        $sendUser = $this->isSendUser();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}是否允许参加该任务",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $sendUser
        );
        $this->debug($debugMsg);
        if(!$sendUser) return false;

        $sendGame = $this->isSendGame();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}登陆的游戏否允许参加该任务",
            'apiKey' => "游戏[{{$this->mRequest['apiKey']}]",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $sendGame
        );
        $this->debug($debugMsg);
        if(!$sendGame) return false;

        return true;
    }

    /**
     * 该用户是否满足赠送
     * @return bool
     */
    protected function isSendUser(){
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        if($task['hd_object'] == Client_Service_TaskHd::TARGET_USER_USER_ALL){
            return true;
        }else if ($task['hd_object'] == Client_Service_TaskHd::TARGET_USER_USER_BY_UUID) {
            $additionInfo = json_decode($task['hd_object_addition_info'], true);
            $userList = $additionInfo['user_list'];
            if (is_array($userList) && in_array($uuid, $userList)) {
                $this->debug(array('msg' => "用户[{$uuid}]在定向用户名单中",
                    'user_list' => $userList));
                return true;
            }

            $this->debug(array('msg' => "用户[{$uuid}]不在定向用户名单中",
                'user_list' => $userList));
            return false;
        }

        $this->err(array('msg' => "hd_object字段[{$task['hd_object']}]非期望值"));
        return false;
    }

    /**
     * 该游戏是否满足赠送
     * @return bool
     */
    protected function isSendGame(){
        $task = $this->mTaskRecord;
        $apiKey = $this->mRequest['apiKey'];
        $gameInfo = $this->getGameInfoByApiKey($apiKey);
        if (!$gameInfo) {
            $this->warning(array('msg' => "无效的apiKey[{$apiKey}]"));
            return false;
        } else {
            $this->debug(array('msg' => "查游戏信息，apiKey[{$apiKey}],".
                "游戏ID[{$gameInfo['id']}], 游戏名[{$gameInfo['name']}]"));
        }

        $gameObject = $task['game_object'];
        if (Client_Service_TaskHd::TARGET_GAME_GAME_ALL == $gameObject) {  //全部游戏
            return true;
        }

        $gameId = $gameInfo['id'];
        if (Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT == $gameObject) {  //专题游戏
            return $this->isGameIdInGameTopic($gameId, $task['subject_id'] );
        } elseif (Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST == $gameObject) {  // 定向游戏
            return $this->isGameIdInGameList($gameId, $task['game_object_addition_info'], "定向");
        } elseif (Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST == $gameObject) {  // 排除游戏
            return !$this->isGameIdInGameList($gameId, $task['game_object_addition_info'], "排除");
        }
        return false;
    }

    /**
     * 初始化规则对像
     */
    private function initRuleInstance() {
        $taskRecord = $this->mTaskRecord;
        $ruleClassName = $this->getRuleClassName();
        $ruleObj = new $ruleClassName($this->mTaskRecord, $this->mRequest);
        $this->mRuleInstance = $ruleObj;

        $debugMsg = array('msg' => "初始化规则对象",
            'taskRecord'=>$taskRecord,
            'request' => $this->mRequest);
        $this->debug($debugMsg);
    }

    /**
     * 发放类型是否是游戏券
     * @return bool
     */
    protected function isGameVoucher(){
        $task = $this->mTaskRecord;
        if($task['ticket_type'] != Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER){
            return false;
        }
        return true;
    }

    /**
     * 获取任务规则对象类名
     */
    private function getRuleClassName(){
        $taskClassName = get_class($this);
        $ruleConfig = Common::getConfig('taskConfig', 'rule');
        return $ruleConfig[$taskClassName];
    }

    /**
     * 赠送符合任务规则的对应物品
     * @param array $task
     * @param array $goods
     */
    private function grantPrizes(){
        $debugMsg = array('msg' => '赠送物品',
            'goods' => ($this->mSendGoods) ? $this->mSendGoods : '无');
        $this->debug($debugMsg);

        if(empty($this->mSendGoods)){ return; }

        foreach ($this->mSendGoods as $good => $data){
            $goodClassName = 'Task_Goods_' . $good;
            $goodObj = new $goodClassName($data);
            $this->mSendResult[$good] = array(
                'result' => $goodObj->onSend(),
                'data' => $data
            );
        }

        $debugMsg = array('msg' => '获取赠送结果',
            'sendResult' => $this->mSendResult);
        $this->debug($debugMsg);

    }

    /**
     * 发送赠送后的消息
     */
    private function sendMessage(){
        foreach ($this->mSendResult as $good => $item){
            if($item['result']){
                foreach ($item['data'] as $data){
                    $message = $this->onCreateMessage($good, $data);
                    $this->saveMessage($good, $message);

                    $debugMsg = array('msg' => '保存赠送物品的消息',
                        'good' => $good,
                        'message' => $message);
                    $this->debug($debugMsg);
                }
            }
        }
    }

    /**
     * 发送消息赠送消息到消息缓存
     * @param string $good
     * @param array $message
     */
    private function saveMessage($good, $message){
        $type = $this->getMessgeType($good);
        $switch = Game_Service_Msg::isNewMsgEnabled($type);
        if($switch) {
            Common::getQueue()->push('game_client_msg',$message);
        }
    }

    /**
     * 根据物品获取消息标识数字
     * @param string $good
     * @return string
     */
    private function getMessgeType($good){
        $messageConfig = Common::getConfig('taskConfig', 'message');
        return $messageConfig[$good];
    }

    /**
     * 验证游戏是否在专题中
     * @param string $apiKey    游戏表示
     * @param string $subjectId 专题游戏
     * @return boolean
     */
    private function isGameIdInGameTopic($gameId, $subjectId){
        $params['subject_id']  = $subjectId;
        $params['game_status'] = Resource_Service_Games::STATE_ONLINE;
        list(, $subjectGames) = Client_Service_Game::getSubjectBySubjectId($params);
        $subjectGames = Common::resetKey($subjectGames, 'resource_game_id');
        $resourceGameIds = array_unique(array_keys($subjectGames));

        $this->debug(array('msg' => "判断游戏[{$gameId}]是否在专题[{$subjectId}]中",
            'resource_game_ids' => $resourceGameIds));

        if(in_array($gameId, $resourceGameIds)){
            return true;
        }
        return false;
    }

    /**
     * 验证游戏是否在游戏列表中
     * @param string $apiKey    游戏表示
     * @param string $subjectId 专题游戏
     * @return boolean
     */
    private function isGameIdInGameList($gameId, $additionInfo, $info="定向") {
        $additionInfo = json_decode($additionInfo, true);
        $gameList = $additionInfo['game_list'];
        if (is_array($gameList) && in_array($gameId, $gameList)) {
            $this->debug(array('msg' => "游戏[{$gameId}]在{$info}游戏名单中",
                'user_list' => $gameList));
            return true;
        }

        $this->debug(array('msg' => "游戏[{$gameId}]不在{$info}游戏名单中",
            'user_list' => $gameList));
        return false;
    }

    /**
     * 获取游戏信息
     * @param string $apiKey 游戏ID
     * @return array
     */
    private function getGameInfoByApiKey($apiKey){
        if ( !$apiKey ) { return false; }

        $game_params['api_key'] = $apiKey;
        $game_params['status'] = Resource_Service_Games::STATE_ONLINE;
        $gameInfo = Resource_Service_Games::getBy($game_params);
        return $gameInfo;
    }
}
