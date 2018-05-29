<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务规则类
 * @author fanch
 *
 */
abstract class Task_Rules_Base extends Util_LogForClass {
    const CALC_PERCENT = 1;
    const CALC_QUOTA = 2;
    protected  $mTaskRecord = array();
    protected  $mRequest = array();

    /**
     *
     * @param string $task
     * @param string $request
     */
    public function __construct($taskRecord, $request) {
        parent::__construct("task.log", get_class($this));
        $this->mTaskRecord = $taskRecord;
        $this->mRequest= $request;
    }

    abstract public function onCalcSendGoods();

    /**
     * 首次条件
     * @return bool
     */
    protected function isFirst(){
        return true;
    }

    /**
     * 每天首次
     */
    protected function isDaily(){
        return true;
    }

    /**
     * 获取累计金额
     * @return int
     */
    protected function getSumMoney(){
        return 0;
    }

    /**
     *区分游戏累计，用于多区间固定额返还
     */
    protected function isDistGameSum(){
        return true;
    }

    /**
     * 单区间百分比规则的计算
     * 1 消费返利-单区间百分比 condition_type:2
     * 2 充值返利-单区间百分比 condition_type:1
     */
    protected function calcSingle(){
        $isFirst = $this->isFirst();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}]任务赠送条件首次条件",
            'taskId' => $this->mTaskRecord['id'],
            'result' => $isFirst
        );
        $this->debug($debugMsg);
        if(!$isFirst) return array();

        $ruleConfig = json_decode($this->mTaskRecord['rule_content'], true);
        $money = $this->mRequest['money'];
        if($money < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        if($money < $ruleConfig['denomination']){
            return array();
        }
        $sendMoney  = ($money * $ruleConfig['restoration'])/100;
        $sendMoney = number_format($sendMoney, 2, '.', '');
        $debugMsg = array(
            'msg' => "计算用户[{$this->mRequest['uuid']}]本次任务返还的金额",
            'taskId' => $this->mTaskRecord['id'],
            'sendMoney' => $sendMoney
        );
        $this->debug($debugMsg);

        if($sendMoney < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        //根据配置组装A券数据
        $sendGoods["Acoupon"][]  =$this->createAcouponItem($sendMoney, Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY, $ruleConfig['deadline']);
        return $sendGoods;
    }

    /**
     * 累计多区间-固定额
     * @param $moneyTotal
     */
    protected function calcSumFixed(){
        $moneyTotal = $this->getSumMoney();
        $debugMsg = array(
            'msg' => "用户[{$this->mRequest['uuid']}]任务累计的金额",
            'taskId' => $this->mTaskRecord['id'],
            'moneyTotal' => $moneyTotal
        );
        $this->debug($debugMsg);

        if($moneyTotal < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        $distGame = $this->isDistGameSum();
        $goods = $this->returnQuotaAcouponForSum($moneyTotal, $distGame);
        return $goods;
    }

    /**
     * 多区间单次
     * @param $ruleType
     * @return array
     */
    protected function calcOnce($ruleType){
        if($this->mRequest['money'] < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }
        $sendGoods = array();
        if($ruleType == self::CALC_PERCENT){
            $sendGoods = $this->returnACoupon();
        } else if($ruleType == self::CALC_QUOTA){
            $sendGoods = $this->returnQuotaACoupon();
        }
        return $sendGoods;
    }

    /**
     * 多区间每天单次
     * @param $ruleType
     * @return array
     */
    protected function calcDaily($ruleType){
        if($this->mRequest['money'] < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        $isDaily = $this->isDaily();

        $debugMsg = array(
            'msg' => "判断用户[{$this->mRequest['uuid']}]是否满足该任务的每日首次的赠送条件",
            'taskId' => $this->mTaskRecord['id'],
            'filterResult' => $isDaily
        );
        $this->debug($debugMsg);
        if(!$isDaily){
            return array();
        }

        $sendGoods = array();
        if($ruleType == self::CALC_PERCENT){
            $sendGoods = $this->returnACoupon();
        } else if($ruleType == self::CALC_QUOTA){
            $sendGoods = $this->returnQuotaACoupon();
        }
        return $sendGoods;
    }

    /**
     * 累计多区间-百分比
     * @param $moneyTotal
     */
    protected function calcSumPercent(){
        $money = $this->mRequest['money'];
        $consumeTotal = $this->getSumMoney();
        if($consumeTotal < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        //根据总额计算本次赠送的A券
        $goods = $this->returnACouponForSum($consumeTotal - $money, $consumeTotal);
        return $goods;
    }

    /**
     * VIP多区间-百分比
     * @return array
     */
    protected function calcVip(){
        if($this->mRequest['money'] < Client_Service_TaskHd::MIN_AMOUNT){
            return array();
        }

        $sendGoods = $this->returnVipAcoupon();
        return $sendGoods;
    }

    /**
     * 百分比累计返利
     * @param string $uuid
     * @param string $oldSumMoney   之前的金额总值 200
     * @param string $newSumMoney   当前的金额总值 294.03
     */
    private function returnACouponForSum($oldSumMoney, $newSumMoney) {
        $ruleConfig = json_decode($this->mTaskRecord['rule_content'], true);
        $oldSection = $this->matchSection($ruleConfig, $oldSumMoney);
        $oldSumMoney = $this->getValidMoney($oldSection, $oldSumMoney);
        $oldSumACoupon = $this->calcPercentACoupon($oldSection, $oldSumMoney);

        $newSection = $this->matchSection($ruleConfig, $newSumMoney);
        $newSumMoney = $this->getValidMoney($newSection, $newSumMoney);
        $newSumACoupon = $this->calcPercentACoupon($newSection, $newSumMoney);

        $this->debug(array(
            'msg' => '跟踪计算数据',
            'uuid' => $this->mRequest['uuid'],
            'task' => $this->mTaskRecord['id'],
            'oldSection' => $oldSection,
            'oldSumMoney' =>$oldSumMoney,
            'oldSumACoupon' => $oldSumACoupon,
            'newSection' =>$newSection,
            'newSumMoney' =>$newSumMoney,
            'newSumACoupon' => $newSumACoupon
        ));

        $sendACoupon = $newSumACoupon - $oldSumACoupon;
        $this->debug(array('msg' => "计算用户[{$this->mRequest['uuid']}]返券金额, 返还金额[{$sendACoupon}], 任务ID[{$this->mTaskRecord['id']}],"
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

    /**
     * 多区间累计固定额计算
     * @param $total
     * @return array
     */
    private function returnQuotaAcouponForSum($total, $distGame){
        $lastSection = $this->getLastSection($distGame);
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
     * 多区间-百分比返利
     */
    private function returnACoupon() {
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

    /**
     * vip 消费返利
     * @return array
     */
    private function returnVipAcoupon(){
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
     * 多多区间-固定额返利
     * @param string $uuid
     * @param array  $task
     * @param string $money
     */
    private function returnQuotaACoupon() {
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
     * @param $uuid
     * @param $task
     * @param $grantSection
     */
    private function calcQuotaAcoupon($grantSection){
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
        $sendGameId = $this->sendGameId();
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
            'send_game_id' => $sendGameId,
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
     * 获取游戏券中的的游戏id
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
            $gameInfo = $this->getGameInfoByApiKey($this->mRequest['apiKey']);
            $gameId = $gameInfo ? $gameInfo['id'] : 0;
            return $gameId;
        }
    }

    /**
     * 针对游戏获取A券或游戏券应用的游戏Id
     * @return int
     */
    private function sendGameId(){
        $task = $this->mTaskRecord;
        //消费返利-消费全部针对单个游戏
        if($task['htype'] == 3){
            $apiKey = $this->mRequest['apiKey'];
            $gameInfo = $this->getGameInfoByApiKey($apiKey);
            return $gameInfo['id'];
        }
        //充值返利-区分不同条件下赠送的游戏id
        if($task['htype'] == 4){
            //赠送条件：累计多区间-固定额
            $conditionFlag = ($task['condition_type'] == 7);
            //累计计算规则：单游戏累计
            $sumFlag = ($task['condition_value'] == Client_Service_Acoupon::PAYMENT_SINGLE_GAME);
            if($conditionFlag && $sumFlag){
                $apiKey = $this->mRequest['apiKey'];
                $gameInfo = $this->getGameInfoByApiKey($apiKey);
                return $gameInfo['id'];
            }else{
                return $this->getGameId($task);
            }
        }
        return $this->getGameId($task);
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
     * 根据金额计算赠送区间多区间适配
     * @param array  $task          表game_client_task_hd的任务配置
     * @param string $sectionValue         金额
     */
    private function matchSection($ruleConfig, $sectionValue){
        $sectionValue = intval(round($sectionValue));
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
        foreach($ruleConfig as $key => $item){
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
        $money = intval(round($money));
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
        $sendMoney = round($money * $grantSection['backPercent']) / 100;
        return round($sendMoney, 2);
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
     * 查询当前活动进行期间用户消费上次使用的返还区间
     * v1.6.1 增加返还游戏区分
     */
    private function getLastSection($distGame){
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
        $search['uuid'] = $uuid;
        $search['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
        $search['consume_time'][0] = array('>=', $task['hd_start_time']);
        $search['consume_time'][1] = array('<=', $task['hd_end_time']);
        $search['sub_send_type'] = $task['id'];
        if($distGame) {
            $gameInfo = $this->getGameInfoByApiKey($this->mRequest['apiKey']);
            $gameId = $gameInfo ? $gameInfo['id'] : 0;
            $search['send_game_id'] = $gameId;
        }

        $sentTicket = Client_Service_TicketTrade::getsBy($search, array('consume_time'=>'DESC'));
        $debugMsg = array(
            'msg' => "计算用户[{$uuid}]本次任务活动进行期间消费已返还的参数",
            'taskId' => $task['id'],
            'uuid' => $uuid,
            'search' => $search,
            'sentTicket' => $sentTicket
        );
        $this->debug($debugMsg);

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
        foreach ($grantSection as $section){
            $jsonData  = json_encode(array(
                'section_start'=>$section['section_start'],
                'section_end'=>$section['section_end']
            ));
            foreach($section['denarr'] as $item){
                $grantData[] = $this->createAcouponItem($item['Step'], $item['effect_start_time'], $item['effect_end_time'], $jsonData);
            }
        }
        return $grantData;
    }
}