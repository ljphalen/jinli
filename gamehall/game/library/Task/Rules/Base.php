<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务规则基类
 * @author fanch
 *
 */
abstract class Task_Rules_Base extends Util_LogForClass {
    protected  $mTaskRecord = array();
    protected  $mRequest = array();

    public function __construct() {
        parent::__construct("task.log", get_class($this));
    }
    /**
     *
     * @param array $taskRecord 数据库记录
     * @param array $request 活动请求参数
     */
    public function initial($taskRecord, $request){
        $this->mTaskRecord = $taskRecord;
        $this->mRequest = $request;
    }


    /**
     * 计算赠送的物品
     */
    abstract public function onCaculateGoods();

    /**
     * 用户对象过滤
     * @return boolean
     */
    protected function filterUserByUuid() {
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        if($task['hd_object'] == Client_Service_TaskHd::TARGET_USER_USER_ALL){
            return true;
        }
        else if ($task['hd_object'] == Client_Service_TaskHd::TARGET_USER_USER_BY_UUID) {
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
     * 老的计算规则
     */
    protected function caculateGoodsInternal(){
        $ruleConfig = json_decode($this->mTaskRecord['rule_content'], true);
        $acouponItem = $this->createAcouponItem(
            $ruleConfig['denomination'],
            Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY,
            $ruleConfig['deadline']
        );
        $sendGoods = array('Acoupon'=>array($acouponItem));
        return $sendGoods;
    }

    /**
     * 组装单条A券数据
     * @param $task
     * @param $uuid
     * @param $useApiKey
     * @param $gameId
     * @param $sendMoney
     * @param $startDay
     * @param $endDay
     * @param string $densection
     * @return array
     */
    protected function createAcouponItem($sendMoney, $startDay, $endDay, $densection = ''){
        $task = $this->mTaskRecord;
        $time = Common::gettime();
        $sectionTime = Common::getSectionTime($time, $startDay, $endDay);
        $useApiKey = $this->getUseApiKey($task);
        $gameId = $this->getGameId($task);
        $acouponItem = array(
            'aid'=> date('YmdHis') . uniqid(),
            'denomination' => $sendMoney,
            'startTime' => $sectionTime['start_time'],
            'endTime' => $sectionTime['end_time'],
            'consume_time' => $time,
            'desc' => $task['title'],
            'uuid' => $this->mRequest['uuid'],
            'send_type' => Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY,
            'sub_send_type' => $task['id'],
            'task_name' => $task['title'],
            'densection'=> $densection,
            'ticket_type' => $task['ticket_type'],
            'useApiKey' => $useApiKey,
            'game_id' => $gameId,
        );
        return $acouponItem;
    }

    /**
     * @param $task
     * @return mixed
     */
    private function getUseApiKey($task){
        if($task['ticket_type'] == Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER){
            if($task['ticket_object'] == Client_Service_Acoupon::TICKET_OBJECT_GAMEHALL){
                return $this->getGameHallApikey();
            } else {
                return $this->mRequest['apiKey'];
            }
        }
        return $this->getPaymentApiKey();
    }

    /**
     * 获取发送的游戏id
     * @param $task
     * @return int
     */
    private function getGameId($task){
        if($task['ticket_type'] == Client_Service_Acoupon::TICKET_TYPE_ACOUPON) {
            return 0;
        }
        if($task['ticket_object'] == Client_Service_Acoupon::TICKET_OBJECT_GAMEHALL){
            return Resource_Service_Games::getGameHallId();
        } else {
            $gameInfo = $this->getGameInfoByApiKey($this->mRequest['apikey']);
            $gameId = $gameInfo ? $gameInfo['id'] : 0;
            return $gameId;
        }
    }

    /**
     * 获取游戏券为全平台的apiKey
     * @return mixed
     */
    private function getGameHallApikey(){
        $config = Common::getConfig('paymentConfig', 'payment_send');
        return $config['api_key'];
    }

    /**
     * 获取A券支付使用apiKey
     * @return mixed
     */
    private function getPaymentApiKey(){
        $config = Common::getConfig('paymentConfig', 'payment_send');
        return $config['pay_api_key'];
    }

    /**
     * 检测登陆的游戏是否在活动任务范围内
     * @param array $task       任务配置
     * @param string $apiKey    游戏标识
     * @return boolean
     */
    protected function canPartiActivity() {
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
     * 获取游戏信息
     * @param string $apiKey 游戏ID
     * @return array
     */
    protected function getGameInfoByApiKey($apiKey){
        if ( !$apiKey ) { return false; }

        $game_params['api_key'] = $apiKey;
        $game_params['status'] = Resource_Service_Games::STATE_ONLINE;
        $gameInfo = Resource_Service_Games::getBy($game_params);
        return $gameInfo;
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
     * 根据金额计算赠送区间多区间适配
     * @param array  $task          表game_client_task_hd的任务配置
     * @param string $sectionValue         金额
     */
    private function matchSection($ruleConfig, $sectionValue){
        $section = array();

        /*
         * 查看当前消费金额是否超过最大的配置区间的结束金额，
        * 如果是直接取最后一个配置区间作为返还区间
        */
        $ruleMaxConfig = end($ruleConfig);
        if($sectionValue >= $ruleMaxConfig['section_end']){
            //返还区间取配置中最后一条区间的配置参数。
            $section = $ruleMaxConfig;
            return $section;
        }

        $findFirstSection = 0;
        foreach($ruleConfig as $item){
            //比较当前结束区间
            if ($sectionValue >= $item['section_end']) {
                continue;
            }
            //比较当前开始区间
            if($sectionValue >= $item['section_start']){
                $findFirstSection = 1;
            }
            //已找到用于赠送的返还区间
            if($findFirstSection == 1) {
                $section = $item;
                break;
            }
        }
        return $section;
    }

    private function getValidMoney($section, $money) {
        if($money >= $section['section_end']) { // 按产品要求，最大值采用的规则是"<=",而非"<"
            return $section['section_end'];
        }
        return  $money;
    }

    /**
     * 计算返利
     * @param array  $grantSection
     * @param string $money
     */
    private function calcPercentACoupon($grantSection, $money) {
        if (empty($grantSection)) {
            return 0;
        }
        return round($money * $grantSection['backPercent']) / 100;
    }

    /**
     * @param $uuid
     * @param $task
     * @param $grantSection
     */
    protected function calcQuotaAcoupon($grantSection){
        $grantData = $jsonData =  array();
        $jsonData  = json_encode(array(
            'section_start'=>$grantSection['section_start'],
            'section_end'=>$grantSection['section_end']
        ));
        foreach($grantSection['denarr'] as $item){
            $grantData[] = $this->createAcouponItem($item['Step'], $item['effect_start_time'], $item['effect_end_time'], $jsonData);
        }
        return $grantData;
    }

    /**
     * 返利
     * @param string $uuid
     * @param array  $task          表game_client_task_hd的任务配置
     * @param string $money         金额
     */
    protected function returnQuotaACoupon() {
        $task = $this->mTaskRecord;
        $money = $this->mRequest['money'];
        $uuid = $this->mRequest['uuid'];

        $ruleConfig = json_decode($task['rule_content'], true);
        $grantSection = $this->matchSection($ruleConfig, $money);
        $this->debug(array(
            'msg' => "计算用户[{$uuid}]返券区间, 任务ID[{$task['id']}], 金额[{$money}]",
            'grantSection' => json_encode($grantSection)
        ));

        if (empty($grantSection)) {
            return array();
        }

        //使用百分比赠送
        $sendACoupon = $this->calcQuotaAcoupon($grantSection);
        $this->debug(array(
            'msg' => "计算用户[{$uuid}]返券数据, 任务ID[{$task['id']}], 金额[{$money}]",
            'sendACoupon' => json_encode($sendACoupon)
        ));
        $sendGoods["Acoupon"] = $sendACoupon;
        return $sendGoods;
    }

    /**
     * vip多区间-百分比返还
     */
    protected function returnVipAcoupon(){
        $task = $this->mTaskRecord;
        $money = $this->mRequest['money'];
        $uuid = $this->mRequest['uuid'];
        $vipLevel = $this->getUserVip($uuid);
        $ruleConfig = json_decode($task['rule_content'], true);
        $grantSection = $this->matchSection($ruleConfig, $vipLevel);
        $this->debug(array(
            'msg' => "计算用户[{$uuid}]返券区间, 任务ID[{$task['id']}], 金额[{$money}]",
            'grantSection' => json_encode($grantSection)
        ));
        if (empty($grantSection)) {
            return array();
        }

        $sendACoupon = $this->calcPercentACoupon($grantSection, $money);
        $this->debug(array('msg' => "计算返券金额, 返还金额[{$sendACoupon}], 任务ID[{$task['id']}], 金额[{$money}]"));
        if ($sendACoupon <= 0) {
            return array();
        }
        //$densection描述数据
        $jsonData  = json_encode(array(
            'section_start' => $grantSection['section_start'],
            'section_end' => $grantSection['section_end']
        ));

        $sendGoods["Acoupon"][] = $this->createAcouponItem(
            $sendACoupon,
            $grantSection['effect_start_time'],
            $grantSection['effect_end_time'],
            $jsonData
        );
        return $sendGoods;
    }

    /**
     * 获取用户vip等级
     * @param $uuid
     * @return int
     */
    private function getUserVip($uuid){
        return Account_Service_User::getUserVipLevel($uuid);
    }

    /**
     * 多区间-百分比返利
     */
    protected function returnACoupon() {
        $task = $this->mTaskRecord;
        $money = $this->mRequest['money'];
        $ruleConfig = json_decode($task['rule_content'], true);
        $grantSection = $this->matchSection($ruleConfig, $money);
        $money = $this->getValidMoney($grantSection, $money);
        $sendACoupon = $this->calcPercentACoupon($grantSection, $money);

        $this->debug(array('msg' => "计算返券金额, 返还金额[{$sendACoupon}], 任务ID[{$task['id']}], 金额[{$money}]"));
        if ($sendACoupon <= 0) {
            return array();
        }
        //$densection描述数据
        $jsonData  = json_encode(array(
            'section_start' => $grantSection['section_start'],
            'section_end' => $grantSection['section_end']
        ));

        $sendGoods["Acoupon"][] = $this->createAcouponItem(
            $sendACoupon,
            $grantSection['effect_start_time'],
            $grantSection['effect_end_time'],
            $jsonData
        );
        return $sendGoods;
    }

    protected function returnQuotaAcouponForSum($total){
        $lastSection = $this->getLastSection();
        $grantSection = $this->getSendSection($total, $lastSection);

        $debugMsg = array(
            'msg' => "计算用户[{$this->mRequest['uuid']}]在本次任务中适用的返还百分比区间",
            'taskId' => $this->mTaskRecord['id'],
            'grantSection' => $grantSection
        );
        $this->debug($debugMsg);

        if(empty($grantSection)){
            return array();
        }

        $grantAcoupon = $this->getGrantAcouponBySection($grantSection);
        if(empty($grantAcoupon)){
            return array();
        }

        $sendGoods['Acoupon'] = $grantAcoupon;
        return $sendGoods;
    }


    /**
     * 查询当前活动进行期间用户消费上次使用的返还区间
     */
    private function getLastSection(){
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        $search['uuid'] = $uuid;
        $search['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $search['consume_time'][0] = array('>=', $task['hd_start_time']);
        $search['consume_time'][1] = array('<=', $task['hd_end_time']);
        $search['sub_send_type'] = $task['id'];

        $sentTicket = Client_Service_TicketTrade::getsBy($search, array('consume_time'=>'DESC'));
        $lastSection = 0;
        foreach ($sentTicket as $val){
            $sentSection = json_decode($val['densection'], true);
            if($lastSection < $sentSection['section_end']){
                $lastSection = $sentSection['section_end'];
            }
        }

        $debugMsg = array(
            'msg' => "计算用户[{$uuid}]本次任务活动进行期间消费已返还的开始区间",
            'taskId' => $task['id'],
            'uuid' => $uuid,
            'lastSection' => $lastSection
        );
        $this->debug($debugMsg);

        return $lastSection;
    }

    /**
     * 根据金额与上次最大的返还区间，计算本次赠送使用的返还区间
     *
     * @param string $money
     * @param string $lastSection
     */
    private function getSendSection($money, $lastSection){
        $section = array();
        $ruleConfig = json_decode($this->mTaskRecord['rule_content'], true);
        $findFirstSection = 0;
        foreach($ruleConfig as $key=>$value){
            //有赠送记录
            if($lastSection){
                //取出上一个赠送区间
                if ($lastSection >= $value['section_end']) {
                    continue;
                }
                if($lastSection >= $value['section_start']){
                    $findFirstSection = 1;
                }
                //没有赠送记录
            } else {
                $findFirstSection = 1;
            }
            //合并用于赠送的返还区间
            if($findFirstSection == 1 && $money >= $value['section_start']) {
                $section[] = $value;
            }
        }

        return $section;
    }

    /**
     * 根据返还区间计算返还的A券
     * @param string $grantSection
     * @return
     */
    private function getGrantAcouponBySection($grantSection){
        $grantData = $jsonData =  array();
        $jsonData  = json_encode(array(
            'section_start'=>$grantSection['section_start'],
            'section_end'=>$grantSection['section_end']
        ));
        foreach($grantSection['denarr'] as $item){
            $grantData[] = $this->createAcouponItem($item['Step'], $item['effect_start_time'], $item['effect_end_time'], $jsonData);
        }
        return $grantData;
    }

    /**
     * 百分比累计返利
     * @param string $uuid
     * @param string $oldSumMoney   之前的金额总值
     * @param string $oldSumMoney   当前的金额总值
     */
    protected function returnACouponForSum($oldSumMoney, $newSumMoney) {
        $ruleConfig = json_decode($this->mTaskRecord['rule_content'], true);

        $oldSection = $this->matchSection($ruleConfig, $oldSumMoney);
        $oldSumMoney = $this->getValidMoney($oldSection, $oldSumMoney);
        $oldSumACoupon = $this->calcPercentACoupon($oldSection, $oldSumMoney);

        $newSection = $this->matchSection($ruleConfig, $newSumMoney);
        $newSumMoney = $this->getValidMoney($newSection, $newSumMoney);
        $newSumACoupon = $this->calcPercentACoupon($newSection, $newSumMoney);

        $sendACoupon = $newSumACoupon - $oldSumACoupon;
        $this->debug(array('msg' => "计算返券金额, 返还金额[{$sendACoupon}], 任务ID[{$this->mTaskRecord['id']}],"
            . " 总金额[{$newSumMoney}], 之前的总金额[$oldSumMoney], 总返还金额[{$newSumACoupon}]"));

        if ($sendACoupon <= 0) {
            return array();
        }

        // $densection描述数据
        $jsonData  = json_encode(array(
            'section_start' => $newSection['section_start'],
            'section_end' => $newSection['section_end']
        ));

        // NOTE: effect_start_time、effect_end_time在每段配置中都是相同的，配置时已做了约束
        $sendGoods["Acoupon"][] = $this->createAcouponItem(
            $sendACoupon,
            $newSection['effect_start_time'],
            $newSection['effect_end_time'],
            $jsonData
        );
        return $sendGoods;
    }
}