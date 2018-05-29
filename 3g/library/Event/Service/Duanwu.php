<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Event_Service_Duanwu {

    static public function getConfig() {
        static $cfgArr = array();
        if (!empty($cfgArr)) {
            return $cfgArr;
        }
        $configStr = Gionee_Service_Config::getValue('duanwu_config');
        $cfgArr    = json_decode($configStr, true);
        return $cfgArr;
    }

    static public function getTimes() {
        $cfgArr = self::getConfig();
        return intval($cfgArr['duanwu_times']);
    }


    static public function getRankKinds() {
        $cfgArr = self::getConfig();
        return $cfgArr['duanwu_goods'];
    }

    static public function getKindRate() {
        $cfgArr  = self::getConfig();
        $s       = $cfgArr['duanwu_kind']['s'];
        $n       = $cfgArr['duanwu_kind']['n'];
        $i       = 0;
        $ret     = array();
        $rankArr = explode(',', $cfgArr['duanwu_kind']['rate']);
        foreach ($rankArr as $v) {
            $t       = $s + $i * $n;
            $ret[$t] = $v;
            $i++;
        }
        return $ret;
    }

    static public function getCoinRate() {
        $cfgArr = self::getConfig();
        $tmp    = $cfgArr['duanwu_coin_rate'];
        $a1     = explode("\n", $tmp);
        $ret    = array();
        foreach ($a1 as $v) {
            $a2 = explode(',', trim($v));
            if (!empty($a2[0]) && !empty($a2[1])) {
                $ret[$a2[0]] = intval($a2[1]);
            }
        }
        return $ret;
    }

    static public function getTime() {
        $cfgArr = self::getConfig();
        $tmp    = $cfgArr['duanwu_open'];
        return array(strtotime($tmp['start']), strtotime($tmp['end']));
    }

    static public function getInfoByName($uName) {
        $info = Event_Service_Duanwu::getUserInfoByUname($uName);
        if (empty($info['id'])) {
            $uaArr   = Util_Http::ua();
            $addData = array(
                'uid'        => 0,
                'uname'      => $uName,
                'uuid'       => !empty($uaArr['uuid']) ? $uaArr['uuid'] : '',
                'uuid_real'  => !empty($uaArr['uuid_real']) ? $uaArr['uuid_real'] : '',
                'app_ver'    => !empty($uaArr['app_ver']) ? $uaArr['app_ver'] : '',
                'model'      => !empty($uaArr['model']) ? $uaArr['model'] : '',
                'ip_addr'    => !empty($uaArr['ip']) ? $uaArr['ip'] : '',
                'created_at' => time(),
                'updated_at' => time(),
                'cur_date'   => date('Ymd'),
                'cur_num'    => 0,
            );
            $ret     = Event_Service_Duanwu::getUserDao()->insert($addData);
            if ($ret) {
                $id         = Event_Service_Duanwu::getUserDao()->getLastInsertId();
                $info       = $addData;
                $info['id'] = $id;
            }
        }
        return $info;
    }

    static public function checkUser() {

        $now     = time();
        $timeArr = Event_Service_Duanwu::getTime();
        if ($now < $timeArr[0] || $now > $timeArr[1]) {
            return false;
        }

        $uName   = Common::getUName();
        $info    = Event_Service_Duanwu::getInfoByName($uName);
        $ret     = array();
        $logInfo = Event_Service_Duanwu::getLogInfo($info['log_id']);
        if (!empty($logInfo['rank']) && $logInfo['status'] == 0) {

            $userInfo = Gionee_Service_User::getCurUserInfo();
            if (!empty($userInfo['id'])) {
                $where         = array('uid' => $userInfo['id'], 'date' => date('Ymd'));
                $num           = Event_Service_Duanwu::getLogDao()->count($where);
                $ret['is_max'] = $num >= Event_Service_Duanwu::getTimes() ? 1 : 0;
            } else {
                $ret['is_max'] = $info['cur_num'] >= Event_Service_Duanwu::getTimes() ? 1 : 0;
            }
            $ret['id']        = $info['id'];
            $ret['max_times'] = Event_Service_Duanwu::getTimes();
            $ret['log_id']    = $logInfo['id'];
            $ret['rank']      = $logInfo['rank'];
            $ret['val']       = $logInfo['val'];
            $rankKinds        = Event_Service_Duanwu::getRankKinds();
            if (isset($rankKinds[$logInfo['rank']])) {
                $goodsId    = $logInfo['val'];
                $goodsInfo  = User_Service_Commodities::get($goodsId);
                $ret['msg'] = "你的奖品" . $goodsInfo['name'];
                $ret['url'] = sprintf('%s/user/goods/detail?from=duanwu&goods_id=%d', Common::getCurHost(), $goodsId);
            } else {
                $coin       = $logInfo['val'];
                $ret['msg'] = $coin . "金币领取成功";
                $ret['url'] = sprintf('%s/user/index/index?from=7', Common::getCurHost());
            }
        }
        return $ret;
    }

    public static function checkGoods($goodsInfo) {
        $ret = array();
        if ($goodsInfo['event_flag'] == 1) {
            $duanwuInfo = Event_Service_Duanwu::checkUser();
            if (!empty($duanwuInfo['rank']) && $duanwuInfo['val'] == $goodsInfo['id']) {
                $ret = $duanwuInfo;
            } else {
                Common::redirect(Common::getCurHost() . '/user/index/index');
            }
        }
        return $ret;
    }

    static public function upToDone($uName, $logId, $uid) {
        $upLogData = array(
            'status'     => 1,
            'uid'        => $uid,
            'updated_at' => time(),
        );
        Event_Service_Duanwu::getLogDao()->update($upLogData, $logId);
        Event_Service_Duanwu::getLogInfo($logId, true);

        $upData = array('status' => 0, 'log_id' => 0);
        Event_Service_Duanwu::upUserInfo($upData, $uName);
    }


    /**
     * @return Event_Dao_DuanwuLog
     */
    public static function getLogDao() {
        return Common::getDao("Event_Dao_DuanwuLog");
    }

    /**
     * @return Event_Dao_DuanwuUser
     */
    public static function getUserDao() {
        return Common::getDao("Event_Dao_DuanwuUser");
    }

    static public function upUserInfo($upData, $uName) {
        Event_Service_Duanwu::getUserDao()->updateBy($upData, array('uname' => $uName));
        Event_Service_Duanwu::getUserInfoByUname($uName, true);
    }


    static public function getUserInfoByUname($uname, $sync = false) {
        if (empty($uname)) {
            return false;
        }
        $rcKey = '5DUANWU_USER_INFO:' . $uname;
        $info  = Common::getCache()->get($rcKey);
        if (empty($info['id']) || $sync) {
            $info = Event_Service_Duanwu::getUserDao()->getBy(array('uname' => $uname));
            Common::getCache()->set($rcKey, $info, Common::T_ONE_DAY);
        }
        return $info;
    }

    static public function getLogInfo($id, $sync = false) {
        if (empty($id)) {
            return false;
        }
        $rcKey = '5DUANWU_LOG_INFO:' . $id;
        $info  = Common::getCache()->get($rcKey);
        if (empty($info['id']) || $sync) {
            $info = Event_Service_Duanwu::getLogDao()->get($id);
            Common::getCache()->set($rcKey, $info, Common::T_ONE_DAY);
        }
        return $info;
    }

    public function listlogexport() {

        ini_set('memory_limit', '1024M');
        $out = fopen('/tmp/duanwu_log.csv', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));

        $total     = Event_Service_Duanwu::getLogDao()->count();
        $offset    = 100;
        $totalPage = ceil($total / $offset);
        var_dump($total, $totalPage);
        for ($page = 1; $page <= $totalPage; $page++) {
            $start = ($page - 1) * $offset;
            $list  = Event_Service_Duanwu::getLogDao()->getList($start, $offset);
            foreach ($list as $k => $v) {
                $v['created_at'] = date('y/m/d H:i:s', $v['created_at']);
                $v['updated_at'] = date('y/m/d H:i:s', $v['updated_at']);
                $v['status']     = $v['status'] == 1 ? '已领取' : '未领取';
                $area            = Vendor_IP::find($v['ip_addr']);
                $v['ip_area']    = implode(',', array($area[1], $area[2]));
                fputcsv($out, array_values($v));
            }
            echo $start . "\n";
        }
        fclose($out);
    }
}