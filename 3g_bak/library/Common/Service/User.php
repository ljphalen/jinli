<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

//用户中心公用类
class Common_Service_User {

    //金币日志类
    public static function scorelog($options) {
        self::score($options);
    }

    //站内信
    public static function innerMsg($options) {

    }

    /**
     * 用户等级称谓
     *
     * @param int $level     用户当前等级
     * @param int $levelType 用户当前升级类别
     *
     * @return string 称谓
     */
    public static function getUserLevelByLevel($level, $levelGroup = 1) {
        if (!is_numeric($level)) return false;
        $levelConfig = self::getConfigByType('userConfig', 'rank', $levelGroup);
        return $levelConfig[$level];
    }

    /**
     *
     * @param int    $score     用户所获得的金币数
     * @param int    $curLevel  用户等级
     * @param number $levelType 升级类别
     *                          用户金币变化后，要检测是否等级提升
     */
    public static function updateUserLevelByScore($uid, $score, $curLevel, $levelGroup = 1) {
        $levelConfig = self::getConfigByType('userConfig', 'rank', $levelGroup);
        $scoreRange  = explode('-', $levelConfig[$curLevel]['range']);
        if ($score >= $scoreRange[1]) { //如果总金币大于当前等级最大金币数，就升一级
            $res = Gionee_Service_User::updateUser(array('user_level' => $curLevel + 1), $uid);
        }
    }

    /**
     *
     * @param unknown $filename  配置文件名
     * @param unknown $type      类型
     * @param unknown $levelType 分组（如果有的话）
     *
     * @return number
     */
    public static function  getConfigByType($filename, $type, $groupType = 0) {
        $config    = Common::getConfig($filename, $type);
        $levelData = $config[$groupType] ? $config[$groupType] : $config;
        return $levelData;
    }

    /**
     * 获取城市信息
     */
    public static function getAreaDataByParentId($parent_id = 0) {
        $key      = "USER:AREA:LIST:" . $parent_id;
        $dataList = Common::getCache()->get($key);
        if (empty($dataList)) {
            $dataList = Gionee_Service_Area::getListByParentId($parent_id);
            Common::getCache()->set($key, $dataList, 24 * 3600);
        }
        return $dataList;
    }
    //用户赚取金币相关操作
    /**
     *
     * @param int    $uid        用户ID
     * @param int    $goup_id    分组信息（1:签到，2：生产金币类商品）
     * @param number $cat_id
     * @param number $goods_id
     * @param number $action_type
     * @param int    $scores     返回获得的金币数
     * @param int    $score_type 金币类型
     */
    public static function scoreVarify($params = array()) {
        if (empty($params)) {
            $params = array(
                'uid'         => 0,
                'group_id'    => 1,
                'cat_id'      => 0,
                'goods_id'    => '0',
                'user_level'  => '1',
                'level_group' => '1',
                'act_id'      => '1',
                'score'       => '0',
                'score_type'  => 101
            );
        }
        $totalIncreScores = 0;//单次总赚取的金币数
        $userScoreInfo    = User_Service_Gather::getInfoByUid($params['uid']);
        if (empty($userScoreInfo)) {
            return array('key' => '-2', 'msg' => '用户信息有误!');
        }
        //用户等级检测
        $scores                   = array();
        $ret                      = $earnLog = $verifyLog = false;
        $data                     = self::_checkUserRewardGoods($params);//是否有额外的奖劢
        $totalIncreScores         = $data['sum'];
        $scores['total_score']    = $userScoreInfo['total_score'] + $data['sum'];
        $scores['remained_score'] = $userScoreInfo['remained_score'] + $data['sum'];
        $ret                      = User_Service_Gather::update($scores, $userScoreInfo['id']); //更新用户汇总信息表
        if ($ret) {
            User_Service_Gather::getInfoByUid($params['uid'], true);
            $earnLog = self::_addEarnScoresLogInfo($params, $data['verifyScore']);
            self::_addUserScoreVierfyLog($params, $data, $userScoreInfo['remained_score']);//写金币变动日志
        }
        //如果用户金币满足升级条件，就给其升级
        if ($ret && $earnLog) {
            return $totalIncreScores;
        }
        return 0;
    }

