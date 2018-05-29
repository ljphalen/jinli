<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Mall_Service_GiftExchangeLog extends Common_Service_Base{
    const GIFT_ALREADY_EXCHANGE = 1; //礼包已兑换
    const GIFT_NOT_EXCHANGE = 0;     //礼包未兑换
    
    /**
     *
     * @param int $page
     * @param int $limit
     * @param params $params
     * @return multitype:unknown
     */
    public static function getList($page, $limit, $params = array(), $orderBy = array('send_order'=>'DESC', 'create_time' => 'DESC')) {
        if(intval($page) < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::getDao()->getList(intval($start), intval($limit), $params, $orderBy);
        $total = self::getDao()->count($params);
        return array($total, $ret);
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getBy($params = array(), $orderBy = array('send_order'=>'DESC', 'create_time' => 'DESC')) {
        return self::getDao()->getBy($params, $orderBy);
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getsBy($params = array(), $orderBy = array('send_order'=>'DESC', 'create_time' => 'DESC')) {
        return self::getDao()->getsBy($params, $orderBy);
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getById($id) {
        if (!intval($id)) return false;
        return self::getDao()->get(intval($id));
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateById($data, $id) {
        if (!is_array($data)) return false;
        return self::getDao()->update($data, intval($id));
    }
    
    /**
     * 
     * @param unknown $data
     * @param unknown $params
     * @return boolean
     */
    public static function updateBy($data, $params) {
        if (!is_array($params) || !is_array($data) ){
            return false;
        }
        return self::getDao()->updateBy($data, $params);
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function deleteById($id) {
        return self::getDao()->delete(intval($id));
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function mutiGiftLog($data) {
        if (!is_array($data)) return false;
        return self::getDao()->mutiInsert($data);
    }
    
    /**
     * 
     * @param unknown $params
     * @return boolean
     */
    public static function deleteBy($params) {
        return self::getDao()->deleteBy($params);
    }
    
   /**
    * 
    * @param unknown $params
    * @return int
    */
    public static function getGiftLogCount($params) {
        return self::getDao()->count($params);
    }
    
	public static function addGiftExchangeCodes($giftExchangeCodes, $goodId, $goodInfo, $addGiftNumber = 0) {
	    $giftLog = self::assembleGiftExchange($giftExchangeCodes, $goodId, $goodInfo, $addGiftNumber);
	    //echo "<pre>";
	    //print_r($giftLog);exit;
	    $ret = self::mutiGiftLog($giftLog);
	    if (!$ret) return false;
	    return true;
	}
	
	private static function assembleGiftExchange($giftExchangeCodes, $goodId, $goodInfo, $addGiftNumber = 0) {
	    $giftLog = array();
	    if (!is_array($giftExchangeCodes)) return $giftLog;
	    $giftLog = self::getGiftExchangeArray($giftExchangeCodes, $goodId, $goodInfo, $addGiftNumber);
	    return $giftLog;
	}
	
	private static function getGiftExchangeArray($giftExchangeCodes, $goodId, $goodInfo, $addGiftNumber = 0){
	    $ret = self::getBy(array('good_id'=>$goodId),array('send_order'=>'DESC'));
	    $maxSendOrder = intval($ret['send_order']);
	     
	    $giftLog = array();
	    if($goodInfo['gift_num_type'] == Mall_Service_Goods::SINGLE_GIFT){
	        $giftTotal = $addGiftNumber ? $addGiftNumber : $goodInfo['total_num'];
	        for($i=0; $i< $giftTotal; $i++) {
	            $maxSendOrder ++;
	            $giftLog[] = array(
	                    'id'=>'',
	                    'good_id'=>$goodId,
	                    'uuid'=>'',
	                    'uname'=>'',
	                    'imei'=>'',
	                    'imeicrc'=>'',
	                    'activation_code'=>$giftExchangeCodes[0],
	                    'create_time'=>'',
	                    'status'=>0,
	                    'send_order'=>$maxSendOrder
	            );
	        }
	    } else {
	        foreach($giftExchangeCodes as $key=>$value){
	            $maxSendOrder ++;
	            $giftLog[] = array(
	                    'id'=>'',
	                    'good_id'=>$goodId,
	                    'uuid'=>'',
	                    'uname'=>'',
	                    'imei'=>'',
	                    'imeicrc'=>'',
	                    'activation_code'=>$value,
	                    'create_time'=>'',
	                    'status'=>0,
	                    'send_order'=>$maxSendOrder
	            );
	        }
	    }
	    return $giftLog;
	}
	
	
	public function updateEditGiftExchangeLog($goodInfo, $oldExchangeInfo) {
	    $retLog = self::updateGiftExchangeLog($goodInfo, $oldExchangeInfo);
	    if(!$retLog){
	            return false;
	    }
	    return true;
	}
	
	private function updateGiftExchangeLog($goodInfo, $oldExchangeInfo) {
	    if($goodInfo['total_num'] == $oldExchangeInfo['old_total_num']){
	        return true;
	    }
	     
	    if($goodInfo['total_num'] > $oldExchangeInfo['old_total_num']){
	        $giftTotal = self::getGiftLogCount(array('good_id'=>$goodInfo['id']));
	        $addGiftNumber = $goodInfo['total_num'] - $giftTotal;
	        $giftExchangeCodes = array($oldExchangeInfo['gift_code']);
	        return self::addGiftExchangeCodes($giftExchangeCodes,
	                $goodInfo['id'], $goodInfo, $addGiftNumber);
	    }
	
	    if($goodInfo['total_num'] < $oldExchangeInfo['old_total_num']){
	        $giftTotal = self::getGiftLogCount(array('good_id'=>$goodInfo['id']));
	        $deleteNumber = $giftTotal - $goodInfo['total_num'];
	        if($deleteNumber <= 0) {
	            return false;
	        }
	         
	        list(, $logs) = self::getList(1, $deleteNumber,
	                array('good_id'=>$goodInfo['id'],'status'=>self::GIFT_NOT_EXCHANGE));
	         
	        $ids = $params = array();
	        foreach($logs as $key=>$value){
	            $ids[] = $value['id'];
	        }
	         
	        if(!$ids) {
	            return false;
	        }
	         
	        $params['id'] = array('IN',$ids);
	        $ret = self::deleteBy($params);
	        if(!$ret){
	            return false;
	        }
	         
	        return true;
	    }
	}
	
	
	
	public static function getRemainGiftExchangeCodeCount($goodId) {
	    $remainGiftCount = self::getGiftLogCount(
	            array('status'=>self::GIFT_ALREADY_EXCHANGE,
	                    'good_id'=>$goodId));
	     
	    $giftTotal = self::getGiftLogCount(array('good_id'=>$goodId));
	    $remainGiftCount = $giftTotal - $remainGiftCount;
	    return $remainGiftCount;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['good_id'])) $tmp['good_id'] = $data['good_id'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imeicrc'])) $tmp['imeicrc'] = $data['imeicrc'];
		if(isset($data['activation_code'])) $tmp['activation_code'] = $data['activation_code'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['send_order'])) $tmp['send_order'] = intval($data['send_order']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Mall_Dao_GiftExchangeLog
	 */
	private static function getDao() {
		return Common::getDao("Mall_Dao_GiftExchangeLog");
	}
	
	
}
