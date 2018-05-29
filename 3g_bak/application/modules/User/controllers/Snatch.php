<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SnatchController extends User_BaseController {

    public $keyTypes = array(
        '4' => '金钥匙',
        '3' => '银钥匙',
        '2' => '铜钥匙',
        '1' => '铁钥匙',
    );

    public function indexAction() {
        Gionee_Service_Log::pvLog('user_snatch_index');
        Gionee_Service_Log::uvLog('user_snatch_index', $this->getSource());
        $goods    = $this->_getGoodsList();
        $uid      = 0;
        $userInfo = Gionee_Service_User::getCurUserInfo();
        if (!empty($userInfo)) {
            $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
            $uid           = $userInfo['id'];
            $this->assign('userScores', $userScoreInfo);
        }
        $this->assign('remainedTimes', $this->_remainedTimes($uid));
        $this->assign('goods', $goods);
        $this->assign('userinfo', $userInfo);
        $this->assign('config', $this->_getConfigData());
    }


    public function ajaxSnatchingAction() {
        $t_bi = $this->getSource();
        Gionee_Service_Log::pvLog('user_snatch_ajax');
        Gionee_Service_Log::uvLog('user_snatch_ajax', $t_bi);

        $loginInfo = Common_Service_User::checkLogin('/user/snatch/index');        //检测是否登陆
        if (!$loginInfo['key']) {
            $this->output('0', '', array('redirect' => $loginInfo['keyMain']));
        }
        $userInfo = $loginInfo['keyMain'];
        $goodsId  = $this->getInput('id');
        if (!intval($goodsId)) {
            $this->output('0', '', array('error_msg' => '商品信息有错!', 'error_key' => '-2'));
        }

        $url = Common_Service_User::validUsername($userInfo['username'], '/user/snatch/index'); //用户手机合法性检测
        if (!empty($url)) {
            $this->output('0', '', array('redirect' => $url));
        }
        $goods = User_Service_Snatch::getBy(array('id' => $goodsId, 'status' => 1));
        if (empty($goods)) {
            $this->output('0', '', array('error_msg' => '商品不存在！', 'error_key' => '-2'));
        }
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_SNATCH_G_PV, $goodsId . ":{$goods['cost_scores']}");

        $scoreMsg = User_Service_Gather::getBy(array('uid' => $userInfo['id']));
        if ($scoreMsg['remained_score'] < $goods['cost_scores']) {
            $this->output('0', '', array('error_msg' => '<p>您的金币不足</p><p>签到,做任务都可获得金币哦！</p>', 'error_key' => '-2'));
        }
        $remainedTimes = $this->_remainedTimes($userInfo['id']);
        if (empty($remainedTimes)) {
            $this->output('0', '', array('error_msg' => '当日游戏次数已用完！', 'error_key' => '-2'));
        }

        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_SNATCH_G_UV, $goodsId . ":{$goods['cost_scores']}", $t_bi);
        //Common_Service_Base::beginTransaction();//事务开始
        $result = User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], -$goods['cost_scores'], '311');
        if ($result >= 0) {
            $ratioList = $prizeList = array();
            $prizeInfo = json_decode($goods['prize_info'], true);
            foreach ($prizeInfo as $k => $v) {
                $ratioList[$k] = $v['ratio'];
                $prizeList[$k] = $v['prize'];
            }

            $prizeLevel = Common_Service_User::getRangeData($ratioList);
            if (intval($prizeList[$prizeLevel])) {
                $remainedScores = $this->_handlePrize($userInfo['id'], $prizeList[$prizeLevel], $goods['id']);//发放奖品并写日志
                $callback       = array(
                    'error_key'       => '0',
                    'prize_scores'    => $prizeList[$prizeLevel],
                    'error_msg'       => "恭喜您,获得{$prizeList[$prizeLevel]}金币",
                    'cost_scores'     => $goods['cost_scores'],
                    'key_name'        => $this->keyTypes[$goods['sort']],
                    'remained_scores' => $remainedScores,
                    'remained_times'  => $remainedTimes - 1
                );
            } else {
                $callback = array(
                    'error_key'       => '-1',
                    'prize_scores'    => 0,
                    'error_msg'       => "很遗憾，宝藏被其他人拿走",
                    'cost_scores'     => $goods['cost_scores'],
                    'key_name'        => $this->keyTypes[$goods['sort']],
                    'remained_scores' => $scoreMsg['remained_score'] - $goods['cost_scores'],
                    'remained_times'  => $remainedTimes - 1
                );
            }
            $ret = User_Service_Gather::deduceFrozenScores($userInfo['id'], $goods['cost_scores']);//扣除冻结金币
           // Common_Service_Base::commit();
            $this->output('0', '', $callback);
        } else {
           // Common_Service_Base::rollBack();
            $this->output('0', '', array('error_key' => '-2', 'error_msg' => '对不起,操作失败,请重试!'));
        }

    }

    private function _handlePrize($uid, $scores, $goodsId, $type = 1) {
        $ret = false;
        switch ($type) {
            case 1: {
                $ret = User_Service_Gather::changeScoresAndWriteLog($uid, $scores, '207', 2, $goodsId);
                break;
            }
            default:
                break;
        }
        return $ret;
    }

    private function _getGoodsList() {
        $key      = "USER:SNATCH:GOODS:LIST";
        $rs       = Common::getCache();
        $dataList = $rs->get($key);
        if (empty($dataList)) {
            list($total, $dataList) = User_Service_Snatch::getlist(1, 8, array('status' => 1), array(
                'sort'     => 'ASC',
                'add_time' => 'DESC'
            ));
            $rs->set($key, $dataList, 60);
        }
        return $dataList;
    }

    private function _remainedTimes($uid) {
        $where               = array();
        $where['uid']        = $uid;
        $where['score_type'] = 311;
        $where['date']       = date('Ymd', time());
        $count               = User_Service_ScoreLog::count($where);

        $config         = self::_getConfigData();
        $snatchMaxTimes = $config['snatch_max_times'];
        if (intval($config['snatch_max_times']) && $snatchMaxTimes <= $count) {
            return 0;
        }
        return $snatchMaxTimes - $count;
    }

    private function _getConfigData() {
        $key        = "USER:SNATCH:CONFIG:DATA:";
        $rs         = Common::getCache();
        $configData = $rs->get($key);
        if (empty($configData)) {
            $param           = array();
            $param['3g_key'] = array(
                'IN',
                array('snatch_status', 'snatch_rule_content', 'snatch_free_times', 'snatch_max_times')
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