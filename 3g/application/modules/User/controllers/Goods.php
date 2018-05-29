<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 金币兑换类（如兑换话费／流量等）
 * @author panxb
 *
 */
class GoodsController extends User_BaseController {
    public $pageSize = 5;

    //生产金币物品列表
    public function producesAction() {
    	$postData = $this->getInput(array('cat_id', 'page', 'name'));
    	
        $this->statProduce ();
        $this->checkPostData ( $postData );
        $page    = $this->getPage ( $postData );
        $imgPath = Common::getImgPath();
        
        Common_Service_User::setReqeustUri("/user/goods/produces?cat_id={$postData['cat_id']}");
        
        list($total, $produceList) = $this->getProduceList ($postData);
        $hasNext = $total > ($page * $this->pageSize) ? true : false;
        
        if($postData['cat_id'] == 8){
        	$produceList = $this->fillBaiduAdImageToProduceList($produceList);
        }       
       
        $wmStatus = Gionee_Service_Config::getValue('user_wangmeng_status');//网盟状态
        $wmflag   = 0;
           
        $userInfo = Gionee_Service_User::getCurUserInfo();
        $experienceLevel = $this->getUserExperienceLevel ( $userInfo );
        $privilegeNum = $this->getUserPrivilegeNum($userInfo);
        if (!empty($userInfo)) { //用户登陆后才能看到相关信息
            $uid           = $userInfo['id'];   
            $userScoreInfo = User_Service_Gather::getInfoByUid($uid);
            $cateInfo      = User_Service_Category::get($postData['cat_id']);
            $userGainGoodsIds = $this->getUserGainGoodsIds ($uid);      
            $produceList  = $this->fillGainGoodsStatus($produceList, $userGainGoodsIds);
            $wmflag       = $this->getWmFlag ($userGainGoodsIds );
        }
               
        if($page > 1) { //page 大于1 时为ajax获取数据 否则为直接渲染视图
            $this->output('0', '', array(
                'list'       => $produceList,
                'hasNext'    => $hasNext,
                'cat_id'     => $postData['cat_id'],
                'score_type' => $cateInfo['score_type'],
                'attachPath' => $imgPath,
            	'wmstatus'   =>$wmStatus,
                'wmflag'     => $wmflag,
            ));
        } else {
            $ads = $this->getAds ($cateInfo );
            $this->assign('more', $hasNext);
            $this->assign('userScores', $userScoreInfo);
            $this->assign('cat_id', $postData['cat_id']);
            $this->assign('catInfo', $cateInfo);
            $this->assign('list', $produceList);
            $this->assign('wmflag', $wmflag);
            $this->assign('wmstatus', $wmStatus);
            $this->assign('wordAds', $ads);
            $this->assign('privilegeNum', $privilegeNum);
            $this->assign('eLevel', $experienceLevel);
        }
    }
    
    private function fillBaiduAdImageToProduceList($produceList){
    	
    	$config = Common::getConfig('baiduAdConfig');
    	header("Content-type:text/html;charset=utf-8");
    	$uaArr  = Util_Http::ua(); 	
    	//$clientIp = Util_Http::getClientIp();
    	//$requestData = array();
    
    	
    	$requestData['request_id']='cNTYGlo9oMtpyl3cD8R3WGV7olbehq41';
    	$requestData['api_version'] = array('major'=>5,
    			                            'minor'=>0);
    	$requestData['app']['app_id']= 'f09880d0';
    	$requestData['app']['app_version'] =  array('major'=>4,
    			                                    'minor'=>2);
    	$requestData['device']['os_version'] =  array('major'=>4,
    												  'minor'=>2,
    												'micor'=>2);
    	$requestData['device']['screen_size'] =  array('width'=>1080,
    												  'height'=>1920);
    	$requestData['device']['vendor'] ='MEIZU';
    	$requestData['device']['model']  ='MX5';
    	$requestData['device']['udid']  = array('mac'=>'12:34:56:78:90:ab',
    			                                'android_id'=>'TestAndroidId123'
    			                                );
    	$requestData['device']['device_type']  =1;
    	$requestData['device']['os_type']  =1;
    	
    	$requestData['network']['ipv4']  ='113.108.119.16';
    	$requestData['network']['connection_type']  =0;
    	$requestData['network']['operator_type']  =0;
    	
    	$requestData['adslot']['adslot_id']  ='2084150';
    	$requestData['adslot']['adslot_size']  = array('width'=>350,
    												  'height'=>250);
    	
    	
    	$jsonData = json_encode($requestData);
    	echo $jsonData;
    	$response = Util_Http::post($config['url'], 
    			$jsonData, 
    			array('Content-Type' => 'application/json'));
    	var_dump($config['url'], $response);die;
    	
    	
    	foreach ($produceList as $key=> $val){
    		$requestData = $this->fillBaiduAdRequestData ( $config, $uaArr, $val);
    		$response = Util_Http::post($config['url'], json_encode($requestData), array('Content-Type' => 'application/json'));
    		$result = json_decode($response->data, true);
    		$produceList[$key]['link']=$result['ads']['click_url'];
    		$produceList[$key]['image']=$result['ads']['show_url'];
    	}   	
    	return $produceList;
  	
    }
    