    //检测是否是用户权限商品，并返回相应等级所能得到的金币数
    private static function _checkUserRewardGoods($params = array()) {
        if (!is_array($params)) return false;
        $rewards   = 0;//连续操作N天时送的奖励
        $rewards2  = 0; //单天操作达到N个数量时送的奖励
        $varifyNum = $params['score'];//默认会传入赚取的金币数
        //首先查看是否是等级商品以及是否设置该分类物品完成任务时的奖励信息
        $privilegeInfo = self::getCateAndGoodsLevelInfo($params['group_id'], $params['cat_id'], $params['goods_id'], $params['user_level'], $params['level_group']);
        if (!empty($privilegeInfo)) {
            $varifyNum = max($varifyNum, $privilegeInfo['scores']);//等级金币
            $operated  = User_Service_Rewards::getRwardGoodsInfo(array(
                'uid'      => $params['uid'],
                'group_id' => $params['group_id'],
                'cat_id'   => $params['cat_id'],
                'goods_id' => $params['goods_id']
            ));
            if ($operated) {
                if (date('Ymd', $operated['last_time']) == date('Ymd', strtotime('-1 day'))) { //是否是连续操作
                    if (($operated['continus_days'] + 1) % $privilegeInfo['days'] == 0) { //连续操作达指定天投数时的赠送金币
                        $rewards = $privilegeInfo['rewards'];
                    }
                    $operated['continus_days'] += 1;
                } else {
                    $operated['continus_days'] = 1;//间断时，重新开始
                }
                //针对单日次数的奖励检测
                if (date('Ymd', $operated['get_rewards_time']) != date('Ymd', time()) && $operated['get_day_rewards'] == '1') { //隔天应把单天已领取标识归0
                    $operated['get_day_rewards'] == '0';
                }
                if ($privilegeInfo['times'] > 0 && $operated['get_day_rewards'] == '0') {
                    $countParams = array(
                        'uid'      => $params['uid'],
                        'group_id' => $params['group_id'],
                        'cat_id'   => $params['cat_id'],
                        'add_time' => array(array('>=', mktime(0, 0, 0)), array('<=', mktime(23, 59, 59)))
                    );
                    $count       = User_Service_Earn::count($countParams);
                    if ($count + 1 >= $privilegeInfo['times']) {
                        $rewards2                     = $privilegeInfo['rewards2'];
                        $operated['get_day_rewards']  = 1; //标为已领取
                        $operated['get_rewards_time'] = time();
                    }
                }
                $operated['last_time'] = time();
                User_Service_Rewards::edit($operated, $operated['id']);//更新记录
            } else {
                //若是首次，就添加
                $addParams = array(
                    'uid'           => $params['uid'],
                    'group_id'      => $params['group_id'],
                    'cat_id'        => $params['cat_id'],
                    'goods_id'      => $params['goods_id'],
                    'last_time'     => time(),
                    'continus_days' => 1,
                );
                if ($privilegeInfo['days'] == 1) {
                    $rewards = $privilegeInfo['rewards'];
                }
                if ($privilegeInfo['times'] == 1) {
                    $rewards2                      = $privilegeInfo['rewards2'];
                    $addParams['get_rewards_time'] = time();
                    $addParams['get_day_rewards']  = 1;
                }
                User_Service_Rewards::add($addParams);
            }
        }
        return array(
            'verifyScore' => $varifyNum,
            'rewards'     => $rewards,
            'rewards2'    => $rewards2,
            'sum'         => ($varifyNum + $rewards + $rewards2)
        );
    }
    
