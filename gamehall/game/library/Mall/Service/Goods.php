<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Mall_Service_Goods extends Common_Service_Base{
	const ENTITY = 1;             //实体奖品
	const ACOUPON = 2;            //Ａ券游戏券
	const GIFT = 3;               //游戏礼包
	const DISCOUNT_COUPON = 4;     //优惠券
	const PHONE_RECHARGE_CARD = 5; //话费,充值卡
	const STATUS_CLOSE = 0;
	const STATUS_OPEN = 1;
	const SECKILL_CATEGORY = 1;  //商品秒杀类型
	const SINGLE_GIFT = 1;       //单个礼包
	const MULTIPLE_GIFT = 2;     //多个礼包
	const ALL_PLATFORM = 1;      //全平台
	const GAME_HALL = 2;         //游戏大厅
	const APPOINT_GAMES = 3;     //指定游戏
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('sort'=>'DESC','start_time'=>'DESC', 'id' => 'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
    public static function getRemainNum($id) {
		if (!intval($id)) return false;
		$goodInfo =  self::_getDao()->get(intval($id));
		return $goodInfo['remaind_num'];
	}
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getBy($params = array(),$orderBy = array('sort'=>'DESC','create_time'=>'DESC')) {
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	public static function count($params) {
	    if (!$params) return false;
	    return self::_getDao()->count($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getsBy($params) {
		return self::_getDao()->getsBy($params, array('sort'=>'DESC','start_time'=>'DESC','id'=>'DESC'));
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
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	public static function updateSort($sorts) {
	    foreach($sorts as $key=>$value) {
	        self::_getDao()->update(array('sort'=>$value), $key);
	    }
	    return true;
	}
	
	public static function addGood($info, $giftCodes) {
	    if (!is_array($info)) return false;
	     
	    $trans = parent::beginTransaction();
	    try {
	        $goodId = self::addBaseInfo($info, $giftCodes);
	        	
	        if($trans) {
	            $result = parent::commit();
	            return $result;
	        }
	    } catch (Exception $e) {
	        parent::rollBack();
	        return false;
	    }
	    return true;
	}
	
	private static function addBaseInfo($info, $giftCodes) {
	    $goodId = self::add($info);
	    if (!$goodId) {
	        throw new Exception("Add Good Information Fail.", -201);
	    }
	     
	    if($info['type'] == Mall_Service_Goods::GIFT){
	        $ret = Mall_Service_GiftExchangeLog::addGiftExchangeCodes($giftCodes, $goodId, $info);
	        if (!$ret) {
	            throw new Exception("Add Gift Exchange Codes Fail.", -202);
	        }
	    }
	    
	    return $goodId;
	}
	
	public function editGiftExchangeGood($goodInfo, $oldExchangeInfo) {
	    $ret = self::update($goodInfo, $goodInfo['id']);
	    if(!$ret){
	        throw new Exception("update Good Information Fail.", -203);
	    }
	
	    if($goodInfo['gift_num_type'] == self::SINGLE_GIFT){
	        $retLog = Mall_Service_GiftExchangeLog::updateEditGiftExchangeLog($goodInfo, $oldExchangeInfo);
	        if(!$retLog){
	            throw new Exception("update Gift Exchange Log Information Fail.", -204);
	        }
	    }
	    
	    $remainGiftCount = Mall_Service_GiftExchangeLog::getRemainGiftExchangeCodeCount($goodInfo['id']);
	    $ret = self::updateBy(array('remaind_num'=>$remainGiftCount), array('id'=>$goodInfo['id']));
	    if(!$ret){
	        throw new Exception("update Good Remaind Num Information Fail.", -205);
	    }
	    
	    return true;
	}
	
	public function assembleGoodInfo($good, $parmes) {
	    $webroot = Common::getWebRoot();
	    $detailUrl = urldecode($webroot. '/client/Mall/exchangeDetail/?goodId=' . $good['id'].'&puuid='.$parmes['puuid'].'&uname='.$parmes['uname'].'&imei='.$parmes['imei'].'&sp='.$parmes['sp'].'&serverId='.$parmes['serverId']);
	    if($good['discountArr']){
	        $discountArr = json_decode($good['discountArr'],true);
	        $discountPoint =  round($good['consume_point'] * ($discountArr['discount'] / 10),0);
	        $discountPoint = ($discountPoint < 1 ? 1 : $discountPoint);
	    }
	    return array($detailUrl, $discountPoint, $discountArr);
	}
	
	public function convertPointVal($point) {
	    if(!$point)  return 0;
	    $baseNumber = 10000;
	    if($point >= $baseNumber){
	        return sprintf("%.1f", $point/10000).'万';
	    }else{
	        return $point;
	    }
 	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
		if(isset($data['type'])) $tmp['type'] = intval($data['type']);
		if(isset($data['gift_num_type'])) $tmp['gift_num_type'] = $data['gift_num_type'];
		if(isset($data['total_num'])) $tmp['total_num'] = intval($data['total_num']);
		if(isset($data['preson_limit_num'])) $tmp['preson_limit_num'] = $data['preson_limit_num'];
		if(isset($data['remaind_num'])) $tmp['remaind_num'] = $data['remaind_num'];
		if(isset($data['consume_point'])) $tmp['consume_point'] = $data['consume_point'];
		if(isset($data['discountArr'])) $tmp['discountArr'] = $data['discountArr'];
		if(isset($data['game_object'])) $tmp['game_object'] = $data['game_object'];
		if(isset($data['game_ids'])) $tmp['game_ids'] = $data['game_ids'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['exchange_rule'])) $tmp['exchange_rule'] = $data['exchange_rule'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['effect_time'])) $tmp['effect_time'] = intval($data['effect_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Mall_Dao_Goods
	 */
	private static function _getDao() {
		return Common::getDao("Mall_Dao_Goods");
	}
}
