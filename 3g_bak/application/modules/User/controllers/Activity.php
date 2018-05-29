<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends User_BaseController {

    /**
     * 刮刮乐
     */

    public $quizSerial = array(
        '1' => 'A',
        '2' => 'B',
        '3' => 'C',
        '4' => 'D'
    );
    public $quizStatus = array(
        '-1' => 'error',
        '0'  => '',
        '1'  => 'success',
    );

    public function lotAction() {
        //检测登陆
        $redirectUrl = '/user/activity/lot';
        $msg         = Common_Service_User::checkLogin($redirectUrl, true);

        $t_bi     = $this->getSource();
        $userInfo = $msg['keyMain'];
        Gionee_Service_Log::pvLog('user_activity_lot');
        Gionee_Service_Log::uvLog('user_activity_lot', $t_bi);
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        //$userScoreInfo =User_Service_Gather::getBy(array('uid'=>$userInfo['id']));//是否已成生随机碼
        if (empty($userScoreInfo['scratch_num'])) {
            $key = Gionee_Service_ShortUrl::genTVal($userInfo['id'] . $redirectUrl . Common::$urlPwd);
            $key = substr(strtoupper($key), 0, 6);
            $rs  = User_Service_Gather::update(array('scratch_num' => $key), $userScoreInfo['id']);
            User_Service_Gather::getInfoByUid($userInfo['id'], true);
            $data = array('code' => $key, 'status' => 0);//生成随机码
        } else {
            $data = array('code' => $userScoreInfo['scratch_num'], 'status' => $userScoreInfo['is_scratch']);
        }
        $config = Gionee_Service_Config::getValue('weixin_focus_config');
        $this->assign('config', json_decode($config));
        $this->assign('data', $data);
    }

    //更新状态
    public function scratchingAction() {
        $redirectUrl = '/user/activity/lot';
        $logMsg      = Common_Service_User::checkLogin($redirectUrl, true);
        $userInfo    = $logMsg['keyMain'];
        Gionee_Service_Log::pvLog('user_activity_scratch');
        Gionee_Service_Log::uvLog('user_activity_scratch', $this->getSource());
        //$rdData = User_Service_Gather::getBy(array('uid'=>$userInfo['id']));
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        if ($userScoreInfo['is_scratch'] == 0) {
            $res = User_Service_Gather::update(array('is_scratch' => 1), $userScoreInfo['id']);
            User_Service_Gather::getInfoByUid($userInfo['id'], true);
            if (!$res) {
                $this->output('-1', '更新数据失败');
            }
        }
        $this->output('0', '');
    }


    public function ajaxgetCouponAction() {
        $loginMsg = Common_Service_User::checkLogin('/user/index/index');
        if (!$loginMsg['key']) {
            $this->output('0','', array('redirect' => $loginMsg['keyMain']));
            exit();
        }
        $userInfo = $loginMsg['keyMain'];
        $check    = User_Service_BookCoupon::getBy(array('uid' => $userInfo['id']));
        if (empty($check)) {
            $coupon = User_Service_BookCoupon::getBy(array('uid' => 0, 'get_time' => 0));
            $ret    = User_Service_BookCoupon::update($coupon['id'], array(
                'uid'      => $userInfo['id'],
                'get_time' => time()
            ));
            if ($ret) {
                $data = array(
                    'status'   => 1,
                    'classify' => 13,
                    'coupon'   => $coupon['card_num'],
                    'uid'      => $userInfo['id']
                );
                Common_Service_User::sendInnerMsg($data, 'user_book_coupon');
                $this->output('0', '获取成功!', array('name' => "恭喜您<br>获得5元书券!"));
            } else {
                $this->output('0', '获取失败!',array('name'=>'获取失败!'));
            }
        }
        $this->output('0', '',array('name'=>'异常错误!'));
    }

    public function couponAction() {

    }

    public function quizAction() {
        $loginMsg = Common_Service_User::checkLogin('/user/activity/quiz');
        if (!$loginMsg['key']) Common::redirect($loginMsg['keyMain']);
        $userInfo = $loginMsg['keyMain'];
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'index:user_quiz');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'index:user_quiz', $userInfo['id']);

        $config        = $this->_getConfigData();
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        $list          = $this->_getQuizList($userInfo['id'], $config['quiz_per_day_num']);
        $this->assign('scoreInfo', $userScoreInfo);
        $this->assign('list', $list);
        $pos = $this->_getPostion($list, $userInfo['id'], mktime(0, 0, 0), mktime(23, 59, 59));
        if ($pos == $config['quiz_per_day_num']) {
            Common::redirect('/user/activity/quizdone');
        }
        $this->assign('pos', $pos);
        $this->assign('answerStatus', $this->quizStatus);
        $this->assign('config', $config);
    }

    public function ajaxAnsweringAction() {
        $loginMsg = Common_Service_User::checkLogin('/user/activity/quiz');
        if (!$loginMsg['key']) {
            $this->output('0', array('redirect' => $loginMsg['keyMain']));
        }
        $postData = $this->getInput(array('quiz_id', 'select_id'));
        if (empty($postData['quiz_id']) || !intval($postData['select_id'])) {
            $this->output('-1', '参数有错');
        }
        $userInfo = $loginMsg['keyMain'];
        $url      = Common_Service_User::validUsername($userInfo['username'], '/user/activity/quiz');
        if (!empty($url)) {
            $this->output('0', '参数有错', array('redirect' => $url));
        }
        $where             = array();
        $where['add_time'] = array(array('>=', mktime(0, 0, 0)), array('<=', mktime(23, 59, 59)));
        $where['uid']      = $userInfo['id'];
        $where['quiz_id']  = $postData['quiz_id'];
        $info              = User_Service_QuizResult::getBy($where);
        if (empty($info) || !empty($info['selected'])) {
            $this->output('-1', '信息不正确或已回答过该题');
        }
        $userScoreInfo  = User_Service_Gather::getInfoByUid($userInfo['id']);
        $remainedScores = $userScoreInfo['remained_score'];
        $redirect       = '';//跳转URL;
        $is_right       = -1;
        $config         = $this->_getConfigData();
        $quiz           = User_Service_Quiz::get($postData['quiz_id']);
        if ($postData['select_id'] == $quiz['answer']) {
            $is_right = 1;
        }
        $rckey = 'answer:' . $userInfo['id'] . ':' . $postData['quiz_id'];
        $e     = Common::getCache()->get($rckey);
        if (!empty($e)) {
            $this->output('-1', '', array('info' => '提交次数过多! 请稍后再试'));
        }
        Common::getCache()->set($rckey, 1, 3);
        $ret = User_Service_QuizResult::edit(array(
            'is_right'    => $is_right,
            'selected'    => $postData['select_id'],
            'answer_time' => time(),
        ), $info['id']);
        if ($ret) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'answer_total:user_quiz');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'answer_total:user_quiz', $userInfo['id']);
            if ($is_right == 1) {
                $scoreLog = $this->_GetScoreLog($userInfo['id'], $quiz['id'], '211');
                if (empty($scoreLog)) {
                    $scores         = $config['quiz_per_reward_scores'];
                    $remained       = User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], $scores, '211', 2, $quiz['id']); //赠送相应积分
                    $remainedScores = $remained >= 0 ? $remained : $remainedScores;
                }
                unset($where['quiz_id']);
                $where['selected'] = array('!=', 0);
                $answeredNum       = User_Service_QuizResult::count($where);
                if ($answeredNum == $config['quiz_per_day_num']) {
                    $redirect = sprintf("%s/user/activity/quizdone", Common::getCurHost());
                }
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'answer_succ:user_quiz');
                Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'answer_succ:user_quiz', $userInfo['id']);
                $this->output(0, 'successed', array(
                    'status'   => 1,
                    'scores'   => $scores,
                    'redirect' => $redirect,
                    'info'     => "回答正确。获得{$scores}金币",
                    'gold'     => $remainedScores
                ));
            } else {
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'answer_fail:user_quiz');
                Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'answer_fail:user_quiz', $userInfo['id']);
                $this->output(0, "failed", array(
                    'status'   => 0,
                    'scores'   => 0,
                    'redirect' => $redirect,
                    'info'     => '回答错误。',
                    'gold'     => $remainedScores
                ));
            }
        }
        $this->output(-1, 'fail', array('info' => '异常错误'));

    }

    public function ajaxGiveRewardScoresAction() {
        $loginMsg = Common_Service_User::checkLogin('/user/activity/quiz');
        if (!$loginMsg['key']) {
            $this->output('0', array('redirect' => $loginMsg['keyMain']));
        }
        $userInfo = $loginMsg['keyMain'];
        $url      = Common_Service_User::validUsername($userInfo['username'], '/user/activity/quizdone');
        if (!empty($url)) {
            $this->output('0', '', array('redirect' => $url));
        }

        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'reward:user_quiz');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'reward:user_quiz', $userInfo['id']);
        $flag = $this->_checkQuizRewardsStatus($userInfo['id']);
        if (empty($flag)) {
            $config    = $this->_getConfigData();
            $dataList  = $this->_getUserAnswerData($userInfo['id']);
            $rightData = $dataList['1'];
            if (count($rightData) == $config['quiz_per_day_num']) {
                $scoreLog = $this->_GetScoreLog($userInfo['id'], 0, '212');
                if (empty($scoreLog)) {
                    $rewardScores = $config['quiz_all_right_reward_scores'];
                    User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], $rewardScores, '212', 2, 0); //赠送相应积分
                }
            }
        }
        Common::redirect('/user/activity/quizdone');
    }

    public function quizDoneAction() {
        $loginMsg = Common_Service_User::checkLogin('/user/activity/quiz');
        if (!$loginMsg['key']) $this->output('0', array('redirect' => $loginMsg['keyMain']));

        $userInfo = $loginMsg['keyMain'];
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'done:user_quiz');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'done:user_quiz', $userInfo['id']);
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);

        $dataList = $this->_getUserAnswerData($userInfo['id']);
        if (empty($dataList) || count($dataList['0']) > 0) {
            Common::redirect('/user/activity/quiz');
        }
        $wrongList = $dataList['-1'];
        $config    = $this->_getConfigData();
        $giveFlag  = 0;
        if ($config['quiz_per_day_num'] == count($dataList['1'])) {
            $giveFlag = 1;
        }

        $rewardFlag = $this->_checkQuizRewardsStatus($userInfo['id']);
        $this->assign('rewardFlag', $rewardFlag);
        $this->assign('wrongData', $wrongList);
        $this->assign('scoreInfo', $userScoreInfo);
        $this->assign('quizSerial', $this->quizSerial);
        $this->assign('isExists', $giveFlag);
        $this->assign('config', $this->_getConfigData());
    }

    public function quizToAction() {
        $type  = $this->getInput('type');
        $title = $this->getInput('title');

        if (empty($type)) {
            exit('access deny!');
        }

        $loginMsg = Common_Service_User::checkLogin('/user/activity/quiz');
        if (!$loginMsg['key']) $this->output('0', array('redirect' => $loginMsg['keyMain']));

        $userInfo = $loginMsg['keyMain'];
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $type . ':user_quiz');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $type . ':user_quiz', $userInfo['id']);

        Common::redirect($this->_baiduSearch($title));
    }


    public function quizRewardSuccessAction() {
        $loginMsg = Common_Service_User::checkLogin('/user/activity/quiz');
        if (!$loginMsg['key']) $this->output('0', array('redirect' => $loginMsg['keyMain']));

        $userInfo = $loginMsg['keyMain'];

        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        $this->assign('scoreInfo', $userScoreInfo);
        $this->assign('config', $this->_getConfigData());
    }


    public function quizClearAction() {
        if (stristr(ENV, 'test')) {
            $userInfo          = Gionee_Service_User::getCurUserInfo();
            $where             = array();
            $where['uid']      = $userInfo['id'];
            $where['add_time'] = array(array('>=', mktime(0, 0, 0)), array("<=", mktime(23, 59, 59)));
            $ret               = User_Service_QuizResult::deletesBy($where);
            $this->_checkQuizRewardsStatus($userInfo['id']);
            $config = $this->_getConfigData();
            $this->_getQuizList($userInfo['id'], $config['quiz_per_day_num'], true);
        }
    }


    private function _GetScoreLog($uid, $quizId = 0, $scoreType = 0) {
        $arr               = array();
        $arr['fk_earn_id'] = $quizId;
        $arr['uid']        = $uid;
        $arr['date']       = date('Ymd', time());
        $arr['score_type'] = $scoreType;
        $data              = User_Service_ScoreLog::getBy($arr);
        return $data;
    }

    private function _checkUsername($username, $callback) {
        $validPhone = Common::checkIllPhone($username);
        if (!$validPhone) {
            $url = sprintf("%s/user/index/bindphone?f=%d&redirect=%s", Common::getCurHost(), 4, $callback);
            $this->output('0', '', array('redirect' => $url));
        }
    }

    private function _checkQuizRewardsStatus($uid) {
        $flag                = 0;
        $where               = array();
        $where['uid']        = $uid;
        $where['date']       = date('Ymd', time());
        $where['group_id']   = 2;
        $where['score_type'] = 212;
        $info                = User_Service_ScoreLog::getBy($where);
        if (!empty($info)) {
            $flag = 1;
        }
        return $flag;
    }

    private function _getUserAnswerData($uid) {
        $where             = $data = array();
        $where['uid']      = $uid;
        $where['add_time'] = array(array('>=', mktime(0, 0, 0)), array('<=', mktime(23, 59, 59)));
        $temp              = User_Service_QuizResult::getsBy($where);
        if (!empty($temp)) {
            foreach ($temp as $k => $v) {
                $info                           = User_Service_Quiz::get($v['quiz_id']);
                $info['options']                = array(
                    '1' => $info['option1'],
                    '2' => $info['option2'],
                    '3' => $info['option3'],
                    '4' => $info['option4']
                );
                $info['selected']               = $v['selected'];
                $keywords                       = $info['keywords'] ? $info['keywords'] : $info['title'];
                $info['searchUrl']              = $this->_baiduSearch($keywords);
                $data[$v['is_right']][$v['id']] = $info;
            }
        }
        return $data;
    }

    private function _getQuizList($uid, $num = 5, $sync = false) {
        $key    = "USER:QUIZ:LIST:{$uid}:";
        $result = Common::getCache()->get($key);
        if ($result === false || $sync == true) {
            $where             = $result = array();
            $where['uid']      = $uid;
            $where['add_time'] = array(array(">=", mktime(0, 0, 0)), array("<=", mktime(23, 59, 59)));
            $list              = User_Service_QuizResult::getsBy($where, array('id' => 'DESC'));
            if (empty($list)) { //第一次时，添加
                $data = $this->_generate();
                $keys = array_rand($data, $num);
                foreach ($keys as $v) {
                    $quizInfo              = $data[$v];
                    $keywords              = !empty($quizInfo['keywords']) ? $quizInfo['keywords'] : $quizInfo['title'];
                    $quizInfo['searchUrl'] = $this->_baiduSearch($keywords);
                    $quizInfo['is_right']  = 0;
                    $result[]              = $quizInfo;
                    $temp['uid']           = $uid;
                    $temp['quiz_id']       = $quizInfo['id'];
                    $temp['add_time']      = time();
                    $ret                   = User_Service_QuizResult::add($temp);
                }
            } else {
                $ids = $arrRight = array();
                foreach ($list as $key => $val) {
                    $ids[]                     = $val['quiz_id'];
                    $arrRight[$val['quiz_id']] = $val['is_right'];
                }
                $dataList = User_Service_Quiz::getsBy(array('id' => array("IN", $ids)), array('id' => 'DESC'));
                foreach ($dataList as $m => $n) {
                    $n['searchUrl'] = Gionee_Service_Baidu::getSearchUrl($n['title']);
                    $n['is_right']  = $arrRight[$n['id']];
                    $result[]       = $n;
                }
            }
            Common::getCache()->set($key, $result, 30);
        }
        return $result;
    }

    public function _baiduSearch($val) {
        $url = "http://wap.sogou.com/web/sl?keyword={$val}&bid=sogou-mobp-c5c1bda1194f9423";
        //$url = 'http://m.baidu.com/s?from=1670a&word=' . $val;
        return $url;
    }


    private function  _generate() {
        $key  = "USER:ACTIVITY:QUIZ";
        $data = Common::getCache()->get($key);
        if (empty($data)) {
            list($total, $data) = User_Service_Quiz::getList(1, 300, array(), array('id' => 'DESC'));
            Common::getCache()->set($key, $data, 600);
        }
        return $data;
    }

    private function _getPostion($data, $uid, $sdate, $edate) {
        $pos               = count($data);
        $where             = array();
        $where['add_time'] = array(array('>=', $sdate), array('<=', $edate));
        $where['uid']      = $uid;
        foreach ($data as $k => $v) {
            $where['quiz_id'] = $v['id'];
            $ret              = User_Service_QuizResult::getBy($where);
            if (empty($ret['selected']) || empty($ret['is_right'])) {
                $pos = $k;
                break;
            }
        }
        return $pos;
    }

    private function _getConfigData() {
        $key        = "USER::CONFIG:QUIZ:DATA:";
        $rs         = Common::getCache();
        $configData = $rs->get($key);
        if (empty($configData)) {
            $param           = array();
            $param['3g_key'] = array(
                'IN',
                array(
                    'quiz_per_reward_scores',
                    'quiz_all_right_reward_scores',
                    'quiz_rule_content',
                    'quiz_per_day_num',
                    'quiz_status'
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
}