    //
    /**
     * 金币日志
     *
     * @param array $userMsg 用户信息
     * @param array $params  要改变的金币信息
     *                       ＠param  int                         金币类型
     */
    public static function score($params = array()) {
        if (!is_array($params)) return false;
        $keys = array_keys($params[0]);
        return User_Service_ScoreLog::add($keys, $params);
    }

    //得到用户金币
    /**
     * public static function  getUserScore($uid) {
     * return User_Service_Gather::getInfoByUid($uid);
     * //return User_Service_Gather::getBy(array('uid' => $uid));
     * }
     **/
    //得到用户等级称谓
    public static function getUserLevelInfo($uid, $level, $levelGroup) {
        $rs       = Common::getCache();
        $levelKey = "USER:LEVEL:" . $uid;
        $levelMsg = $rs->get($levelKey);
        if (empty($levelMsg)) {
            $levelMsg = self::getUserLevelByLevel($level, $levelGroup);
            $rs->set($levelKey, $levelMsg, 300);
        }
        //下一个等级的信息
        $nextLevel = self::getUserLevelByLevel($level + 1, $levelGroup);
        return array('curLevelMsg' => $levelMsg, 'nextLevelMsg' => $nextLevel);
    }

    //是否有未读的站内信
    public static function unReadMsgNumber($uid,$sync=false) {
    	$key = "User_Unread_Msg_Num_$uid";
    	$num = Common::getCache()->get($key);
    	if($num === false || $sync){
    		$num = User_Service_InnerMsg::unReadMsgNumber(array('is_read' => 0, 'uid' => $uid));
    		Common::getCache()->set($key,$num,300);
    	}
        return  $num;
    }

    /**
     * 检测用户登陆情况
     *
     * @param string $callback_url 返回跳转链接
     * @param bool   $redirect     是否直接跳转
     *
     * @return array
     */
    public static function checkLogin($callback_url = '', $redirect = false,$testMobile ='') {
        if (empty($callback_url)) {
            $callback_url = Common::getCurUrl();
        }
        if(empty($testMobile)){
            $userInfo = Gionee_Service_User::getCurUserInfo();
        }else{
            $userInfo = Gionee_Service_User::getCurUserInfoForTest($testMobile);
        }
        if (empty($userInfo['id'])) {
            $callback = sprintf('%s/user/login/login', Common::getCurHost());
            $url      = Api_Gionee_Oauth::requestToken($callback);
            Util_Cookie::set('GIONEE_LOGIN_REFER', $callback_url, true, Common::getTime() + (5 * 3600), '/');
            if ($redirect) {
                Common::redirect($url);
            }
            return array('key' => '0', 'keyMain' => $url);
        }
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        if (empty($userScoreInfo)) {
            User_Service_Gather::add(array('uid' => $userInfo['id']));
        }
        return array('key' => '1', 'keyMain' => $userInfo);
    }

    
    /**
     * 用户完成任务时获得金币
     * @param $uid  用户ID
     * @param $group_id  分组ID
     * @param $cat_id 		分类ID
     * @param goods_id	物品ID
     * @param $scores 	获得金币数
     * @param $score_type	 获得金币和任务类型
     */
    public static function earnScoresByTask($uid=0,$group_id=0,$cat_id=0,$goods_id=0,$scores=0,$score_type='101'){
    	
    	Common_Service_Base::beginTransaction();
    	//写获取日志
    	$earnParams = array(
    		'uid'=>$uid,
    		'group_id'=>$group_id,
    		'cat_id'		=>$cat_id,
    		'goods_id'	=>$goods_id,
    		'score'=>$scores,
    		'add_time'	=>time(),
    		'user_ip'				=>Util_Http::getClientIp(),
    		'add_date'			=>date('Ymd',time()),
    	);
    	$earnLog = User_Service_Earn::add($earnParams);
    	//账号增加金币
    	$userScores = User_Service_Gather::getBy(array('uid'=>$uid));
    	if(empty($userScores)){
    		$id = User_Service_Gather::add(array('uid'=>$uid,'create_time'=>date("Y-md",time())));
    		$userScores = User_Service_Gather::get($id);
    	}
    	$updateParams = array(
    		'total_score'		=>$userScores['total_score'] + $scores,
    		'remained_score'=>$userScores['remained_score']+$scores,
    	);
    	$upResult = User_Service_Gather::update($updateParams,$userScores['id']);
    	
    	//写金币变动日志
    	$verifyParams = array(
    		'uid'						=>$uid,
    		'group_id'			=>$group_id,
    		'score_type'		=>$score_type,
    		'before_score'	=>$userScores['remained_score'],
    		'after_score'		=>$updateParams['remained_score'],
    		'affected_score'	=>$scores,
    		'add_time'			=>time(),
    		'date'					=>date('Ymd',time()),
    		'fk_earn_id'		=>$goods_id,
    	);
    	$verifyLog = User_Service_ScoreLog::insert($verifyParams);
    	if($earnLog && $upResult && $verifyLog){
    		Common_Service_Base::commit();
    		return true;
    	}else{
    		Common_Service_Base::rollBack();
    		return false;
    	}
    }
    
