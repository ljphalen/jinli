<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

 class Event_Service_Activity {
 	
 	/**
 	 * 
 	 * @return Event_Dao_ActivityType
 	 */
 	public static function getEventTypeDao(){
 		
 		return Common::getDao("Event_Dao_ActivityType");
 	}

     /**
      *
      * @return Event_Dao_ActivityRemind
      */
     public static function getEventRemindDao(){

         return Common::getDao("Event_Dao_ActivityRemind");
     }




     /**
 	 *@return Event_Dao_ActivityGoods
 	 */
 
 	public static function getGoodsDao(){
 			
 		return Common::getDao("Event_Dao_ActivityGoods");
 	}
 	
 	/**
 	 * @return object
 	 */
 	public static function getResultDao(){
 	
 		return Common::getDao("Event_Dao_ActivityResult");
 	}
 	
 	public static function getClicksDao(){
 		return Common::getDao("Event_Dao_Clicks");
 	}

     public static function getUserRemindInfo($uid,$sync=false){
         //检测是否有提醒信息
         $rcKey = "Event:Seckill:User:Remind:{$uid}";
         $remindInfo = Common::getCache()->get($rcKey);
         if(empty($remindInfo) || $sync){
             $where = array(
                 'uid'		=>$uid
             );
             $remindInfo= Event_Service_Activity:: getEventRemindDao()->getBy($where);
             Common::getCache()->set($rcKey,$remindInfo,300);
         }
         return $remindInfo;
     }

 	public static function getUserPrizeInfo($uid,$sync=false){
 		//检测昨天是否有未过期且未领奖品
 		 list($hour,$minus,$seconds) = explode(":",date('H:i:s',time()));
 		$nowSeconds = self::_getSeconds($hour, $minus, $seconds);//当前时间秒数
 		$configData = self::getConfigData();
 		$expiredSeconds = $configData['national_day_expires']*60;
 		if($nowSeconds >0 && $nowSeconds <= $expiredSeconds){
 			$date = date("Ymd",strtotime("-1 day"));
 			$yestdayPrize  = self::_getUserPrizeInfo($uid, $date,$sync);
 			if((!empty($yestdayPrize)) && ($yestdayPrize['prize_status']  == '0' )){//昨天未领奖
 				return $yestdayPrize;
 			}
 		}
 		$date = date("Ymd",strtotime('now'));
 		$prize = self::_getUserPrizeInfo($uid, $date,$sync);
 		return $prize;
 	}
 	
 	private  static function _getUserPrizeInfo($uid,$date,$sync=false){
 		$rcKey = "Event:National:User:Prize:{$date}:{$uid}";
 		$prizeInfo = Common::getCache()->get($rcKey);
 		if(empty($prizeInfo) || $sync){
 			$where = array(
 				'uid'		=>$uid,
 				'add_date'	=>$date,
 			);
 			$prizeInfo = Event_Service_Activity::getResultDao()->getBy($where);
 			Common::getCache()->set($rcKey,$prizeInfo,300);
 		}
 		return $prizeInfo;
 	}
 	
 	//奖品列表
 	public static  function getPrizeGoodsList($sync=false){
 		$rcKey = "User:Prize:Goods:List";
 		$goodsList = Common::getCache()->get($rcKey);
 		if(empty($goodsList) || $sync){
 			$where = array(
 				'status' 	=>1,
 				'number'=>array(">",0),
 			);
 			$goodsList = Event_Service_Activity::getGoodsDao()->getsBy($where,array('id'=>'DESC'));
 			Common::getCache()->set($rcKey,$goodsList,3600);
 		}
 		return $goodsList;
 	}
 	//单个奖品信息
 	public static  function getPrizeGoodsInfo($goodsId,$sync=false){
 		$rcKey = "Event:Seckill:Goods:{$goodsId}:";
 		$data = Common::getCache()->get($rcKey);
 		if(empty($data) || $sync){
 			$data = Event_Service_Activity::getGoodsDao()->get($goodsId);
 			Common::getCache()->set($rcKey,$data,3600);
 		}
 		return $data;
 	}

 //新的单个奖品信息
 public static  function getNewPrizeGoodsInfo($goodsId,$sync=false){
     $rcKey = "Event:Seckill:Goods:{$goodsId}:";
     $data = Common::getCache()->get($rcKey);
     if(empty($data) || $sync){
         $where['id'] = $goodsId;
         $where['start_time']=array('<=',time());
         $where['end_time']=array('>=',time());
         $data = Event_Service_Activity::getGoodsDao()->getby($where);
         Common::getCache()->set($rcKey,$data,3600);
     }
     return $data;
 }

 public static  function getDrawingPrizeGoodsInfo($goodsId,$sync=false){
     $rcKey = "Event:Seckill:DrawingGoods:{$goodsId}:";
     $data = Common::getCache()->get($rcKey);
     if(empty($data) || $sync){
         $where['id'] = $goodsId;
         $data = Event_Service_Activity::getGoodsDao()->getby($where);
         Common::getCache()->set($rcKey,$data,3600);
     }
     return $data;
 }

 public static function totalSendScoresPerDay($where,$group){
 	$data = self::getResultDao()->totalSendScoresPerDay($where,$group);
 	$ret = array();
 	foreach($data as $k=>$v){
 		$prizeGoods = self::getPrizeGoodsInfo($v['prize_id']);
 		$scores = 0;
 		if($prizeGoods['prize_type'] == 2){
 			$scores = $prizeGoods['prize_val']*$v['prize_number'];
 			$ret[$v['add_date']][$v['prize_id']] = $scores;
 		}
 	}
 	return $ret;
 }
 	
 	private static function _getSeconds($hour,$minus,$seconds){
 		return $hour*3600+$minus*60+$seconds;
 	}
 	
	public static function  updateExpiredPrizeStatus(){
		$ids = array();
		 $data = self::getResultDao()->getList(0,50,array('prize_status'=>0),array('id'=>'ASC'));
		 $config = self::getConfigData();
		 $expireSeconds = $config['national_day_expires']*60;
		foreach ($data as $k=>$v){
			if(($v['add_time'] + $expireSeconds)< time()){
				$ids[] = $v['id'];
				self::getResultDao()->update(array('prize_status'=>'-1','expire_time'=>time()),$v['id']);
				$prizeGoods = Event_Service_Activity::getPrizeGoodsInfo($v['prize_id']);
					self::changePrizeGoodsNumber($prizeGoods,'+');
			}
		}
		return $ids;
	}

     public static function updateExpiredPrizeMiaoShaStatus(){
         $ids = array();
         $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
         $type_id=$config['id'];
         $data = self::getResultDao()->getList(0,50,array('activity_id'=>$type_id,'prize_status'=>0),array('id'=>'ASC'));
         $expireSeconds = $config['valid_minutes']*60;
         foreach ($data as $k=>$v){
             if(($v['add_time'] + $expireSeconds)< time()){
                 $ids[] = $v['id'];
                 self::getResultDao()->update(array('prize_status'=>'-1','expire_time'=>time()),$v['id']);
                 Event_Service_Activity::getPrizeGoodsInfo($v['prize_id'],true);
                 Event_Service_Activity::getNewPrizeGoodsInfo($v['prize_id'],true);
                 //Event_Service_Activity::getPrizeGoodsList(true);

                // self::changePrizeGoodsNumber($prizeGoods,'+');
             }
         }
         return $ids;
     }

 	public static function addClicksData($uid=0,$type=''){
 		$where = array(
 			'uid'	=>$uid,
 			'event_type'=>$type,
 			'add_time'=>time(),
 			'user_ip'	=>Util_Http::getClientIp(),
 			'date'		=>date('Ymd',time()),
 		);
 		self::getClicksDao()->insert($where);
 	}
	
 	public static function getClickUsers($where,$gruop){
 		$ret = self::getClicksDao()->getClickUsers($where,$gruop);
 		$data = array();
 		foreach($ret as $k=>$v){
 			$data[$v['event_type']] = $v['total_users'];
 		}
 		return $data;
 	}
 	
 	
 	public static function changePrizeGoodsNumber($prizeGoods,$type='-'){
 		if($type == '+'){ 
 			$number = $prizeGoods['number'] +1;
 		}else{
 			$number = $prizeGoods['number'] - 1;
 		}
 		Event_Service_Activity::getGoodsDao()->update(array('number'=>$number),$prizeGoods['id']);
 		//Event_Service_Activity::getPrizeGoodsList(true);
        $config = Event_Service_Activity::getActivityTypeInfoBySign('miaosha');
        $type_id = $config['id'];
        Event_Service_Activity::getPrizeList($type_id,true);
 		Event_Service_Activity::getDrawingPrizeGoodsInfo($prizeGoods['id'],true);
 	}
 	
 	public static function getConfigData($sync=false){
 		$rcKey = "3G:National:Day:Config:";
 		$configData = Common::getCache()->get($rcKey);
 		if(empty($configData) || $sync){
 			$keys = array(
 					'national_day_start_time',
 					'national_day_end_time',
 					'national_day_status',
 					'national_day_rule',
 					'national_day_times',
 					'national_day_expires'
 			);
 			$params['3g_key'] = array('IN',$keys);
 			$ret = Gionee_Service_Config::getsBy($params);
 			foreach ($ret as $k=>$v){
 				$data[$v['3g_key']] = $v['3g_value'];
 			}
 			Common::getCache()->set($rcKey,$data,3600);
 		}
 		return $configData;
 	}
     public static function getRemindConfigData($sync=false){
         $rcKey = "3G:Seckill:Remind:Config:";
         $configData = Common::getCache()->get($rcKey);
         if(empty($configData) || $sync){
             $keys = array(
                 'seckill_remind_start_time',
                 'seckill_remind_end_time',
                 'seckill_remind_status',
                 'seckill_remind_jb',
                 'seckill_remind_rule'
             );
             $params['3g_key'] = array('IN',$keys);
             $ret = Gionee_Service_Config::getsBy($params);
             foreach ($ret as $k=>$v){
                 $data[$v['3g_key']] = $v['3g_value'];
             }
             Common::getCache()->set($rcKey,$data,3600);
         }
         return $configData;
     }

     public static function getActivityTypeInfo($id,$sync=false){
         $tKey     = "3G:Seckill:ActivityType" . $id;
         $activityTypeInfo = Common::getCache()->get($tKey);
         if (empty($activityTypeInfo) || $sync) {
             $activityTypeInfo = self::getActivityType($id);
             Common::getCache()->set($tKey, $activityTypeInfo, Common::T_ONE_DAY);
         }
         return $activityTypeInfo;
     }

     public static function getActivityTypeInfoBySign($type_sign,$sync=false){
         $tKey     = "3G:Seckill:ActivityType" . $type_sign;
         $activityTypeInfo = Common::getCache()->get($tKey);
         if (empty($activityTypeInfo) || $sync) {
             $activityTypeInfo = self::getActivityTypeBySign($type_sign);
             Common::getCache()->set($tKey, $activityTypeInfo, Common::T_ONE_DAY);
         }
         return $activityTypeInfo;
     }

     public function getActivityTypeBySign($type_sign) {
         if (!$type_sign) return false;
         return Event_Service_Activity::getEventTypeDao()->getBy(array('type_sign' =>$type_sign));
     }

     public static function getActivityType($id) {
         if (!intval($id)) return false;
         return Event_Service_Activity::getEventTypeDao()->get(intval($id));
     }

     public function getActivityTypeByName($name) {
         if (!$name) return false;
         return Event_Service_Activity::getEventTypeDao()->getBy(array('name' => $name));
     }

     public static function getPrizeList($type_id,$sync=false){
         $tKey     = "3G:Seckill:PrizeList" . $type_id;
         $activityPrizeList = Common::getCache()->get($tKey);
         if (empty($activityPrizeList) || $sync) {
             $activityPrizeList = self::getActivityPrizeList($type_id);
             Common::getCache()->set($tKey, $activityPrizeList, Common::T_ONE_DAY);
         }
         return $activityPrizeList;
     }

     public function getActivityPrizeList($type_id) {
         if (!$type_id) return false;
         $where =array();
         if(intval($type_id)){
             $where['activity_type'] = $type_id;
         }
         $data = Event_Service_Activity::getGoodsDao()->getsBy($where,array("sort"=>"ASC"));
         /*
         foreach ($data as $k=>$v){
             $type = Event_Service_Activity::getEventTypeDao()->get($v['activity_type']);
             $data[$k]['type_name'] = $type['name'];
         }
         */
         return $data;
     }

     public  static function getUserPrizeList($uid,$type_id,$sync=false){
         $rcKey = "Event:Seckill:User:Prize:{$type_id}:{$uid}";
         $prizeInfo = Common::getCache()->get($rcKey);
         if(empty($prizeInfo) || $sync){
             $where = array(
                 'uid'		    =>$uid,
                 'activity_id'	=>$type_id,
             );
             $prizeInfo = Event_Service_Activity::getResultDao()->getsBy($where);
             Common::getCache()->set($rcKey,$prizeInfo,300);
         }
         return $prizeInfo;
     }

     public  static function getUserPrizeById($uid,$type_id,$prize_id,$sync=false){
         $rcKey = "Event:Seckill:User:PrizeInfo:{$type_id}:{$uid}:{$prize_id}";
         $prizeInfo = Common::getCache()->get($rcKey);
         if(empty($prizeInfo) || $sync){
             $where = array(
                 'uid'		    =>$uid,
                 'prize_id'	    =>$prize_id,
                 'activity_id'	=>$type_id,
             );
             $prizeInfo = Event_Service_Activity::getResultDao()->getBy($where);
             Common::getCache()->set($rcKey,$prizeInfo,300);
         }
         return $prizeInfo;
     }
 }
