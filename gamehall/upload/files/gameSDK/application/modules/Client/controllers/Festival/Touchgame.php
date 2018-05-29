<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 划屏游戏
 * @author fanch
 *
 */
class Festival_TouchgameController extends Client_BaseController{

    public function indexAction(){
        $url = $this->getLocalUrl();
        $request = $this->getInput(array('source', 'puuid', 'sp'));
        $account = $this->getOnlineAccount($request);
        $time = time();
        $info = $this->getOnlineInfo($time);
        if(!$info){
            $this->display('close');
            die;
        }
        $canPlay = $this->canPlayGame($info, $time);
        if($canPlay){
            $propsConfig = $this->getPropsConfig($info['props_config']);
            $gamePool = $this->initGamePool($propsConfig);
            $this->assign('gamePool', $gamePool);
            $this->assign('propsConfig', $propsConfig);
        }
        $historyScore = $this->getUserHistoryScore($info['id'], $account['uuid']);
        $canStore = $this->canStoreScore($info, $time);
        if($canStore) {
            $this->initUserChance($info['id'], $account['uuid']);
            $userChance = $this->getUserChance($account['uuid']);
            $this->assign('userChance', $userChance);
        }

        $remainTimes = $this->getUserRemainTimes($account,$canStore, $userChance);
        $weiXin = $this->getShareInfo($url);
        $clientDownLoad = Game_Service_Config::getValue('game_link');
        $weiXinDownLoad = Game_Service_Config::getValue('game_amgo_url');
        $this->assign('account', $account);
        $this->assign('time', $time);
        $this->assign('info', $info);
        $this->assign('historyScore', $historyScore);
        $this->assign('weiXin', $weiXin);
        $this->assign('requestUrl', $url);
        $this->assign('remainTimes', $remainTimes);
        $this->assign('clientDownLoad', $clientDownLoad);
        $this->assign('weiXinDownLoad', $weiXinDownLoad);
        if($time > $info['end_time']){
            $this->display('close');
            die;
        }
        if($time < $info['start_time']){
            $this->display('preheat');
            die;
        }
        $this->display('index');
        die;
    }

    private function getUserRemainTimes($account, $canStore, $userChance){
        if(!$account) return 1;
        if(!$canStore) return 1;
        if(!$userChance['valid']) return 0;
        return $userChance['remainTimes'];
    }

    public function rankAction(){
        $time = time();
        $info = $this->getOnlineInfo($time);
        $data = array();
        if($info) {
            $search = array('info_id' => $info['id'], 'status' => 1, 'score' => array('!=', 0));
            $orderBy = array('score' => 'DESC', 'create_time' => 'ASC');
            list(, $data) = Festival_Service_TouchGame::getTotalList(1, 10, $search, $orderBy);
        }
        $this->assign('data', $data);
    }