	private function fillBaiduAdRequestData($config, $uaArr, $ads) {
		$requestData['request_id']  = md5($ads['id']);
		$requestData['api_version'] = $config['api_version'];
		$requestData['app']['app_id']   = $config['app_id'];
		$requestData['app']['app_version'] = array('major'=>$uaArr['ua']['app_ver'],
											       'minor'=>$uaArr['ua']['app_ver']
												  );   
		$requestData['network']['ipv4']     =$uaArr['ip'];
		$requestData['network']['connection_type']     =0;
		$requestData['network']['operator_type']       =0;
		$requestData['device']['os_version']=$config['os_version'];
		$requestData['device']['screen_size']    =array('width'=>1080,
				                       		 		    'height'=>1920
				                       		 		 );
		$requestData['device']['vendor']    ='goinee';
		$requestData['device']['model']     =$uaArr['ua']['model'];
		$requestData['device']['udid']      = array('imei'=> $uaArr['ua']['uuid'],	                             
													'mac'=>'12:34:56:78:90:ab',
				                                    'android_id'=>'TestAndroidId123'
													);
		$requestData['device']['device_type']      = 1;
		$requestData['device']['os_type']        = 1;
		$requestData['adslots'] = array(
				                       array('adslot_id'=>$ads['out_ad_id'],
				                       		 'adslot_size'=> array('width'=>350,
				                       		 		               'height'=>250
				                       		 		 )
				                       		)
				);
		return $requestData;
	}

        
	private function getAds($cateInfo) {
		if (!empty($cateInfo['ext'])) {
		    $ext = json_decode($cateInfo['ext'], true);
		    $ads = Gionee_Service_Ads::getsBy(array('id' => $ext['ad_id']));
		} else {
		    $ads = Common_Service_User::getAdsByPageType('produce', '2');
		}
		return $ads;
	}
    
    private function fillGainGoodsStatus($produceList, $userGoodsIds){
    	foreach ($produceList as $k => $v) {
    		$produceList[$k]['get'] = in_array($v['id'], $userGoodsIds) ? 1 : 0;
    	}
    	return $produceList;
    }

    
    private function getWmFlag($userGoodsIds) {
    	if(!is_array($userGoodsIds)){
    		return 0;
    	}
		if (in_array('-1', $userGoodsIds)) {
		    return 1;
		}
		
    }

    
    private function getUserPrivilegeNum($userInfo) {
    	$privilegeNum = 0 ;
    	if($userInfo['experience_level']){
    		$privilegeNum = User_Service_ExperienceInfo::getLevelRewardsData($userInfo['experience_level'], 1);
    	}
    	return $privilegeNum;
    }
    
	private function getUserExperienceLevel($userInfo) {
		$experienceLevel = 0 ;
		if($userInfo['experience_level'] ){
			$experienceLevel = $userInfo['experience_level'];
		}
		return $experienceLevel;
	}
	
	private function getUserGainGoodsIds($uid) {
		$params ['uid']     = $uid;
		$params['group_id'] = 2;
		$params['add_time'] = array(array('>=', mktime(0, 0, 0)), array('<=', mktime(23, 59, 59)));
		$goodsIds           = User_Service_Earn::getDayEarnedGoodsIds($params);    //查询用户已領取的商品ID
		return $goodsIds;
	}
	
	private function getProduceList($postData, $page) {
		$params['cat_id']     = $postData['cat_id'];
        $params['status']     = User_Service_Produce::PRODUCE_OPEN_STATUS;
        $params['start_time'] = array('<=', time());
        $params['end_time']   = array('>=', time());
        $orderBy                = array('sort' => 'DESC', 'id' => 'DESC');
        list($total, $list) = User_Service_Produce::getList($page, $this->pageSize, $params, $orderBy);
	    return array($total, $list);
	}
    
	private function getPage($postData) {
		return max($postData['page'], 1);
	}

