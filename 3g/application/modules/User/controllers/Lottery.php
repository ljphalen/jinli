<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LotteryController extends User_BaseController {
    public function indexAction() {
        Gionee_Service_Log::pvLog('user_lottery_index');
        Gionee_Service_Log::uvLog('user_lottery_index', $this->getSource());
        $dataList = $this->_getLotteryGoods();
        $config   = $this->_getConfig();
        $scoreMsg = array();
        $uid      = $levelRewardTimes = $experienceLevel = 0;
        $userInfo = Gionee_Service_User::getCurUserInfo();
        if (!empty($userInfo)) {
            $userScoreInfo   = User_Service_Gather::getInfoByUid($userInfo['id']);
            $uid             = $userInfo['id'];
            $experienceLevel = $userInfo['experience_level'];
        }

        $arrTimes         = $this->_getDrawTimes($uid);
        $cosumedTimes     = array_sum($arrTimes);
        $levelRewardTimes = User_Service_ExperienceInfo::getLevelRewardsData($userInfo['experience_level'], 2); //经验等级赚送的抽奖次数
        $remainedTimes    = $config['ulottery_max_times'] + $config['ulottery_free_times'] + $levelRewardTimes - $cosumedTimes;
        $this->assign('remainedTimes', $remainedTimes);
        $this->assign('levelRewardTimes', $levelRewardTimes);
        $this->assign('experienceLevel', $experienceLevel);
        $this->assign('scoreMsg', $userScoreInfo);
        $this->assign('list', array_chunk($dataList, 4));
        $this->assign('config', $config);
    }

    /**
     * ajax 请求
     */

    public function ajaxDrawingAction() {
        //登陆检测
        $login = Common_Service_User::checkLogin('/user/lottery/index');
        if (!$login['key']) {
            $this->output('0', '', array('redirect' => $login['keyMain']));
        }
        $userInfo = Gionee_Service_User::getCurUserInfo();
        $url      = Common_Service_User::validUsername($userInfo['username'], '/user/lottery/index');
        if (!empty($url)) {
            $this->output('0', '', array('redirect' => $url));
        }
        $config = $this->_getConfig();
        if (!$config['ulottery_status']) {
            $this->output('-1', '', array(
                'prize_msg'        => '对不起，该活动已关闭！',
                'prize_error_code' => -1
            ));
        }

        Gionee_Service_Log::pvLog('user_lottery_drawing');
        //状态检测
        $levelRewardTimes = User_Service_ExperienceInfo::getLevelRewardsData($userInfo['experience_level'], 2);
        $totalGiveTimes   = $levelRewardTimes + $config['ulottery_free_times'];
        $totalTimes       = $config['ulottery_max_times'] + $totalGiveTimes;
        $vaildMsg         = $this->_check($userInfo['id'], $totalTimes, $config['ulottery_per_cosume']);
        if (!$vaildMsg['key']) {
            $callback = array(
                'prize_msg'        => $vaildMsg['keyMain'],
                'prize_error_code' => $vaildMsg['data']['prize_error_code'],
                'prize_link'       => $vaildMsg['data']['prize_link'],
            );
            $this->output('-1', '', $callback);
        }
        Gionee_Service_Log::uvLog('user_lottery_drawing', $this->getSource());
        //抽奖
        //Common_Service_Base::beginTransaction();//事务开始
        //扣除用户消耗的金币
        if ($vaildMsg['data']['free_times'] < $totalGiveTimes) {
            $costs     = 0;
            $scoreType = '307';
        } else {
            $costs     = $config['ulottery_per_cosume'];
            $scoreType = '303';
        }
        $result = User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], -$costs, $scoreType);
        if ($result) {
            $goods = self::_getLotteryGoods();
            $data  = $geted = $tmp = array();
            foreach ($goods as $key => $val) {
                $data[$val['id']] = $val['ratio'];
                $tmp[$val['id']]  = $val;
            }
            $n     = Common_Service_User::getRangeData($data);
            $geted = $tmp[$n];
            User_Service_Lottery::edit(array('number' => $geted['number'] - 1), $n);//数量减少
        }
        $remainedScores = $this->_handlePrize($userInfo['id'], $n);
        if ($remainedScores >= 0) {
            //Common_Service_Base::commit();
            $reminedTimes = $totalTimes - $vaildMsg['data']['prize_draw_times'] - 1;
            $callback     = array(
                'prize_hit'   => $geted['id'],
                'score'       => $remainedScores,
                'prize_num'   => $reminedTimes,
                'prize_msg'   => "恭喜您，已抽中{$geted['name']}!",
                'prize_link'  => '/user/index/index',
                'prize_costs' => $costs
            );
            $this->output('0', '', $callback);
        } else {
            //Common_Service_Base::rollBack();
            $this->output('-1', '操作失败!');
        }
    }

    /**
     * 中奖处理
     */
    private function _handlePrize($uid, $gid = 0) {
        $goods = User_Service_Lottery::get($gid);
        if (empty($goods)) return false;
        $ret = 0;
        switch ($goods['type']) { //奖品类型,1送金币，2送话费，3，购物券
            case 1: {
                $scores = $goods['val'];//赠送的金币数
                $ret    = User_Service_Gather::changeScoresAndWriteLog($uid, $scores, '203', 2, $gid); //赠送相应积分
                break;
            }
            case 2: {
                break;
            }
            case 3: {
                break;
            }
            default:
                break;
        }

        return $ret;
    }

    /**
     * 得到奖品信息
     */

    private function _getLotteryGoods() {
        $key      = "USER:LOTTERY:LIST";
        $rs       = Common::getCache();
        $dataList = $rs->get($key);
        if (empty($dataList)) {
            list($total, $dataList) = User_Service_Lottery::getlist(1, 8, array('status' => 1), array(
                'sort'         => 'ASC',
                'created_time' => 'DESC'
            ));
            $rs->set($key, $dataList, 60);
        }
        return $dataList;
    }

    /**
     * 获得基本配置参数信息
     */
    private function _getConfig() {
        $key        = "USER:LOTTERY:CONFIG:DATA:";
        $rs         = Common::getCache();
        $configData = $rs->get($key);
        if (empty($configData)) {
            $param           = array();
            $param['3g_key'] = array(
                'IN',
                array(
                    'ulottery_status',
                    'ulottery_per_cosume',
                    'ulottery_max_times',
                    'ulottery_rule_content',
                    'ulottery_free_times'
                )
            );
            $data            = Gionee_Service_Config::getsBy($param);
            foreach ($data as $k => $v) {
                $configData[$v['3g_key']] = $v['3g_value'];
            }
            $rs->set($key, $configData, 60);
        }
        return $configData;
    }

    /**
     * 参数检测
     *
     * @param int $uid
     * @param int $times
     * @param int $scores
     */
    private function _check($uid = 0, $times, $scores) {
        if (!intval($uid) || !intval($times) || !intval($scores)) return array('key' => 0, 'keyMain' => '参数有错！');
        $userScores = User_Service_Gather::getInfoByUid($uid);
        if ($userScores['remained_score'] < $scores) {
            return array(
                'key'     => 0,
                'keyMain' => '您的金币不足!',
                'data'    => array('prize_error_code' => -2, 'prize_link' => '/user/index/index')
            );
        }
        $arrCostTimes = self::_getDrawTimes($uid);
        $totalTimes   = array_sum($arrCostTimes);
        if ($totalTimes >= $times) {
            return array(
                'key'     => 0,
                'keyMain' => '今日抽奖次数已用完！',
                'data'    => array('prize_error_code' => -3, 'prize_link' => '')
            );
        }

        return array(
            'key'     => 1,
            'keyMain' => 'Success！',
            'data'    => array(
                'prize_draw_times' => $totalTimes,
                'free_times'       => $arrCostTimes['307'],
                'cost_times'       => $arrCostTimes['303'],
                'prize_error_code' => 0,
                'prize_link'       => ''
            )
        );
    }


    private function _getDrawTimes($uid = 0) {
        $times = array();
        if ($uid > 0) {
            $params               = array();
            $params['uid']        = $uid;
            $params['score_type'] = array('IN', array('303', '307'));
            $params['group_id']   = 3;
            $params['date']       = date('Ymd', time());
            $times                = User_Service_ScoreLog::countBy($params, array('score_type'));
        }
        return $times;
    }

}

