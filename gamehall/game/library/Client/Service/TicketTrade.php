<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Client_Service_TicketTrade{

    const GRANT_CAUSE_A_COUPON_WEALTASK = 1; //福利任务
    const GRANT_CAUSE_A_COUPON_DAILYTASK = 2; //福利任务
    const GRANT_CAUSE_A_COUPON_ACTIVITY = 3; //A券活动
    const GRANT_CAUSE_A_COUPON_MANUALSEND = 4; //手动赠送
    const GRANT_CAUSE_A_COUPON_PRIZESEND = 5; //抽奖发放
    const GRANT_CAUSE_A_COUPON_MALLSEND = 6; //商城兑换
    const GRANT_CAUSE_A_COUPON_USERSEND = 7; //用户赠送
    const GRANT_CAUSE_A_COUPON_FESTIVAL = 8; //节日活动
    const GRANT_CAUSE_A_COUPON_SUMMER = 9; //暑假活动
    const GRANT_CAUSE_A_COUPON_TOUCHGAME = 10; //划屏游戏
    const GRANT_CAUSE_A_COUPON_VIP = 11; //VIP赠送
    
    
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy= array('start_time'=>'DESC', 'id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

    public static function getListGroupBy($page, $limit = 10, $params = array(), $field){
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        return self::_getDao()->getListGroupBy($start, $limit, $params, $field);
    }

    public static function countGroupBy($params, $field){
        if (!is_array($params) || !$field ) return false;
        return self::_getDao()->countGroupBy($params, $field);
    }

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNum($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getNum($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getCount($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getCount($params);
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getBy($params = array() , $orderBy = array('id'=>'ASC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;

	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getsBy($params = array(), $orderBy = array('id'=>'ASC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteBy($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @param unknown_type $btype
	 * @return Ambigous <boolean, mixed>
	 */
	public static function mutinsert($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiInsert($data);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function mutiFieldInsert($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiFieldInsert($data);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function getConsumeRank($page = 1, $limit = 10, $params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getConsumeRank($page = 1, $limit = 10, $params);
	}
	
	/**
	 * 获取游戏券为全平台的apiKey
	 * @return mixed
	 */
	public static function getGameHallApikey(){
	    $config = Common::getConfig('paymentConfig', 'payment_send');
	    return $config['api_key'];
	}
	
	/**
	 * 获取A券支付使用apiKey
	 * @return mixed
	 */
	public static  function getPaymentApiKey(){
	    $config = Common::getConfig('paymentConfig', 'payment_send');
	    return $config['pay_api_key'];
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
        if(isset($data['ticket_type'])) $tmp['ticket_type'] = $data['ticket_type'];
        if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
        if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['aid'])) $tmp['aid'] = $data['aid'];
		if(isset($data['denomination'])) $tmp['denomination'] = $data['denomination'];
		if(isset($data['use_denomination'])) $tmp['use_denomination'] = $data['use_denomination'];
        if(isset($data['balance'])) $tmp['balance'] = $data['balance'];
        if(isset($data['useable'])) $tmp['useable'] = $data['useable'];
        if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['send_type'])) $tmp['send_type'] = $data['send_type'];
		if(isset($data['sub_send_type'])) $tmp['sub_send_type'] = $data['sub_send_type'];
		if(isset($data['consume_time'])) $tmp['consume_time'] = $data['consume_time'];
		if(isset($data['out_order_id'])) $tmp['out_order_id'] = $data['out_order_id'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if(isset($data['densection'])) $tmp['densection'] = $data['densection'];
		if(isset($data['description'])) $tmp['description'] = $data['description'];
		if(isset($data['third_type'])) $tmp['third_type'] = $data['third_type'];
		return $tmp;
	}
	
	public static function getListByEndTime($uuid, $sDate, $eDate) {
	    return self::_getDao()->getListByEndTime($uuid, $sDate, $eDate);
	}

    /**
     * @param $sendType
     * @param $subSendType
     * @return string
     */
    public static function getTaskId($sendType, $subSendType){
        return "{$sendType}-{$subSendType}";
    }

	public static function getRowCount($params) {
		return self::_getDao()->count($params);
	}

    public static function getDetailList($page = 1, $limit = 10, $params = array(), $orParams = array(), $orderBy= array('start_time'=>'DESC', 'id'=>'DESC')) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getDetailList($start, $limit, $params, $orParams, $orderBy);
        $total = self::_getDao()->countDetailList($params, $orParams);
        return array($total, $ret);
    }

    public static function getUserTradeId($uuid, $time, $ticketType, $gameId, $useable =1){
        $data = self::getUserTrade($uuid, $time, $ticketType, $gameId, $useable);
        if(!$data)  return array();
        $data = Common::resetKey($data, 'id');
        $tradeIds = array_keys($data);
        return $tradeIds;
    }

    public static function getUserTicketBalance($uuid, $time, $ticketType, $gameId){
        $userTrade = self::getUserTrade($uuid, $time, $ticketType, $gameId, 1);
        if(!$userTrade) return 0;
        $userTradeBlance = self::getTicketBlance($userTrade);
        if(!$userTradeBlance) return 0;
        $balance = self::sumTicketBlance($userTradeBlance);
        return $balance;
    }

    public static function getTaskInfo($id, $type, $subType){
        $info = self::getBy(array('id'=>$id));
        switch($type){
            case 1:
                //福利任务
                return '福利任务';
                break;
            case 2:
                //每日任务
                return '每日任务';
                break;
            case 3:
                //A券活动
                $taskInfo = Client_Service_TaskHd::getBy(array('id'=>$subType));
                return $taskInfo['title'];
                break;
            case 4:
                //手动赠送
                return $info['description'];
                break;
            case 5:
                //抽奖活动
                $prizeInfo = Point_Service_Prize::getByPrize(array('id'=>$subType));
                return $prizeInfo['title'];
                break;
            case 6:
                //商城兑换
                return '积分商城兑换';
                break;
            case 7:
                //用户赠送
                $sendInfo = ($subType ==1) ? '生日礼物' : '金立游戏大厅赠送';
                return $sendInfo;
                break;
            case 8:
                //节日活动
                $activityInfo = Festival_Service_BaseInfo::getBy(array('id'=>$subType));
                return $activityInfo['title'];
                break;
            case 9:
                //暑假活动
                $activityInfo = Activity_Service_Cfg::getBy(array('id'=>$subType));
                return $activityInfo['name'];
                break;
            case 10:
                //划屏游戏
                $activityInfo = Festival_Service_TouchGame::getByInfo(array('id'=>$subType));
                return $activityInfo['name'];
                break;
            case 11:
                //VIP赠送
                return 'VIP 赠送';
                break;
            default:
                return "金立游戏大厅赠送";
        }
    }

    private static function getUserTrade($uuid, $time, $ticketType, $gameId ,$useable){
        $params = array(
            'uuid' => $uuid,
            'status' => 1,
            'start_time' => array('<=', $time),
            'end_time' => array('>=', $time),
            'useable' => $useable
        );
        if($gameId){
            $params['game_id'] = $gameId;
        }
        if($ticketType){
           $params['ticket_type'] = $ticketType;
        }
        $data = Client_Service_TicketTrade::getsBy($params);
        if(!$data)  return array();
        return $data;
    }

    private static function getTicketBlance($tradeData){
        $str = '';
        $temp = array();
        foreach ($tradeData as $v){
            $temp[]['ano'] = $v['out_order_id'];
            $str .=$v['out_order_id'];
        }

        //取得配置
        $payment_arr = Common::getConfig('paymentConfig','payment_send');
        $pri_key = $payment_arr['ciphertext'];
        $api_key    = $payment_arr['api_key'];
        $url       = $payment_arr['ticket_list_url'];
        $params['api_key'] = $api_key;
        $params['token'] = md5($pri_key.$api_key.$str);
        $params['data'] = $temp;

        //post到支付服务器
        $response = Util_Http::post($url, json_encode($params), array('Content-Type' => 'application/json'), 2);
        $result = json_decode($response->data,true);
        return $result;
    }

    private static function sumTicketBlance($ticketBlance){
        $balance = 0;
        foreach ($ticketBlance['data'] as $item){
            $balance += $item['balance'];
            $useable = ($item['balance']) ? 1 : 0;
            //tbd
            self::updateBy(array('balance' => $item['balance'], 'useable'=>$useable), array('out_order_id'=>$item['ano']));
        }
        return $balance;
    }

	/**
	 * 
	 * @return Client_Dao_TicketTrade
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_TicketTrade");
	}
}