    /**
     * 连续签到记录
     * @param number $uid
     * @param number $groupId
     * @param number $cat_id
     * @param number $good_id
     * @return boolean
     */
    public static  function updateContinusSignLog($uid=0,$groupId=0,$cat_id=0,$good_id=0){
    	$params = array(
    			'uid'      => $uid,
    			'group_id' => $groupId,
    			'cat_id'   =>$cat_id,
    			'goods_id' => $good_id,);
    	$data = User_Service_Rewards::getRwardGoodsInfo($params);
    	if(empty($data)){
    		$params['continus_days'] = 1;
    		$params['last_time'] = time();
    		return User_Service_Rewards::add($params);
    	}
    	if (date('Ymd', $data['last_time']) == date('Ymd', strtotime('-1 day'))) { //是否是连续操作
    		$data['continus_days'] += 1;
    	} else {
    		$data['continus_days'] = 1;//间断时，重新开始
    	}
    	$data['last_time'] = time();
    	return User_Service_Rewards::edit($data, $data['id']);//更新记录
    }
    /**
     * 返还冻结金币数
     *
     * @param int    $uid        用户ID
     * @param int    $orderId    订单ID
     * @param int    $score_type 金币变动的原因
     * @param string $flag       加减金币的标识
     */
    public static function changeUserScores($orderId = 0, $score_type = 0, $flag = '+') {
        if (empty($orderId) || empty($score_type)) return false;
        $orderMsg = User_Service_Order::get($orderId);
        if (!$orderMsg) return false;
        $uid            = $orderMsg['uid'];
        $affectedScores = $orderMsg['total_cost_scores']; //受影响的金币数
        $userScores    = User_Service_Gather::getInfoByUid($uid);
        $reminedScores = $userScores['remained_score'];
        if ($flag == '+') {
            $userScores['remained_score'] += $affectedScores;
        }
        $userScores['frozed_score'] -= $affectedScores;
        $userScores['affected_score'] = $affectedScores;
        $res                          = User_Service_Gather::updateBy($userScores, array('uid' => $uid)); //更新用户金币表
        User_Service_Gather::getInfoByUid($uid, true);
        if ($res && $flag == '+') {
            $data[] = array(
                'uid'            => $uid,
                'group_id'       => 3,
                'before_score'   => $reminedScores,
                'after_score'    => $userScores['remained_score'],
                'score_type'     => $score_type,
                'affected_score' => $affectedScores,
                'add_time'       => time()
            );
            return Common_Service_User::score($data); //写日志
        }
        return true;
    }