    public function submitAction(){
        $request = $this->getInput(array('score', 'grant', 'source', 'puuid', 'sp'));
        $time = time();
        $info = $this->getOnlineInfo($time);
        if(!$info) $this->output(-1, '非法提交');

        $account = $this->getOnlineAccount($request);
        $login = ($account) ? true : false;
        $canStore = $this->canStoreScore($info, $time);
        $score = $request['score'];
        $userChance = $this->getUserChance($account['uuid']);
        $remainTimes = ($userChance) ? $userChance['remainTimes'] : 0;
        $share = ($userChance) ? $userChance['share'] : false;
        //未登录
        if (!$login){
            $this->response(0, $info, 1, $share, $score);
        }
        //不在游戏记分时间内
        if (!$canStore){
            $this->response(1, $info, 1, $share, $score);
        }

        //用户未经初始化不能玩
        if (!$userChance)  $this->output(-1, '非法提交');
        //无效用户
        if(!$userChance['valid']) $this->output(-1, '非法提交');

        //今日已无剩余机会。
        if (!$remainTimes) {
            if ($share) {
                //已分享
                $this->response(-1, $info, 0, $share, 0);
            } else {
                //未分享
                $this->response(5, $info, 0, $share, 0);
            }
        }

        //更新游戏基础表参与的总次数
        Festival_Service_TouchGame::updateByInfo(array('times'=> $info['times'] + 1), array('id'=> $info['id']));
        //减掉可抽奖次数
        $userChance['remainTimes'] = $userChance['remainTimes'] - 1;
        $this->saveUserChance($account['uuid'], $userChance);
        //重新获取剩余机会
        $remainTimes = $userChance['remainTimes'];

        //本次分数为0
        if (!$score){
            //记录
            $this->saveUserData($info, $account, 0, 0, $time);
            $this->response(2, $info, $remainTimes, $share, 0);
        }

        //校验用户提交数据，并计算获取的积分数。
        $propsConfig = $this->getPropsConfig($info['props_config']);
        $points = $this->getPoints($propsConfig, $score, $request['grant']);

        if(!$points) $this->output(-1, '提交非法');

        //游戏阀值处理
        $waringConfig = json_decode($info['waring_config'], true);
        if ($score > $waringConfig['score']) {
            //设置用户有效状态
            $userChance['valid'] = false;
            $this->saveUserChance($account['uuid'], $userChance);
            //记录刷分数据
            $this->saveUserData($info, $account, $score, $points, $time, 0);
            //触发刷分阀值
            $this->response(-2, $info, 0, false, 0);
        }
        //计算需要再次赠送的积分
        $usePoints = $waringConfig['point'] - $userChance['grantPoints'];
        $sendPoint = 0;
        if($usePoints){
            if ($points < $usePoints) {
                $sendPoint = $points;
            } else {
                $sendPoint = $usePoints;
            }
            $this->sendUserPoints($account['uuid'], $info['id'], $sendPoint);
            //更新用户今天已送的积分总数
            $userChance['historyScore'] = ($userChance['historyScore'] < $score) ? $score : $userChance['historyScore'];
            $userChance['grantPoints'] = $userChance['grantPoints'] + $sendPoint;
            $this->saveUserChance($account['uuid'], $userChance);
            //更新游戏基础表发放总积分数
            Festival_Service_TouchGame::updateByInfo(array('points'=> $info['points'] + $sendPoint), array('id'=> $info['id']));
        } else {
            //更新用户历史最高纪录
            $userChance['historyScore'] = ($userChance['historyScore'] < $score) ? $score : $userChance['historyScore'];
            $this->saveUserChance($account['uuid'], $userChance);
        }

        $code = ($sendPoint) ? 3 : 4;

        //修改统计数据
        $result = $this->saveUserData($info, $account, $score, $sendPoint, $time);
        if(!$result) $this->output(-1, '保存失败');

        $this->response($code, $info, $remainTimes, $share, $score, $sendPoint);
    }

    private function response($code, $info, $remainTimes, $share, $score, $point = 0){
        //最后一次处理
        if (in_array($code, array(2, 3, 4))) {
            if(!$remainTimes) {
                $code = ($share) ? -1 : 5;
            }
        }
        $propsConfig = $this->getPropsConfig($info['props_config']);
        $gamePool = $this->initGamePool($propsConfig);
        $data = array(
            'code' => $code,
            'grant' =>$score,
            'integral' => $point,
            'share'=> $share,
            'remainTimes' => $remainTimes,
            'fish' => $gamePool
        );
        $this->output(0, '', $data);
    }

    public function shareAction(){
        $request = $this->getInput(array('score', 'puuid', 'sp'));
        $account = $this->getOnlineAccount($request);
        if(!$account) $this->output(0, '');
        $uuid = $account['uuid'];
        $userChance = $this->getUserChance($uuid);
        if($userChance && (!$userChance['share'])){
            $userChance['remainTimes'] = $userChance['remainTimes'] + 1;
            $userChance['share'] = true;
            $this->saveUserChance($uuid, $userChance);
        }
        $this->output(0, '');
    }

