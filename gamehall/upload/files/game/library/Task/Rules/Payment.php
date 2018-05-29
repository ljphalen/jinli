<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-规则类-充值规则
 * @author fanch
 *
 */
class Task_Rules_Payment extends Task_Rules_Base{

    public function onCalcSendGoods(){
        return $this->calcSendRule();
    }

    private function calcSendRule(){
        $task = $this->mTaskRecord;
        $ruleType =$task['condition_type'];
        switch($ruleType) {
            case 1: //每天首次充值（单区间-百分比）
                return $this->calcSingle();
                break;
            case 2: //每天首次充值（多区间-百分比）
                return $this->calcDaily(self::CALC_PERCENT);
                break;
            case 5: //每天首次充值（多区间-固定额）
                return $this->calcDaily(self::CALC_QUOTA);
                break;
            case 3: //单次充值（多区间-百分比）
                return $this->calcOnce(self::CALC_PERCENT);
                break;
            case 6: //单次充值（多区间-固定额）
                return $this->calcOnce(self::CALC_QUOTA);
                break;
            case 4: //累计充值（多区间-百分比）
                return $this->calcSumPercent();
                break;
            case 7: //累计充值（多区间-固定额）
                return $this->calcSumFixed();
                break;
        }
    }

    /**
     * 充值判断是否每天首次付费
     * @param array $task
     * @param string $uuid
     * @return boolean
     */
    protected function isFirst() {
        $taskStartTime = $this->mTaskRecord['hd_start_time'];
        $startDay = date('Y-m-d', $taskStartTime);
        $taskEndTime = $this->mTaskRecord['hd_end_time'];
        $endDay = date('Y-m-d', $taskEndTime);
        $currentDay = date('Y-m-d', Common::getTime());
        if($startDay == $currentDay){
            $startTime = $taskStartTime;
        }else{
            $startTime = strtotime($currentDay . ' 00:00:00');
        }

        if($endDay == $currentDay){
            $endTime = $taskEndTime;
        } else {
            $endTime = strtotime($currentDay . ' 23:59:59');
        }

        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_PAYMENT; // 2是充值
        $search['trade_time'][0] = array('>=', $startTime);
        $search['trade_time'][1] = array('<=', $endTime);
        $search['uuid'] = $this->mRequest['uuid'];

        $result = Client_Service_MoneyTrade::getsBy($search);
        $count = count($result);
        if (1 == $count) {
            return true;
        }
        $this->debug(array('msg' => "每日充值返利,用户[{$this->mRequest['uuid']}]已经充值过了,总次数[{$count}],任务ID[{$this->mTaskRecord['id']}]"));
        return false;
    }

    /**
     * 每天首次充值条件
     * @return bool
     */
    protected function isDaily(){
        return $this->isFirst();
    }

    /**
     * 区分游戏累计，用于多区间固定额返还
     * @return bool
     */
    protected function isDistGameSum(){
        $sumType = $this->mTaskRecord['condition_value'];
        if($sumType == Client_Service_Acoupon::PAYMENT_SINGLE_GAME){
            return true;
        }
        return false;
    }

    /**
     * 统计一段时间内的充值总金额，
     * 1.6.1 增加[全平台|单游戏]累计计算方式
     * @return string  总金额
     */
    protected function getSumMoney(){
        $startTime = $this->mTaskRecord['hd_start_time'];
        $endTime = $this->mTaskRecord['hd_end_time'];
        $search['event'] = Client_Service_MoneyTrade::TRADE_EVENT_PAYMENT; // 2是充值
        $search['trade_time'][0] = array('>=', $startTime);
        $search['trade_time'][1] = array('<=', $endTime);
        $search['uuid'] = $this->mRequest['uuid'];
        $distGame = $this->isDistGameSum();
        if($distGame){
            $search['api_key'] = $this->mRequest['apiKey'];
        } else {
            $gamesParams = $this->getCalcGamesParams();
            if($gamesParams){
                $search['api_key'] = $gamesParams;
            }
        }

        $total = Client_Service_MoneyTrade::getCount($search);
        $this->debug(array('msg' => "用户[{$this->mRequest['uuid']}]在{$startTime} 至{$endTime} 期间的充值总金额为{$total}"));
        return $total;
    }

    /**
     * 根据参与游戏的情况获取计算的游戏范围
     */
    private function getCalcGamesParams(){
        $task = $this->mTaskRecord;
        $gameObject = $task['game_object'];
        if (Client_Service_TaskHd::TARGET_GAME_GAME_ALL == $gameObject) {  //全部游戏
            return array();
        }

        if (Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT == $gameObject) {  //专题游戏
            $params['subject_id']  = $task['subject_id'];
            $params['game_status'] = Resource_Service_Games::STATE_ONLINE;
            list(, $subjectGames) = Client_Service_Game::getSubjectBySubjectId($params);
            $subjectGames = Common::resetKey($subjectGames, 'resource_game_id');
            $gameIds = array_unique(array_keys($subjectGames));
            $apiKeys = $this->getGamesApiKey($gameIds);
            return array('IN', $apiKeys);
        } elseif (Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST == $gameObject) {  // 定向游戏
            $additionInfo = json_decode($task['game_object_addition_info'], true);
            $apiKeys = $this->getGamesApiKey($additionInfo['game_list']);
            return array('IN', $apiKeys);
        } elseif (Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST == $gameObject) {  // 排除游戏
            $additionInfo = json_decode($task['game_object_addition_info'], true);
            $apiKeys = $this->getGamesApiKey($additionInfo['game_list']);
            return array('NOT IN', $apiKeys);
        }
    }

    /**
     * 根据游戏ids获取apikeys
     * @param $gameIds
     * @return array
     */
    private function getGamesApiKey($gameIds){
        $params = array(
            'id' => array('IN', $gameIds),
            'status'=>Resource_Service_Games::STATE_ONLINE
        );
        $gamesData = Resource_Service_Games::getsBy($params);
        $gamesData = Common::resetKey($gamesData, 'api_key');
        $gameApiKeys = array_unique(array_keys($gamesData));
        return $gameApiKeys;
    }
}