    //发送站内信
    public static function sendInnerMsg($data, $type = '') {
        $tmpMsg = User_Service_InnerMsg::getTplData($type);
        if (empty($tmpMsg)) return;
        $arrMsg  = explode('|', $tmpMsg);
        $realMsg = $data['status'] == -1 ? $arrMsg[1] : $arrMsg[0];
        foreach ($data as $k => $v) {
            $realMsg = str_replace("#{$k}#", $v, $realMsg);
        }

        $params = array(
            'uid'      => $data['uid'],
            'type'     => $data['classify'],
            'content'  => $realMsg,
            'status'   => $data['status'] ? $data['status'] : 1,
            'is_read'  => 0,
            'add_time' => time(),
        );
        return User_Service_InnerMsg::add($params);
    }


    /**
     * 用户赚取金币的日志
     * ＠params $params 基本信息
     * ＠params $scores  变化的金币数
     */
    private static function _addEarnScoresLogInfo($params = array(), $scores = 0) {
        if (empty($params)) return false;
        $options = array(
            'uid'      => $params['uid'],
            'group_id' => $params['group_id'],
            'cat_id'   => $params['cat_id'],
            'goods_id' => $params['goods_id'],
            'add_time' => time(),
            'score'    => $scores
        );
        return User_Service_Earn::add($options); //添加到赚取金币的商品日志表中
    }

    /**
     * 记录用户金币日志（如果是规则物品，则需要考虑满足条件后的奖励信息）
     *
     * @param $params    array            用户及物品的基本信息
     * @param $scores    array                等级商品的奖励金币信息
     * @param $reScores  int                用户剩余可用金币数
     *                   ＠ return BOOL
     *
     */

    private static function _addUserScoreVierfyLog($params = array(), $scores = array(), $reScores = 0) {
        if (empty($params) || empty($scores) || empty($scores['verifyScore'])) return false;
        $p           = array();
        $beforeScore = $reScores;
        $afterScore  = $reScores + $scores['verifyScore'];
        $element     = array(
            'uid'            => $params['uid'],
            'group_id'       => $params['group_id'],
            'score_type'     => $params['score_type'],
            'before_score'   => $beforeScore,
            'after_score'    => $afterScore,
            'affected_score' => $scores['verifyScore'],
            'add_time'       => time(),
            'date'           => date('Ymd', time()),
            'fk_earn_id'     => $params['goods_id']
        );
        array_push($p, $element);
        $beforeScore = $afterScore;
        if ($scores['rewards']) {
            $afterScore += $scores['rewards'];
            $element1 = array(
                'uid'            => $params['uid'],
                'group_id'       => $params['group_id'],
                'score_type'     => 201,
                'before_score'   => $beforeScore,
                'after_score'    => $afterScore,
                'affected_score' => $scores['rewards'],
                'add_time'       => time(),
                'date'           => date('Ymd', time())
            );
            array_push($p, $element1);
        }
        $beforeScore = $afterScore;
        if ($scores['rewards2']) {
            $afterScore += $scores['rewards2'];
            $element2 = array(
                'uid'            => $params['uid'],
                'group_id'       => $params['group_id'],
                'score_type'     => 202,
                'before_score'   => $beforeScore,
                'after_score'    => $afterScore,
                'affected_score' => $scores['rewards2'],
                'add_time'       => time(),
                'date'           => date('Ymd', time())
            );
            array_push($p, $element2);
        }
        return self::score($p);
    }