	private function checkPostData($postData) {
		if (!intval($postData['cat_id'])){
			Common::redirect('/user/index/index');
		} 
	}

		
	private function statProduce() {
		Gionee_Service_Log::pvLog('user_produces');
        Gionee_Service_Log::uvLog('user_produces', $this->getSource());
	}


 //签到
    public function signAction(){
    	$this->_checkUser();
    	$userInfo = Gionee_Service_User::getCurUserInfo();
    	$stime = mktime(0,0,0);
    	$etime = mktime(23,59,59);
    	$signData = User_Service_Earn::getSignInfo($stime, $etime, $userInfo['id'],true);	
    	if(!empty($signData)){
    		$this->output('-1','您今天已经完成签到，请明天再来！');
    	}
    	$earned           = Common_Service_User::getFinalScores($userInfo['id']);
 		$ret = Common_Service_User::earnScoresByTask($userInfo['id'],1,0,0,$earned);
 		if($ret){
 			Common_Service_User::updateContinusSignLog($userInfo['id'],1);
 			User_Service_Gather::getInfoByUid($userInfo['id'],true);
 			User_Service_Earn::getSignInfo($stime, $etime, $userInfo['id'],true);	
 			$this->output('0','签到成功',array('increScores'=>$earned));
 		}else{
 			$this->output('-1','签到失败!',array('increScores'=>0));
 		}
    }
    /**
     * 用户赚取金币的动作（包括打卡，看图等)
     */
    public function earnAction() {
        $this->_checkUser();
        $postData    = $this->getInput(array('goods_id', 'score_type', 'cat_id'));
        if(!intval($postData['goods_id']) || !intval($postData['score_type'])){
        	$this->output('-1','参数有错');
        }
        $goods = $this->_getProduceGoodsInfo($postData['goods_id']);
        if(empty($goods)){
        	$this->output('-1','物品不存在');
        }
        $now = time();
        if(empty($goods['status']) || ($goods['start_time']>$now || $goods['end_time']< $now)){
        	$this->output('-1','已下线');
        }
        Gionee_Service_Log::addPVUVData(Gionee_Service_Log::TYPE_U_G_IMG, Gionee_Service_Log::TYPE_U_G_IMG_UV, $postData['goods_id']);
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_U_G_IMG_UV, $postData['goods_id'], $this->getSource());
        $catId = $goods['cat_id'];
        if($postData['goods_id'] =='-1'){ //网盟广告时,goods_id 为-1
        	$catId = $postData['cat_id'];
        }
        $userInfo = Gionee_Service_User::getCurUserInfo();
  		$earnParams = array();
  		$earnParams['uid'] = $userInfo['id'];
  		$earnParams['group_id'] = 2;
  		$earnParams['cat_id'] = $catId;
  		$earnParams['goods_id'] = $goods['id'];
        $earnParams['add_date'] = date('Ymd',time());
        $earnData = User_Service_Earn::getBy($earnParams);
        if(!empty($earnData)){
        	$this->output('-1','今天已领取该奖励!');
        }
        $redirectUrl =$goods['link'] ;
        $flag = $this->_canGetScores($userInfo['id'], $goods['cat_id'], $userInfo['experience_level'], 1);
        if (!$flag){
        	$this->output('0', '', array('redirect' => html_entity_decode($redirectUrl)));
        	exit();
        }
        $result = Common_Service_User::earnScoresByTask($userInfo['id'],2,$catId,$goods['id'],$goods['scores'],$postData['score_type']);
        if($result){
        	User_Service_Gather::getInfoByUid($userInfo['id'],true);
        	$this->output('0','',array('redirect'=>html_entity_decode($redirectUrl),'increScores'=>$goods['scores']));
        }else{
        	$this->output('-1','操作失败!');
        }
    }

    
    //消费金币物品列表
    public function listAction() {
        $postData = $this->getInput(array('page', 'cat_id'));
        Gionee_Service_Log::pvLog('user_cosume_list');
        Gionee_Service_Log::uvLog('user_cosume_list', $this->getSource());
        $page = max($postData['page'], 1);
        Common_Service_User::setReqeustUri('/user/goods/list');
        $userScoreInfo = array();
        $userInfo      = Gionee_Service_User::getCurUserInfo();
        if (!empty($userInfo)) {
            $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        }
        $params = array();
        if(intval($postData['cat_id'])){
        	$params['cat_id'] = $postData['cat_id'];
        }
       $goodsList = User_Service_Commodities::getGoodsList();
        $flag = 0;
        if (stristr(ENV, 'product')) {
            $flag = 1;
        }
        $list = $this->_getCategoryGoodsData($goodsList);
        $ads  = Common_Service_User::getAdsByPageType('cosume');
        $this->assign('ads', $ads);
        $this->assign('flag', $flag);
        $this->assign('userScores', $userScoreInfo);
        $this->assign('list', $list);
        $this->assign('refurl', Common::getRequestUrl());
    }

    
    private function _getCategoryGoodsData($data) {
        $list = array();
        if (empty($data)) return $list;
        foreach ($data as $k => $v) {
            if ($v['goods_type'] == 2) {
                $list['entites'][] = $v;
            } else {
                if ($v['virtual_type_id'] == 999) {
                    $list['calls'][] = $v;
                } else {
                    $list['virtuals'][] = $v;
                }
            }
        }
        return $list;
    }

    //检测用户当前可用金币是否足够兑换指定的商品
    public function  ajaxCheckScoreAction() {
        $login = Common_Service_User::checkLogin($this->_backUrl());
        if (!$login['key']) $this->output('0', '', array('redirect' => $login['keyMain']));
        $userInfo = $login['keyMain'];
        $phone    = $userInfo['username'];
        if (!empty($phone) && !Common::checkIllPhone($phone)) {
            $url = sprintf("%s/user/index/bindphone?f=%d", Common::getCurHost(), 1);
            $this->output('0', '', array('redirect' => $url));
        }
        $goods_id = $this->getInput('goods_id');
        $refurl   = $this->getInput('refurl');
        //查看是否为等级物品
        $goodsInfo = User_Service_Commodities::get($goods_id);
        self::_setPvUvData(Gionee_Service_Log::TYPE_U_G_SCORE, Gionee_Service_Log::TYPE_U_G_SCORE_UV, $goodsInfo['id']);
        if ($goodsInfo['number'] < 1) {
            $this->output('-1', '您要兑换的商品数量不足!');
        }
        //用户金币信息
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        $remined       = $userScoreInfo['remained_score'] - $goodsInfo['scores'];
        if ($remined >= 0) {
            $webroot = Common::getCurHost();
            $refurl  = $refurl ? $webroot . $refurl : $webroot . '/user/index/index';
            $this->output('0', '', array('redirect' => $webroot . "/User/goods/detail?goods_id={$goods_id}&refurl={$refurl}"));
        } else {
            $this->output('-1', '您的金币不够！');
        }
    }

    /**
     * 兑换详情页(虚拟商品）
     */
    public function detailAction() {
        $from = $this->getInput('from');
        if (!empty($from)) {
            $login = Common_Service_User::checkLogin('/event/seckill/index', true,$this->getInput('testMobile'));        //检测登陆状态
        }
        $login = Common_Service_User::checkLogin($this->_backUrl(),false,$this->getInput('testMobile'));
        if (!$login['key']) $this->output('0', '', array('redirect' => $login['keyMain']));
        $userInfo = $login['keyMain'];
        $postData = $this->getInput(array('goods_id','prize_id', 'refurl'));
        if (!intval($postData['goods_id'])) {
            exit('非法操作');
        };
        
        $goodsInfo = User_Service_Commodities::get($postData['goods_id']);
        $now = time();
        if( $now < $goodsInfo['start_time'] || $now > $goodsInfo['end_time']){
        	$this->output('-1','商品已下线');
        }
        if(empty($goodsInfo['event_flag'])){ //非特别物品时
        	if($goodsInfo['status'] == 0){
        		$this->output('-1','商品已下线');
        	}
        }
        $remainedSeconds = -1;
        $expiredTime = 0;
        if($goodsInfo['event_flag']){ //如果是活动商品
        		$this->_checkActivityStatus($userInfo['id'],$postData['prize_id'],$postData['goods_id']);
                $config = Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
                $type_id=$config['id'];
        		$expiredTime = $config['valid_minutes'];
        		$now = time();
        		$prizeInfo = Event_Service_Activity:: getUserPrizeById($userInfo['id'],$type_id,$postData['prize_id']);

        		$remainedSeconds = $prizeInfo['add_time']+ $config['valid_minutes']*60 - $now;
        }
        $this->_setPvUvData(Gionee_Service_Log::TYPE_U_G_DETAIL, Gionee_Service_Log::TYPE_U_G_DETAIL_UV, $goodsInfo['id']);
        Common_Service_User::setReqeustUri();
        if (!empty($goodsInfo['card_info_id'])) {
            $cardInfo = User_Service_CardInfo::get($goodsInfo['card_info_id']);
            $this->assign('exType', $cardInfo['group_type']);
        }
        if ($goodsInfo['goods_type'] == 2) {
            $this->assign('provinceList', Gionee_Service_Area::getProvinceList());
        }
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        $captchaid     = Gionee_Service_Config::getValue('geetest_captcha_id');
        $this->assign('captchaid', $captchaid);
        $this->assign('userScores', $userScoreInfo);
        $this->assign('goodsInfo', $goodsInfo);
        $this->assign('prize_id', $postData['prize_id']);
        $this->assign('userInfo', $userInfo);
        $this->assign('refurl', $postData['refurl']);
        $this->assign('from', $from);
        $this->assign('remainedSconds', $remainedSeconds);
        $this->assign('expiredTime', $expiredTime);
    }


    /**
     * 获得城市信息
     */
    public function ajaxGetCityListAction() {
        $pid = $this->getInput('province_id');
        if (empty($pid)) $this->output('-1', '请选择省份!');
        $data = Gionee_Service_Area::getListByParentId($pid);
        $this->output('0', '', $data);
    }

    /**
     * 金币兑换接口
     *
     * @param int $cat_id       分类ID
     * @param int $goods_id     物品ID
     * @param int $scores       默认所需消费金币数
     * @param int $goods_number 兑换物品数 (默认为1)
     * @param int $address_id   收贷地址ID （可为空)
     */
    public function exchangeAction() {
        $postData      = $this->getInput(array(
            'goods_id',
            'prize_id',
            'number',
            'inprice',
            'refurl',
            'goods_number',
            'geetest_challenge',
            'geetest_validate',
            'geetest_seccode'
        ));
        $user          = $this->_getUserInfoIfLogin();
        $flag          = $this->getInput('flag');
        $validPhoneNum = Common::checkIllPhone($user['username']); //用户名是否为手机号
        if ($user['is_black_user'] || !$validPhoneNum) {
            $callback = sprintf("%s/user/goods/detail?goods_id=%d&prize_id=%d", Common::getCurHost(), $postData['goods_id'], $postData['prize_id']);
            $url      = sprintf("%s/user/index/bindphone?f=%d&redirect=%s", Common::getCurHost(), 3, $callback);
            $this->output('0', '', array('redirect' => $url));
        } 
        if ($user['is_frozed']) {
            $this->output('-1', '对不起,您的账号积分已冻结,暂不能兑换!');
        }
        $this->_dynamicCheck($postData);
        $goods = $this->_getGoodsInfo($postData['goods_id']);
        if (empty($goods)) $this->output('-1', '对不起，商品已下架!');
        if($goods['number']< 1 || $goods['show_number'] < $goods["num_ratio"]){
        	$this->output('-1', '对不起，商品库存不足!');
        }
        $this->_checkDataIsValid($goods, $postData['number'], $user['id']);
        $this->_setPvUvData(Gionee_Service_Log::TYPE_U_G_EXCHANGE, Gionee_Service_Log::TYPE_U_G_EXCHANGE_UV, $goods['id']);
        $this->_check_phone_times($postData['number']);
        $this->_handleOrder($goods, $user, $postData);
    }

    private function _dynamicCheck($postData) {
        if (!$postData['geetest_challenge'] || !$postData['geetest_validate'] || !$postData['geetest_seccode']) {
            $this->output('-1', '验证码不能为空,请重试!');
        }
        $geetest               = new Vendor_Geetest();
        $config['private_key'] = Gionee_Service_Config::getValue('geetest_private_key');

        $geetest->set_privatekey($config['private_key']);
        if (!$geetest->validate($postData['geetest_challenge'], $postData['geetest_validate'], $postData['geetest_seccode'])) {
            $this->output('-1', '验证失败,请重试!');
        }
    }

    private function _check_phone_times($phone) {
        $rcKey    = 'user_pay_phone_times' . date('Ymd');
        $num      = Common::getCache()->hGet($rcKey, $phone);
        $maxTimes = Gionee_Service_Config::getValue('user_person_max_exchange');
        $maxTimes = max($maxTimes, 2);
        if ($num > $maxTimes) {
            $this->output('-1', '您今天充值次数已用完，请明天再来');
        }
    }

    /**
     * 兑换提交
     */
    public function submitAction() {
        $userInfo = $this->_getUserInfoIfLogin();
        $postData = $this->getInput(array(
            'goods_id',
            'prize_id',
            'province_id',
            'city_id',
            'distinct_id',
            'address',
            'mobile',
            'receiver_name',
            'refurl'
        ));
        $validPhoneNum = Common::checkIllPhone($userInfo['username']); //用户名是否为手机号
        if ($userInfo['is_black_user'] || !$validPhoneNum) {
        	$callback = sprintf("%s/user/goods/detail?goods_id=%d&prize_id=%d", Common::getCurHost(), $postData['goods_id'],$postData['prize_id']);
        	$url      = sprintf("%s/user/index/bindphone?f=%d&redirect=%s", Common::getCurHost(), 3, $callback);
        	$this->output('0', '', array('redirect' => $url));
        }
        if ($userInfo['is_frozed']) {
        	$this->output('-1', '对不起,您的账号积分已冻结,暂不能兑换!');
        }
        
        $this->_checkParams($postData);
        $shippingId = User_Service_Shipping::add($postData);
        if (!intval($shippingId)) {
            $this->output('-1', '添加收货信息失败，请重试!');
        }
        $goodsInfo = User_Service_Commodities::get($postData['goods_id']);
        if(empty($goodsInfo) ){
        	$this->output('-1','对不起,商品已下架!');
        }
        
        if(empty($goodsInfo['event_flag']) && $goodsInfo['number'] < 1){
        	$this->output('-1','对不起,商品库存不足!');
        }
        if($goodsInfo['show_number'] < $goodsInfo["num_ratio"]){
        	$this->output('-1', '对不起，商品库存不足!');
        }
        
        if($goodsInfo['event_flag'] ){//活动商品状态检测
        	$this->_checkActivityStatus($userInfo['id'],$postData['prize_id'],$goodsInfo['id']);
        }
        $this->_setPvUvData(Gionee_Service_Log::TYPE_U_G_EXCHANGE, Gionee_Service_Log::TYPE_U_G_EXCHANGE_UV, $goodsInfo['id']);
        $postData['shipping_id'] = $shippingId;
        $this->_handleOrder($goodsInfo, $userInfo, $postData);
    }

    private function _checkParams($params) {
        if (empty($params['goods_id'])) {
            $this->output('-1', '请选择要兑换的商品！');
        }
        if (!intval($params['province_id']) || !intval($params['city_id'])) {
            $this->output('-1', '城市信息不完整，请重新填写!');
        }
        if (empty($params['address'])) {
            $this->output('-1', '请输入详细收货地址!');
        }
        if (mb_strlen($params['address']) > 100) {
            $this->output('-1', '收货地址不要超过100个文字');
        }

        if (empty($params['receiver_name'])) {
            $this->output('-1', '收货人姓名不能为空！');
        }

        if (mb_strlen($params['receiver_name']) > 20) {
            $this->output('-1', '收货人姓名最大长度不能超过20个文字');
        }

        if (!Common::checkIllPhone($params['mobile'])) {
            $this->output('-1', '手机号码格式错误');
        }
    }

    private function _handleOrder($goods, $userInfo, $postData) {
        $redirect = sprintf("%s/user/goods/success?goods_id=%d&prize_id=%d&refurl=%s", Common::getCurHost(), $postData['goods_id'],$postData['prize_id'], $postData['refurl']);
      	if(empty($goods['event_flag'])){ //非活动商品时
	        $ret      = User_Service_Gather::frozenUserScores($goods, $userInfo['id']); //冻结金币并写日志
	        if (empty($ret)) {
	            $this->output('-1', '金币扣除失败!');
	        }
      	}
        $orderSn = User_Service_Order::generateOrder($userInfo['id'], $goods, $postData);        //生成订单
        if (empty($orderSn)) {
            $this->output('-1', '操作失败!');
        }
        $order = User_Service_Order::getBy(array('order_sn' => $orderSn));
        if ($order['order_type'] == '999') {
            User_Service_Order::forSelfOrder_999($order);
        }
        $this->output('0', '订单提交，正在处理中，请稍后！', array('redirect' => $redirect));
    }


    /**
     * 提交成功页
     */
    public function successAction() {
        $login = Common_Service_User::checkLogin('/user/goods/success');
        if (!$login['key']) $this->output('0', '', array('redirect' => $login['keyMain']));//检测登陆状态
        $userInfo = $login['keyMain'];
        Gionee_Service_Log::pvLog('user_cosume_success');
        Gionee_Service_Log::uvLog('user_cosume_success', $this->getSource());
        $post = $this->getInput(array('goods_id','prize_id', 'refurl'));
        if (!intval($post['goods_id'])) $this->output('-1', '参数有错!');
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
        $goodsInfo     = User_Service_Commodities::get($post['goods_id']);

        $backUrl = Common::getCurHost() . '/user/index/index';
        if ($goodsInfo['event_flag'] == 1) {
            $this->_checkActivityStatus($userInfo['id'],$post['prize_id'],$post['goods_id']);
            $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
            $type_id=$config['id'];
            $prizeInfo = Event_Service_Activity:: getUserPrizeById($userInfo['id'],$type_id,$post['prize_id']);
        	$backUrl = Common::getCurHost().'/event/seckill/index';
        	if(!empty($prizeInfo)){
        		$ret = Event_Service_Activity::getResultDao()->update(array('prize_status'=>'1','get_time'=>time()),$prizeInfo['id']);
                Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id,true);
                Event_Service_Activity:: getUserPrizeById($userInfo['id'],$type_id,$post['prize_id'],true);
        		$data = array(
        			'uid'		=>$userInfo['id'],
        			'status'	=>1,
        			'activity'	=>'双十一活动奖励',
        			'prize_name'=>$goodsInfo['name'],
        			'classify'=>'18',
        		);
        		Common_Service_User::sendInnerMsg($data,'receive_seckill_score_tpl');
        	}
        }
        $this->assign('userScores', $userScoreInfo);
        $this->assign('refurl', $post['refurl']);
        $this->assign('goods', $goodsInfo);
        $this->assign('backUrl', $backUrl);
    }


    /**
     * 黑名单用户漂白
     */
    public function changeStateAction() {
        $redirect = $this->getInput('redirect');
        $username = $this->getInput('username');
        $user     = array();
        $login    = Common_Service_User::checkLogin('/user/exchange/index');
        if (!$login['key']) {
            $user = Gionee_Service_User::getUserByName($username);
        } else {
            $user = $login['keyMain'];
        }
        Gionee_Service_User::updateUser(array('is_black_user' => 0, 'username' => $username), $user['id']);
        Gionee_Service_User::getCurUserInfo(true);
        Common::redirect($redirect);
        exit();
    }

    //活动状态检测
   private function _checkActivityStatus($uid=0,$prize_id=0,$gid=0){
	   	$redirectUrl = '/user/index/index/';
	   	$now = time();
        $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
        $type_id=$config['id'];
	   	if(!intval($config['status'])){
	   		Common::redirect($redirectUrl);
	   	}
	   	if($now> $config['end_time']){
	   		Common::redirect($redirectUrl);
	   	}

        $prizeInfo = Event_Service_Activity::getUserPrizeById($uid,$type_id,$prize_id);
	   	if(empty($prizeInfo)){
	   		Common::redirect($redirectUrl);
	   	}

	   	if(in_array($prizeInfo['prize_status'],array('1','-1'))){

            Common::redirect($redirectUrl);
	   	}

	   	$prizeGoods = Event_Service_Activity::getPrizeGoodsInfo($prizeInfo['prize_id']);
	   	if($prizeGoods['prize_val'] != $gid){
	   		Common::redirect($redirectUrl);
	   	}
	   	
	   	$expriedTime = $prizeInfo['add_time']+ $config['valid_minutes']*60;

	   	if($prizeInfo['prize_status'] == 0 && $expriedTime < $now) {//已过期
	   		Event_Service_Activity::getResultDao()->update(array('prize_status'=>'-1','expire_time'=>$now),$prizeInfo['id']);
	   		if($prizeGoods['prize_type'] == 1){ //失效后,实物奖品返回奖品池
	   			// Event_Service_Activity::changePrizeGoodsNumber($prizeGoods,'+');
	   		}
	   		Common::redirect($redirectUrl);
	   	}

   } 
   
    private  function _checkUser(){
    	$callbackUrl = $this->_backUrl();
    	$login       = Common_Service_User::checkLogin($callbackUrl);        //检测是否登陆
    	if (!$login['key']) $this->output('0', '', array('redirect' => $login['keyMain']));
    	$userInfo = Gionee_Service_User::getCurUserInfo();
/*     	if ($userInfo['is_black_user']) {
    		$url = sprintf("%s/user/index/bindphone?f=%d&redirect=%s", Common::getCurHost(), 3, $callbackUrl);
    		$this->output('0', '', array('redirect' => $url));
    	} */
    	$url = Common_Service_User::validUsername($userInfo['username'], $callbackUrl); //用户手机合法性检测
    	if(!empty($url)){
    		$this->output('0', '', array('redirect' => $url, 'increScores' => 0));
    	}
    }
    
    private function _getProduceGoodsInfo($gid=0){
    	$rcKey = "User:GOODS:PRODUCE:{$gid}";
    	$goods = Common::getCache()->get($rcKey);
    	if($goods === false){
    		$goods = User_Service_Produce::get($gid);
    		Common::getCache()->set($rcKey,$goods,60);
    	}
    	return $goods;
    }
    
    /**
     * 获得等级商品真正兑换消耗金币信息
     */
    private function _getRealCostScores($isSpecial = 0, $goods_id, $scores, $uid, $levelGroup = 1, $userLevel = 1) {
        $realScore = $scores;
        if (empty($isSpecial)) {
            return $realScore;
        }
        $var           = array(
            'group_id'    => '3',
            'goods_id'    => $goods_id,
            'level_group' => $levelGroup,
            'user_level'  => $userLevel
        );
        $privilegeInfo = User_Service_Uprivilege::getBy($var);
        if (!empty($privilegeInfo) && $privilegeInfo['scores'] > 0) {
            $realScore = $privilegeInfo['scores'];
        }
        return $realScore;
    }

    /**
     * 对看图/下载等做次数限制
     */
    public function  _canGetScores($uid = 0, $cat_id = 0, $curLevel = 1, $rewardType = 1) {
        if (empty($cat_id) || empty($uid)) return false;
        $catInfo            = User_Service_Category::get($cat_id);
        $max_num            = $catInfo['max_number'];
        $params             = array();
        $params['add_time'] = array(array('>=', mktime(0, 0, 0)), array('<=', mktime(23, 59, 59)));
        $params['cat_id']   = $cat_id;
        $params['group_id'] = 2;
        $params['uid']      = $uid;
        $count              = User_Service_Earn::count($params);
        $levelRewardTimes   = User_Service_ExperienceInfo::getLevelRewardsData($curLevel, $rewardType);
        if ($count < $max_num + $levelRewardTimes) return true;
        return false;
    }

    private function _getGoodsInfo($goodsId = 0) {
        $goods = User_Service_Commodities::getBy(array('id' => $goodsId, 'status' => 1));
        return $goods;
    }

    //七夕活动检测
    private function  _qxCheck($gid = 0){
    	$userInfo      = Gionee_Service_User::getCurUserInfo();
    	$where['uid']  	= $userInfo['id'];
    	 $where['date'] = date("Ymd",time());
    	 $where['prize_status'] = 1;
    	 $where['prize_val'] = $gid;
    	$prizeInfo	  = Event_Service_Link::getPrizeDao()->getBy($where);
    	if(!empty($prizeInfo)){
    		$this->output('-1','您已获得奖品,请不要重复操作!');
    	}	
    }
    /**
     * 参数检测
     */
    private function _checkDataIsValid($goods, $number, $uid) {
        if ($goods['number'] < 1) {
            $this->output('-1', '对不起，您要兑换的商品库存不足!');
        }
        
        if($goods['show_number']<$goods['num_ratio']){
        	$this->output('-1', '对不起，您要兑换的商品库存不足!');
        }
  
        if (!empty($goods['card_info_id'])) {
            $cardMsg = User_Service_CardInfo::get($goods['card_info_id']);
            if (in_array($cardMsg['group_type'], array('1', '3'))) {//充流量包或充话费
                if (!empty($number) && !preg_match('/^1[3|5|7|8|9]\d{9}$/', $number)) {
                    $this->output('-1', '手机号码格式有误,请核实!');
                }
            } elseif ($cardMsg['group_type'] == 4) {//QQ
                if (!preg_match('/^[1-9]\d{3,13}$/', intval($number))) {
                    $this->output('-1', 'QQ号格式不正确');
                }
            }
        }

        //账号金额检测
        $userScoreInfo = User_Service_Gather::getInfoByUid($uid);
        if (empty($userScoreInfo)) {
            $this->output('-1', '对不起，您的账号可能有问题，请联系相关工作人员确认，谢谢！');
        }
        if ($userScoreInfo['remained_score'] < $goods['scores']) {
            $this->output('-1', '对不起，您的账号金币不够!');
        }
        if (!Common_Service_User::checkFreq($uid)) {
            $this->output('-1', '请求次数过多');
        }
    }


    private function _getUserInfoIfLogin() {
        $login = Common_Service_User::checkLogin($this->_backUrl());
        if (empty($login['key'])) {
            $this->output('0', '', array('redirect' => $login['keyMain']));//检测登陆状态
        }
        $userInfo = $login['keyMain'];
        return $userInfo;
    }


    private function _setPvUvData($pvType, $uvType, $goodsId) {
        Gionee_Service_Log::incrBy($pvType, $goodsId);
        Gionee_Service_Log::toUVByCacheKey($uvType, $goodsId, $this->getSource());
    }

    /**
     * 得到前一个返回上一级
     */
    private function _backUrl() {
        return Common::getHttpReferer();
    }
}