    private function initGamePool($config, $number = 300){
        $result = array();
        foreach($config as $value ){
            if($value['probability']<=0) continue;
            $total = ($value['probability']/100) * $number;
            $new = array_fill(0, $total, intval($value['index']));
            $result = array_merge($result, $new);
        }
        shuffle($result);
        return $result;
    }

    private function getPoints($propsConfig, $score, $grant){
        $grantPoints =  $grantScore = 0;
        foreach ($grant as $index => $value){
            $grantPoints += $propsConfig[$index]['point'] * $value;
            $grantScore += $propsConfig[$index]['score'] * $value;
        }
        if($grantScore != $score) return 0;
        return $grantPoints;
    }

    private function getPropsConfig($propsConfig){
        $config = json_decode($propsConfig, true);
        $result = array();
        $index = 1;
        foreach($config as $item){
            if($item['type'] == 0){
                $item['index'] = 0;
            } else {
                $item['index'] = $index;
            }
            $result[] = $item;
            $index++;
        }
        $result = Common::resetKey($result, 'index');
        return $result;
    }

    private function getCacheKey(){
        $time = time();
        $info = $this->getOnlineInfo($time);
        $cacheKey =  Util_CacheKey::FESTIVAL_TOUCH_GAME.':'.$info['id'];
        return $cacheKey;
    }

    private function initUserChance($infoId, $uuid){
        $cacheKey =  $this->getCacheKey();
        $cache = Cache_Factory::getCache();
        $cacheData = $cache->hGet($cacheKey, $uuid);
        if($cacheData == false){
               $userChance = array(
                'valid' => $this->isValid($infoId, $uuid),
                'remainTimes' => 6,
                'share' => false,
                'historyScore' =>$this->getUserHistoryScore($infoId, $uuid),
                'grantPoints' =>0
            );
            $this->saveUserChance($uuid, $userChance);
        }
    }

    private function isValid($infoId, $uuid){
        $flag = true;
        $data = Festival_Service_TouchGame::getByTotal(array('info_id'=>$infoId, 'uuid'=>$uuid));
        if($data){
            $flag = $data['status'] ? true : false;
        }
        return $flag;
    }

    private function getUserChance($uuid){
        $userChance = array();
        $cacheKey = $this->getCacheKey();
        $cache = Cache_Factory::getCache();
        $cacheData = $cache->hGet($cacheKey, $uuid);
        if($cacheData != false){
            $userChance = json_decode($cacheData, true);
        }
        return $userChance;
    }

    private function getUserHistoryScore($infoId, $uuid){
        $data = Festival_Service_TouchGame::getByTotal(array('info_id'=>$infoId, 'uuid'=>$uuid));
        if(!$data) return 0;
        return $data['score'];
    }

    private function saveUserChance($uuid, $data){
        $expire = strtotime('tomorrow') - time();
        $cacheKey = $this->getCacheKey();
        $cache = Cache_Factory::getCache();
        $cache->hSet($cacheKey, $uuid, json_encode($data), $expire);
    }

    private function getOnlineInfo($time){
        $params = array(
            'preheat_time'=> array('<=', $time),
            'end_time' => array('>=', $time),
            'status' => 1
        );
        $info = Festival_Service_TouchGame::getByInfo($params);
        return $info;
    }

    private function getShareInfo($url){
        $appid = Api_WeiXin_Sdk::getAppID();
        $token = Api_WeiXin_Sdk::getToken();
        $jsticket = Api_WeiXin_Sdk::getJsTicket($token);
        $nonceStr = Api_WeiXin_Sdk::getNoncestr();
        $time = time();
        $signature = Api_WeiXin_Sdk::getJsSignature($jsticket,$nonceStr,$time, $url);
        $data = array(
            'appId'=> $appid,
            'timestamp'=> $time,
            'nonceStr' => $nonceStr,
            'signature' => $signature
        );
        return $data;
    }

    private function getLocalUrl(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }

    /**
     * @param $request
     * @param $uuid
     * @param $imei
     */
    private function getOnlineAccount($request) {
        if ($request['sp']) {
           $account = $this->getClientAccount($request);
        } else {
            $account = $this->getWebAccount();
        }
        return $account;
    }

    /**
     * @param $uuid
     * @param $imei
     * @param $user
     */
    private function getClientAccount($request) {
        $uuid = $request['puuid'];
        $imei = Common::parseSp($request['sp'], 'imei');
        $account = array();
        if (!$uuid) return array();
        $onlineUser = Account_Service_User::checkOnline($uuid, $imei, 'uuid');
        if ($onlineUser) {
            $userInfo = Account_Service_User::getUserInfo(array('uuid' => $uuid));
            $attachPath = Common::getAttachPath();
            $account = array(
                'uuid' => $uuid,
                'uname' => $userInfo['uname'],
                'avatar' => $userInfo['avatar'] ? $attachPath . $userInfo['avatar'] : "",
                'nickname' => $userInfo['nickname'],
                'optime' => time()
            );
        }
        return $account;
    }

    /**
     * @return array|bool
     */
    private function getWebAccount() {
        $onlineUser = Account_Service_User::checkOnlineByWeb();
        if (!$onlineUser) return array();
        return $onlineUser;
    }

    /**
     * @param $info
     * @param $userPoint
     * @param $points
     */
    private function sendUserPoints($uuid, $infoId, $points) {
        $userPoint['uuid'] = $uuid;
        $userPoint['gain_type'] = 10;
        $userPoint['gain_sub_type'] = $infoId;
        $userPoint['points'] = $points;
        $userPoint['create_time'] = Common::getTime();
        $userPoint['update_time'] = Common::getTime();
        $userPoint['status'] = 1;
        Point_Service_User::gainPoint($userPoint);
    }

    /**
     * @param $info
     * @param $account
     * @param $request
     * @param $points
     * @param $time
     */
    private function saveUserLogs($info, $account, $score, $points, $time) {
        $logData = array(
            'info_id' => $info['id'],
            'uuid' => $account['uuid'],
            'uname' => $account['uname'],
            'score' => $score,
            'points' => $points,
            'create_time' => $time
        );
        Festival_Service_TouchGame::insertLog($logData);
    }

    /**
     * @param $info
     * @param $account
     * @param $score
     * @param $points
     * @param $time
     * @param int $status
     * @return bool
     */
    private function saveUserData($info, $account, $score, $points, $time, $status = 1) {
        //保存日志
        $this->saveUserLogs($info, $account, $score, $points, $time);
        //保存统计数据
        $totalData = Festival_Service_TouchGame::getByTotal(array('info_id' => $info['id'], 'uuid' => $account['uuid']));
        if ($totalData) {
            //更新
            $updateData = array('status'=>$status, 'times' => $totalData['times'] + 1, 'score' => ($totalData['score'] < $score) ? $score : $totalData['score'], 'points' => $totalData['points'] + $points);
            $result = Festival_Service_TouchGame::updateByTotal($updateData, array('id' => $totalData['id']));
        } else {
            //新增
            $addData = array('status'=>$status, 'info_id' => $info['id'], 'uuid' => $account['uuid'], 'uname' => $account['uname'], 'times' => 1, 'score' => $score, 'points' => $points, 'create_time' => $time);
            $result = Festival_Service_TouchGame::insertTotal($addData);
            //更新游戏基础表参与的总人数
            Festival_Service_TouchGame::updateByInfo(array('join'=>$info['join']+1),array('id'=> $info['id']));
        }

        return $result;
    }

    /**
     * @param $account
     * @param $info
     * @param $time
     */
    private function canPlayGame($info, $time) {
        if(!$info) return false;
        if($time < $info['start_time']) return false;
        if($time > $info['end_time']) return false;
        return true;
    }

    private function canStoreScore($info, $time){
        if(!$info) return false;
        if($time < $info['game_start_time']) return false;
        if($time > $info['game_end_time']) return false;
        return true;
    }
}