    /**
     * 获取等级商品及等级分类信息
     * 默认对单个商品查看，再查看该商品分类是否有设置等级信息
     *
     * @param $group_id          int 物品类别
     * @param $cat_id            int        分类IID
     * @param $goods_id          int    物品ID
     * @param $user_level        用户等级
     * @param $level_group       等级类别
     */
    public static function getCateAndGoodsLevelInfo($group_id = '', $cat_id = 0, $goods_id = 0, $user_level = 1, $level_group = 1) {
        $params                = array();
        $params['group_id']    = $group_id;
        $params['cat_id']      = $cat_id;
        $params['goods_id']    = $goods_id;
        $params['user_level']  = $user_level;
        $params['level_group'] = $level_group;
        $res                   = User_Service_Uprivilege::getBy($params);//针对单个商品
        if (empty($res)) {
            if ($group_id == '1') { //如果是签到，没指定分组和等级时
                $res = User_Service_Uprivilege::getBy(array(
                    'group_id'    => $group_id,
                    'level_group' => 0,
                    'user_level'  => 0
                ));
            } else {
                unset($params['goods_id']);
                $res = User_Service_Uprivilege::getBy($params);
            }
        }
        return $res;
    }

    //通过页面类别获得广告
    public static function getAdsByPageType($pname, $type = 1) {
        if (!$pname) return false;
        $config     = Common::getConfig('userConfig');
        $prefix     = $config['pos_identifiers'][$pname]['prefix'];
        $identifier = $prefix . $config['ads_type'][$type]['tag'];
        $posInfo    = Gionee_Service_Position::getBy(array('identifier' => $identifier));
        if (!$posInfo) return false;
        $key = '3G:USER:ADS:' . $pname;
        $ads = Common::getCache()->get($key);
        if (empty($ads)) {
            $params                = array();
            $params['position_id'] = $posInfo['id'];
            $params['status']      = 1;
            $params['start_time']  = array('<=', Common::getTime());
            $params['end_time']    = array('>=', Common::getTime());
            $ads                   = Gionee_Service_Ads::getsBy($params, array('sort' => 'DESC', 'id' => 'DESC'));
            Common::getCache()->set($key, $ads, 60);
        }
        return $ads;
    }

    /**
     * 获到用户连续签到单次实际得到的金币数
     */
    public static function getFinalScores($uid) {
        $signConfig = Common::getConfig('userConfig', 'signin');
        $scores     = $signConfig['scores'];
        if (intval($uid)) {
            $userMsg = User_Service_Rewards::getBy(array('uid' => $uid, 'group_id' => 1));
            if ($userMsg['continus_days'] > 0) {
                $params['3g_key'] = array('IN', array('user_signin_step', 'user_signin_per_max'));
                $signConfig       = Gionee_Service_Config::getsBy($params);
                $temp             = array();
                foreach ($signConfig as $k => $v) {
                    $temp[$v['3g_key']] = $v['3g_value'];
                }
                $scores = min($temp['user_signin_per_max'], (($userMsg['continus_days']) * $temp['user_signin_step'] + $scores));
            }
        }
        return $scores;
    }

    /**
     * 存储当前URL
     */
    public static function setReqeustUri($url = '') {
        if (empty($url)) {
            $url = Common::getCurUrl();
        }
        Util_Cookie::set('USER_REQUEST_URI', $url, true, Common::getTime() + (5 * 3600), '/');
    }

    /**
     * 控制用户每次请求频率
     * @return bool true正常|false过快
     */
    public static function checkFreq($uid, $t = 3) {
        $rcKey    = 'USER_FREQ:' . intval($uid);
        $now      = time();
        $prevTime = Common::getCache()->get($rcKey);
        if ($prevTime > $now) {
            return false;
        }
        Common::getCache()->set($rcKey, $now + $t);
        return true;
    }

    /**
     * 得到随机数
     */
    public static function getRangeData($params) {
        $max        = array_sum(array_values($params));
        $randNumber = mt_rand(1, $max);
        $n          = 0;
        foreach ($params as $k => $v) {
            $n += $v;
            if ($randNumber <= $n) {
                return $k;
            }
        }
    }

