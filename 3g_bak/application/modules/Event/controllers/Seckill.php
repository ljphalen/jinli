<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SeckillController extends User_BaseController {

    public $actions = array(
        'remindUrl'      => '/Event/seckill/ajaxRemind',
        'redirectUrl'      => '/user/center/index',
    );

    //双11活动预约页面
    public function preheatAction(){
		$data = array(
				'login'								=>0,  //是否已登陆
				'remind'							=>0,  //是否已预约
				'loginUrl'							=>'', //登陆URL
		);
		$from = $this->getInput('from');
		if(empty($from)){
			$from = 'preheat';
		}
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $from . ':seckill');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $from . ':seckill');

        $loginInfo = Common_Service_User::checkLogin('/event/seckill/preheat',false,$this->getInput('testMobile'));
        $data['login']  = $loginInfo['key'];
        if(!intval($loginInfo['key'])){
            $data['loginUrl'] = $loginInfo['keyMain'];
        }
        $userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));//测试改为true
        $prize = array();
        if(!empty($userInfo)) {
            $remindInfo = Event_Service_Activity::getUserRemindInfo($userInfo['id'],false);//测试改为true
            if (!empty($remindInfo)) {
                $data['remind']  = 1;
                $status=intval($this->getInput('status'));
                if($status==2) $data['remind']  = 2;
            }
        }

		$this->assign('data', $data);
	}

    //预约页面
    public function remindAction(){
        $actions = array(
            'remindUrl'      => '/Event/seckill/ajaxRemind',
        );
        $loginInfo = Common_Service_User::checkLogin('/event/seckill/remind',true,$this->getInput('testMobile'));
        $userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
        if (!Common::checkIllPhone($userInfo['username'])) {
            $userInfo['username']='';
        }
        $this->assign('data', $userInfo);
    }

    //预约成功页面
    public function remindsuccessAction(){
        $actions = array(
            'redirectUrl'      => '/user/center/index',
        );
        $loginInfo = Common_Service_User::checkLogin('/event/seckill/remind',true,$this->getInput('testMobile'));
        $userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
        $remindInfo = Event_Service_Activity::getUserRemindInfo($userInfo['id']);
        $is_receive=0;
        if($remindInfo['is_get']==1){
            //可以领取30金币
            $is_receive=1;
        }
        $this->assign('is_receive', $is_receive);
        $this->assign('data', $userInfo);
    }

    //获取金币功能
    public function ajaxGetJbAction(){
        $loginInfo = Common_Service_User::checkLogin('/event/seckill/remind',true,$this->getInput('testMobile'));
        $userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
        $remindInfo = Event_Service_Activity::getUserRemindInfo($userInfo['id']);
        $config = Event_Service_Activity::getRemindConfigData();
        $scores=$config['seckill_remind_jb'];
        //获取金币
        if($remindInfo['is_get']==1){
            $ret = User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], $scores, 216);
            if($ret){
                $data  = array(
                    'uid'=>$userInfo['id'],
                    'scores'=>$scores,
                    'status'=>1,
                    'activity'=>'双十一活动奖励',
                    'prize_name'=>sprintf("%d金币",$scores),
                    'classify'=>18
                );
                Common_Service_User::sendInnerMsg($data,'receive_seckill_score_tpl');
                $ret2= Common_Service_User::unReadMsgNumber($userInfo['id'],true);
            }else{
                $this->output('-1','',array('err_msg'=>'操作失败!'));
            }
            //修改状态
            $update = Event_Service_Activity::getEventRemindDao()->updateBy(array('is_get'=>2),array('uid'=>$userInfo['id']));
            Event_Service_Activity::getUserRemindInfo($userInfo['id'],true);
            $this->output('0','',array(
                'err_msg'=>"恭喜您,金币领取成功!",
                'url'=>'/user/index/index?secpop=1',
            ));

        }else{
            $this->output('-1','',array('err_msg'=>'金币已领取!'));
        }
        $this->output('-1','操作失败',array('err_msg'=>'操作失败!'));
    }

    //预约
    public function ajaxRemindAction(){
        $loginInfo = Common_Service_User::checkLogin('/event/seckill/preheat',true,$this->getInput('testMobile'));
        $userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
        $is_get=0;
        $config = Event_Service_Activity::getRemindConfigData();
        $this->_checkActivityRemindStatus();
        if($userInfo['register_time']>strtotime($config['seckill_remind_start_time'])){
          //  $userInfo['id'];
            $is_get=1;  //0.老用户;1.新用户;2.已领取
        }
        $mobile   = trim($this->getInput('mobile'));
        if (!Common::checkIllPhone($mobile)) {
           $this->output('-1','',array("err_msg"=>'手机号码错误!'));
           // Common::redirect('/event/seckill/remind');
        }
        Event_Service_Activity::addClicksData($userInfo['id'],'seckill_remind');
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'remind:seckill');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  'remind:seckill');
        $this->_checkRequestTimes($userInfo['id'], 'remind');
        $remindInfo = Event_Service_Activity::getUserRemindInfo($userInfo['id']);
        $webroot=Common::getCurHost();
        if(!empty($remindInfo)){
            //Common::redirect('/event/seckill/preheat');
            //$this->output('-1','',array("err_msg"=>'预约已经设置!'));
            $this->output('0','',array("err_msg"=>'预约已经设置!',"redirect" => $webroot.'/event/seckill/preheat'));
            
        }
        $params=  array(
            'uid'				=>$userInfo['id'],
            'mobile'			=>$mobile,
            'user_ip'			=>Util_Http::getClientIp(),
            'is_get'            =>$is_get,
            'send_sgn'          =>0,
            'send_time'         =>0,
            'add_time'			=>time(),
        );
        $ret = Event_Service_Activity::getEventRemindDao()->insert($params);
        Event_Service_Activity::getUserRemindInfo($userInfo['id'],true);
        if($is_get==1){
            //Common::redirect('/event/seckill/remindsuccess');
            $jumpUrl = $webroot.'/event/seckill/remindsuccess';
        }else{
            //Common::redirect('/event/seckill/preheat?status=2');
            $jumpUrl = $webroot.'/event/seckill/preheat?status=2';
        }
        $this->output('0','',array('redirect'=>$jumpUrl));

    }

    //是否开始预约
    private function _checkActivityRemindStatus(){
        $config = Event_Service_Activity::getRemindConfigData();
        if(!intval($config['seckill_remind_status'])) {
            $this->output('-1','',array('err_msg'=>'预约已结束!'));
        }
        if(strtotime($config['seckill_remind_start_time']) > time() || strtotime($config['seckill_remind_end_time'])< time()){
            $this->output('-1','' ,array('err_msg'=>'预约已结束!'));
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //双十一活动页面
    public function indexAction() {
        $data = array(
            'login'					=>0,  //是否已登陆
            'activity'				=>0, //活动状态
            'loginUrl'				=>'', //登陆URL
        //     'prizeUrl'			    =>'', //领奖URL
            'expireSeconds'			=>0,//默认过期时间
        //     'remainedSeconds'		=>0, //剩余领奖时间
        //     'prizeImageUrl'			=>'', //奖品图片
        //    'prizeName'				=>'',
        //    'chance'				=>1,//是否有领奖机会
        //     'prizeStatus'			=>-2, //是否有未领奖品
            'ifEnd'=>0,
        );
        $from = $this->getInput('from');
        if(empty($from)){
            $from = 'index';
        }
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $from . ':seckill');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $from . ':seckill');
        $loginInfo = Common_Service_User::checkLogin('/event/seckill/index',false,$this->getInput('testMobile'));
        $data['login']  = $loginInfo['key'];
        if(!intval($loginInfo['key'])){
            $data['loginUrl'] = $loginInfo['keyMain'];
        }
        //活动状态
        $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
        if(!intval($config['status'])) {
            $data['ifEnd']='1';
        }
        $now = time();
        if($config['start_time'] > $now ||$config['end_time']< $now){
            $data['ifEnd']='1';
        }
        $data['expireSeconds'] = $config['valid_minutes']*60;
        $type_id=$config['id'];
        $now = time();
        if(!intval($config['status'])){
            $data['activity'] = 1;
        }
        if( $config['start_time'] >$now|| $now > $config['end_time']){
            $data['activity'] = 1;
        }
        $data['prizeList'] = Event_Service_Activity::getPrizeList($type_id);
        //管道创建
        $apcKey = "Event:Seckill:User:MiaoshaTube";
        $apcdata   = Cache_APC::get($apcKey);
        if (empty($apcdata)) {
            foreach($data['prizeList'] as $k=>$v){
                Util_Lock::createTube('tube_prize_'.$v['id'],1);
            }
            Cache_APC::set($apcKey, 1,86400);
        }
		//奖品状态
        $prize_array = array();
		$userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
		if(!empty($userInfo)){
			$prize_array = Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id);
			if(!empty($prize_array)){
                $data['chance']  = 0;
                foreach($prize_array as $key=>$v){
                    $prize_info=array();
                    $prize_info['prize_id'] = $v['prize_id'] ;
                    $prize_info['prizeStatus'] = $v['prize_status'] ;
                    $prizeGoods =Event_Service_Activity::getPrizeGoodsInfo($v['prize_id']);
                    $iamgePath = Common::getImgPath();
                    $prize_info['prizeImageUrl'] = $iamgePath.$prizeGoods['image'];
                    $prize_info['prizeName'] = $prizeGoods['name'];
                    $host = Common::getCurHost();
                    $prize_info['prizeUrl'] = sprintf("%s%s%s%d",$host,'/event/seckill/getPrize','?prize_id=',$v['prize_id']); //默认为金币
                    if($prizeGoods['prize_type'] == 1){ //实物奖品时
                        $prizeGoodsInfo =Event_Service_Activity::getDrawingPrizeGoodsInfo($v['prize_id']);
                        $prize_info['prizeUrl']  = sprintf("%s%s%d%s%d",$host,'/user/goods/detail?goods_id=',$prizeGoodsInfo['prize_val'],'&prize_id=',$v['prize_id']);
                    }
                    if($v['prize_status'] == 0){ //中奖但没领取时
                        $expiredTime = $v['add_time']+$data['expireSeconds'];
                        $remainedSeconds  = $expiredTime - $now;
                        if($remainedSeconds > 0 ){
                            $prize_info['remainedSeconds']  = $remainedSeconds;
                        }else{
                            Event_Service_Activity::getResultDao()->update(array('prize_status'=>'-1', 'expire_time'=>$now),$v['id']);
                            /*
                            $prizeGoods = Event_Service_Activity::getPrizeGoodsInfo($v['prize_id']);
                            if($prizeGoods['prize_type'] == 1){ //实物奖品过期,返回奖品池
                                Event_Service_Activity::changePrizeGoodsNumber($prizeGoods,'+');
                            }
                            */
                            Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id,true);
                            $prize_info['prizeStatus'] = '-1';
                        }
                    }
                    $prize_array[$key]=$prize_info;
                }

			} 
		}
        $data['nowTime']=Common::getTime();
        $this->assign('prize', Common::jsonEncode($prize_array) );
		$this->assign('data', $data);
    }

    public function giveUpPrizeAction(){
          $loginInfo = Common_Service_User::checkLogin('/event/seckill/index',false,$this->getInput('testMobile'));
          $userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
          $prize_id  = intval($this->getInput('prize_id'));
          $prizeGoodsInfo =Event_Service_Activity::getDrawingPrizeGoodsInfo($prize_id);
          if(empty($prizeGoodsInfo)){
             $this->output('-1',array('err_msg'=>'奖品不存在!'));
          }else{
              //活动状态
              $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
              $type_id=$config['id'];
              Event_Service_Activity::getResultDao()->updateBy(array('prize_status'=>'-2','expire_time'=>time()),array('prize_id'=>$prize_id));
              Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id,true);
              $this->output('0',array('err_msg'=>'奖品已放弃!'));
          }

    }

	//抽奖
	public  function ajaxDrawingAction(){
		$loginInfo = Common_Service_User::checkLogin('/event/seckill/index',true,$this->getInput('testMobile'));
		$userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
		$this->_checkActivityStatus();
        //活动状态
        $config = Event_Service_Activity::getActivityTypeInfoBySign('miaosha');
        $data['expireSeconds'] = $config['valid_minutes'] * 60;
        $type_id = $config['id'];
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'drawing:seckill');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  'drawing:seckill');
		$this->_checkRequestTimes($userInfo['id'], 'seckill');
        $prize_id  = intval($this->getInput('prize_id'));
        $prizeGoodsInfo =Event_Service_Activity::getDrawingPrizeGoodsInfo($prize_id);
        /*Common::getCache()->hSet('seckill',$userInfo[['id']],date('Ymd',time()),360);
        $data = Common::getCache()->hGet('seckill',date('Ymd',time()));
        foreach($data as $k=>$v){
            $uid[] = $v;
        }*/
         Event_Service_Activity::addClicksData($userInfo['id'],'seckill_drawing');
        if(empty($prizeGoodsInfo)){
            $this->output('-1',array('err_msg'=>'奖品不存在!','err_code'=>'0'));
        }else{
            $now = time();
            if($prizeGoodsInfo['start_time'] > $now || $prizeGoodsInfo['end_time']< $now){
                $this->output('-1','' ,array('err_msg'=>'奖品不存在!','err_code'=>'0'));
            }

            $mrcKey = "Event:Seckill:User:MiaoshaUserSgnXX:{$prizeGoodsInfo['id']}";
            $miaosha_sgn = Common::getCache()->get($mrcKey);
            if($miaosha_sgn==1){
                $this->output('-1', '', array("err_msg" => '商品已经抢光了!','err_code'=>'-1'));
            }

            $tubeName = "tube_prize_{$prizeGoodsInfo['id']}";  //管道名

            if (Util_Lock::tubeLock($tubeName) == false) {
                $this->output('-1', '', array("err_msg" => '服务器繁忙,请稍后再试!','err_code'=>'-2'));  //如果管理已经加锁，直接返回
            }

            $type_id=$prizeGoodsInfo['activity_type'];
            $user_prize_info = Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prize_id,true);//测试设为true
            if(!empty($user_prize_info)){
                $this->output('-1', '', array("err_msg" => '商品您已经抢到了!','err_code'=>'1'));
            }
            //$prize_id //奖品ID  number//奖品数量
            //奖品数量大于0
            if($prizeGoodsInfo['number']>0){
                //中奖
                $params=  array(
                    'activity_id'       =>$type_id,
                    'uid'				=>$userInfo['id'],
                    'prize_id'			=>$prizeGoodsInfo['id'],
                    'prize_status'		=>0,
                    'add_time'			=>time(),
                    'add_date'			=>date('Ymd',time()),
                    'user_ip'			=>Util_Http::getClientIp(),
                );
                $ret = Event_Service_Activity::getResultDao()->insert($params);
                Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id,true);
                Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prizeGoodsInfo['id'],true);
                $imgPath = Common::getImgPath();
                $host  	 = Common::getCurHost();
                $prizeUrl = $host."/event/seckill/getPrize?prize_id=".$prizeGoodsInfo['id'];
                if($prizeGoodsInfo['prize_type'] == 1){
                    $prizeUrl = sprintf("%s%s%d%s%d",$host,'/user/goods/detail?goods_id=',$prizeGoodsInfo['prize_val'],'&prize_id=',$prize_id);
                }
                Event_Service_Activity::changePrizeGoodsNumber($prizeGoodsInfo);//更新奖品数量
                $this->output('0','',array(
                    'err_msg'=>"恭喜您,抢到{$prizeGoodsInfo['name']}!",
                    'prize_id'=>$prizeGoodsInfo['id'],
                    'prize_image'=>$imgPath.$prizeGoodsInfo['image'],
                    'prize_url'		=>$prizeUrl,
                    'expireSeconds'  => $data['expireSeconds'],
                    'nowTime'  =>Common::getTime(),
                ));
            }else{
                $mrcKey = "Event:Seckill:User:MiaoshaUserSgnXX:{$prizeGoodsInfo['id']}";
                Common::getCache()->set($mrcKey, 1, 3600);
                $this->output('-1', '', array("err_msg" => '商品已经抢光了!','err_code'=>'-1'));
            }
         }
        $this->output('-1','操作失败',array('err_msg'=>'操作失败!','err_code'=>'-3'));
	}

	//获得奖品
	public function getPrizeAction(){
		$loginInfo = Common_Service_User::checkLogin('/event/seckill/index',true,$this->getInput('testMobile'));
		$userInfo = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
		Event_Service_Activity::addClicksData($userInfo['id'],'seckill_getprize');

        $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
        $type_id=$config['id'];
        if(!intval($config['status'])) {
            Common::redirect('/event/seckill/index');
        }
        $now = time();
        if($config['start_time'] > $now || $config['end_time']+$config['valid_minutes']*60< $now){
            Common::redirect('/event/seckill/index');
        }
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'get:seckill');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  'get:seckill');

        $prize_id  = intval($this->getInput('prize_id'));
		$redirectUrl = sprintf("%s%s%d",Common::getCurHost(),'/user/index/index?prize_id=',$prize_id);
		$this->_checkRequestTimes($userInfo['id'], 'get');
		$prize = Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prize_id);

		if(empty($prize)){
			Common::redirect($redirectUrl);
		}
		if($prize['prize_status'] == '-1'){
			Common::redirect($redirectUrl);
		}
		if($prize['prize_status'] == '1'){
			Common::redirect($redirectUrl);
		}
		$expiredTime = $config['valid_minutes']*60 + $prize['add_time'];
		if($prize['prize_status'] == '0'  && $now >$expiredTime){
			Event_Service_Activity::getResultDao()->update(array('prize_status'=>'-1','expire_time'=>$now),$prize['id']);
            Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id,true);
            Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prize_id,true);
			Common::redirect($redirectUrl);
		}
		$prizeGoods =Event_Service_Activity::getNewPrizeGoodsInfo($prize['prize_id']);
		if($prizeGoods['prize_type'] == 1){ //实物奖品
			$redirectUrl = Common::getCurHost()."/user/goods/detail?goods_id={$prizeGoods['prize_val']}";
			Common::redirect($redirectUrl);
		}else{
			$update = Event_Service_Activity::getResultDao()->update(array('prize_status'=>1,'get_time'=>time()),$prize['id']);
			$ret = User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], $prizeGoods['prize_val'], 216);
			if($ret && $update ){
				$data  = array(
						'uid'=>$userInfo['id'],
						'scores'=>$prize['prize_val'],
						'status'=>1,
						'activity'=>'双十一活动奖励',
						'prize_name'=>sprintf("%d金币",$prizeGoods['prize_val']),
						'classify'=>18
				);
				Event_Service_Activity::changePrizeGoodsNumber($prizeGoods);

				Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prize_id,true);

				Common_Service_User::sendInnerMsg($data,'receive_seckill_score_tpl');

				Common::redirect($redirectUrl);
			}
		}
		$this->output('-1','',array('err_msg'=>'操作失败!'));
	}

	public function clearAction(){
			$userInfo =  Gionee_Service_User::getCurUserInfo();
			$mobile = $this->getInput('mobile');
			if(!empty($mobile)){
				$userInfo =  Gionee_Service_User::getUserByName($mobile);
			}
			Event_Service_Activity::getResultDao()->deleteBy(array('uid'=>$userInfo['id']));
            Event_Service_Activity::getUserPrizeList($userInfo['id'],4,true);
            Event_Service_Activity:: getUserPrizeById($userInfo['id'],4,18,true);
            Event_Service_Activity:: getUserPrizeById($userInfo['id'],4,19,true);
            Event_Service_Activity:: getUserPrizeById($userInfo['id'],4,20,true);
            Event_Service_Activity:: getUserPrizeById($userInfo['id'],4,21,true);
            Event_Service_Activity:: getUserPrizeById($userInfo['id'],4,22,true);
            Event_Service_Activity:: getUserPrizeById($userInfo['id'],4,23,true);
            header("Content-type:text/javascript;charset=utf-8");
			exit($userInfo['username'].'信息清除成功!');
	}

	private function _checkActivityStatus(){
        $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀

		if(!intval($config['status'])) {
			$this->output('-1','',array('err_msg'=>'活动已结束!'));
		}
        $now = time();
		if($config['start_time'] > $now ||$config['end_time']< $now){
			$this->output('-1','' ,array('err_msg'=>'活动已结束!'));
		}
	}

	private function _checkRequestTimes($uid,$type){
		$key = "EVENT:REQUEST:TIMES:{$uid}:{$type}:";
		$data = Common::getCache()->get($key);
		if(!empty($data)){
			Common::getCache()->set($key,1,3);
			$this->output('-1','',array('err_msg'=>'请求太频繁,请稍后再试!'));
		}
		
	}
}