    /**
     * 增加经验值并写日志
     *
     * @param int  $uid            用户ID
     * @param int  $curLevel       当前用户经验等级
     * @param  int $type           获得经验值的活动类型
     * @param  int $affectedPoints 获得的经验值
     * @param int  $msgType        站内信类型
     */
    public static function increExperiencePoints($uid = 0, $curLevel = 1, $type = 1, $affectedPoints = 1, $msgType = 7, $goodsId = 0) {
        //增加经验值
        $userScores  = User_Service_Gather::getBy(array('uid' => $uid));
        $totalPoints = $userScores['experience_points'] + $affectedPoints;

        $ret = User_Service_Gather::update(array('experience_points' => $totalPoints), $userScores['id']);
        //写日志
        $params = array(
            'uid'     => $uid,
            'type'    => $type,
            'points'  => $affectedPoints,
            'msgType' => $msgType,
            'gid'     => $goodsId
        );
        User_Service_ExperienceLog::writeLog($params);
        self::upgradeExperienceLevel($uid, $curLevel, $totalPoints, 6);        //检测用户升级情况
    }

    /**
     * 更新用户等级
     *
     * @param int $uid
     * @param int $curLevel 当前等级
     * @param int $points   经验值
     * @param int $msgType  站内信内型
     */
    public static function upgradeExperienceLevel($uid, $curLevel, $points, $msgType) {
        $isUpgrade = self::checkUserExperenceLevel($curLevel, $points);
        if ($isUpgrade) {
            $newLevel = $curLevel + 1;
            self::upgradeLevelAndSendMsg($uid, $newLevel, $msgType);
            Gionee_Service_User::getCurUserInfo(true);

            $params['uid']       = $uid;
            $params['old_level'] = $curLevel;
            $params['new_level'] = $newLevel;
            $params['add_time']  = time();
            $params['date']      = date('Y-m-d', time());
            $ret                 = User_Service_ExperienceLevelLog::add($params);
        }
    }


    public static function checkUserExperenceLevel($curLevel, $points) {
        $config = Common::getConfig('userConfig', 'experience_level_data');
        $range  = explode('-', $config[$curLevel]['range']);
        return $points > $range[1];
    }

    /**
     * 用户经验等级升级,并发送站内信
     *
     * @param int $uid
     * @param int $newLevel
     * @param int $MsgType
     */
    public static function upgradeLevelAndSendMsg($uid, $newLevel, $MsgType) {
        $ret      = Gionee_Service_User::updateUser(array('experience_level' => $newLevel), $uid);//增加升级
        $msg      = '';
        $levelMsg = User_Service_ExperienceInfo::getInitLevelData($newLevel);
        foreach ($levelMsg as $k => $v) {
            $catData = User_Service_ExperienceType::get($v['cat_id']);
            $msg .= sprintf($catData['name'], $v['num']);
            $msg .= ",";
            if ($catData['type'] == 3) {
                $user    = Gionee_Service_User::getUser($uid);
                $seconds = User_Service_ExperienceInfo::getVoipLevelSeconds($user['experience_level'], $v['num']);
                Gionee_Service_VoIPUser::addSecondsToUserAccount($user['id'], $user['username'], $seconds);
                Gionee_Service_VoIPUser::getInfoByPhone($user['username'], true);
            }
        }
        $data = array(
            'msg'      => substr($msg, 0, -1),
            'level'    => $newLevel,
            'status'   => 1,
            'uid'      => $uid,
            'classify' => $MsgType,
        );
        self::sendInnerMsg($data, 'experience_level_upgrade_tpl');//升级信息
    }
    
    
    
    public  static  function validUsername($username,$callback,$type=3){
    	$validPhone 	  = Common::checkIllPhone($username);
    	if(!$validPhone){
    		$callback = sprintf("%s%s", Common::getCurHost(), $callback);
    		$url      = sprintf("%s/user/index/bindphone?f=%d&redirect=%s", Common::getCurHost(), $type, $callback);
    		return $url;
    	}
    	return '';
    }
    
    public static function  curl($params=array(),$url=''){
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	$output  = curl_exec($ch);
    	curl_close($ch);
    	return json_decode($output,true);
    }
}
	