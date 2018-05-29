<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Cut_Service_Goods{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGoods() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
    /**
     * 查询当前商品的砍价状态
     * @param integer $goods_id
     * @param string $uid
     * @param string $refer
     * @return array
     */
    public static function getCutStatus($goods_id, $uid, $refer) {
	    if(!is_array($goods_id)) $goods_id = array($goods_id);
        $goods = self::getGoodsByIds($goods_id);
        $goods = Common::resetKey($goods, 'id');
        $webroot = Common::getWebRoot();
        
        $data = array();
        $time = Common::getTime();
        foreach ($goods as $key=>$value) {
            
            //logs
           list($total, $logs) = Cut_Service_Log::getsBy(array('goods_id'=>$value['id']), array('id'=>'DESC'));
           $log_list = Common::resetKey($logs, 'uid');
            
            $data[$key]['id'] = $value['id'];
            $data[$key]['current_price'] = $logs ? $logs[0]['price'] : $value['price'];
            $data[$key]['is_cut'] = true;
            $data[$key]['is_buy'] = false;
            $data[$key]['cut_code'] = 0;
            $data[$key]['cut_msg'] = '砍价';
            $data[$key]['cut_info'] = $total.'人已砍价';
            
            //未开始
            if($value['start_time'] > $time) {
                $data[$key]['is_cut'] = false;
                $data[$key]['is_buy'] = false;
                $data[$key]['cut_code'] = 1;
                $data[$key]['cut_msg'] = '未开始';
                $data[$key]['cut_info'] = self::formatTime($value['start_time']).'开始';
                $data[$key]['tips'] = '还没有开始哦';
                continue;
            }
            
            //已被购买、锁定、已下架、订单数大于2、已过期的
            //if((in_array($value['status'], array(2,3,4,5))) || ($value['sale_num']>=5) || ($value['end_time'] < $time) || ($value['quantity'] == 0)) {
            if((in_array($value['status'], array(2,3,4,5))) || ($value['end_time'] < $time) || ($value['quantity'] == 0)) {
                $data[$key]['is_cut'] = false;
                $data[$key]['is_buy'] = false;
                $data[$key]['cut_code'] = 6;
                $data[$key]['cut_msg'] = '已抢光';
                $data[$key]['cut_info'] = $total.'人已砍价';
                $data[$key]['tips'] = '今天没有了，看看其他的吧';
                continue;
            }
            
            //不能再低了
            if($logs[0]['price'] == $value['min_price']) {
                $data[$key]['is_cut'] = false;
                $data[$key]['is_buy'] = true;
                $data[$key]['cut_code'] = 4;
                $data[$key]['cut_msg'] = '砍价';
                if($refer == 'detail') {
                    $data[$key]['is_cut'] = false;
                    $data[$key]['cut_msg'] = '抢购';
                }
                $data[$key]['cut_info'] = $total.'人已砍价';
                $current_price = $logs ? $logs[0]['price'] : $value['price'];
                $data[$key]['current_price'] = $current_price.'(最低价)';
                $data[$key]['tips'] = '最低价啦，赶紧抢购吧';
                continue;
            }
             
           //已砍过价
           if($uid && $log_list[$uid]) {
               $data[$key]['is_cut'] = false;
               $data[$key]['is_buy'] = true;
               $data[$key]['cut_code'] = 3;
               $data[$key]['cut_msg'] = '找人砍';
               if($refer == 'detail') {
                   $data[$key]['cut_msg'] = '立即购买';
               }
               $data[$key]['cut_info'] = $total.'人已砍价';
               $data[$key]['tips'] = '您已砍过价,再找朋友一起来吧';
               continue;
           }
           
        }    
	    
	    return $data;
	}

	public static function getNotCutCount() {
	    return self::_getDao()->getNotCutCount() - self::_getDao()->getWillCutCount();
	}

    /**
     *
     * @param $id
     * @return bool|mixed
     */
    public static function get($id){
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * @param $params
     * @param array $sort
     * @return array|bool
     */
    public static function getsBy($params,$sort=array()) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        $ret = self::_getDao()->getsBy($params,$sort);
        return array($total, $ret);
    }


    /**
     * 砍价
     * @param int $goods_id
     * @param string $uid
     * @return bool
     */
    public static function cut($goods_id, $uid, $refer) {
        //当前价格
        $goods = Cut_Service_Goods::getGoods($goods_id);
        $current_price = $goods['price'];
        
        $log = Cut_Service_Log::getBy(array('goods_id'=>$goods_id), array('id'=>'DESC'));
        if($log) $current_price = $log['price'];
        
        //计算砍价价格
        if(Common::money($current_price - $goods['range']) >= Common::money($goods['min_price'])) {
            $price = Common::money($current_price - $goods['range']);
            $range = Common::money($goods['range']);
        } else {
            $price = Common::money($goods['min_price']);
            $range = Common::money($log['price'] - $goods['min_price']);
        }
        
        $log_data = array(
            'goods_id'=>$goods['id'],
            'uid'=>$uid,
            'create_time'=>Common::getTime(),
            'price'=>$price
        );
        $log_ret = Cut_Service_Log::addLog($log_data);
        if(!$log_ret) return false;
        
        $status = self::getCutStatus($goods_id, $uid, $refer);
        $status[$goods_id]['range'] = Common::money($range);
        $status[$goods_id]['detail_url'] = Common::getWebRoot().'/cut/detail?id='.$goods['id'];
        return $status[$goods_id];
	        
	}
	

    /**
     * get uuid via user agent
     * @return int|mixed|string
     */
    public static function getUid($system) {
        //默认是andriod系统
        if(!$system) {
            $uid = Common::getAndroidtUid();
            if(!$uid) $uid = Common::getIMEI();
        } else {
            $uid = Common::getIosUid();
        }
	    
	    if(!$uid) {
	        $uid = Util_Cookie::get('GOUID');
	        if (!$uid) {
	            $uid = crc32(Util_Http::getServer('HTTP_USER_AGENT') . Util_Http::getClientIp());
	            Util_Cookie::set('GOUID', $uid, false, strtotime("+360 day"), '/');
	        }
	    }
	    return $uid;
	}
	

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(),$sort=array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * @param array $params
	 * @return array
	 */
	public static function getCount($params = array()) {
	    $params = self::_cookData($params);
	    $total = self::_getDao()->count($params);
	    return $total;
	}

    public static function getImageLink($url,$width=0,$height=0){
        $height = !$height ? $width : $height;
        $attachPath = Common::getAttachPath();
        if(strpos($url, 'http://') === false){
            return $attachPath.$url;
        }else{
            if(!$width){
                return $url;
            }
            return sprintf("%s_%sx%s.%s", $url, $width, $height, 'jpg');
        }
    }

    /**
     * get Goods info by ids
     * @param $ids
     * @return mixed
     */
    public static function getGoodsByIds($ids) {
		if (!count($ids)) return false;
		return self::_getDao()->getGoodsByIds($ids);
	}

    public static function getGoods($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

    public static function getBy($params = array(), $sort = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params, $sort);
    }


	public static function updateGoods($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	public static function updateQuantity($num, $id) {
	    return self::_getDao()->updateQuantity($num, $id);
	}
	

	public static function deleteGoods($id) {
		return self::_getDao()->delete(intval($id));
	}
	

	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addGoods($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['quantity'] = 1;
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateSaleNum($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('sale_num', array('id'=>intval($id)));
	}
	
	
	/**
	 * 
	 * @param int $time
	 */
	public static function formatTime($time) {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        $time_diff = $time - strtotime(date('Y-m-d', Common::getTime()));

        //大于2天
        if($time_diff >= 172800 || $time_diff < 0) return  date('m-d H:i', $time);

        //大于1天
        if($time_diff >= 86400) return  sprintf("明天%s", date('H:i', $time));

        //今天内
        if($time_diff < 86400) return  sprintf("今天%s", date('H:i', $time));
	}


    private static function _cookData($data) {
		$tmp = array();
        if(isset($data['id']))           $tmp['id']         = $data['id'];
        if(isset($data['sort']))         $tmp['sort']       = $data['sort'];
        if(isset($data['store_id']))     $tmp['store_id']   = intval($data['store_id']);
        if(isset($data['shop_id']))      $tmp['shop_id']    = intval($data['shop_id']);
        if(isset($data['type_id']))      $tmp['type_id']    = intval($data['type_id']);
        if(isset($data['author_id']))    $tmp['author_id']  = intval($data['author_id']);
        if(isset($data['title']))        $tmp['title']      = $data['title'];
        if(isset($data['author_say']))   $tmp['author_say'] = $data['author_say'];
        if(isset($data['price']))        $tmp['price']      = $data['price'];
		if(isset($data['min_price']))    $tmp['min_price']  = $data['min_price'];
		if(isset($data['range']))        $tmp['range']      = $data['range'];
		if(isset($data['start_time']))   $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time']))     $tmp['end_time']   = $data['end_time'];
		if(isset($data['status']))       $tmp['status']     = $data['status'];
		if(isset($data['quantity']))     $tmp['quantity']   = $data['quantity'];
		if(isset($data['sale_num']))     $tmp['sale_num']   = $data['sale_num'];
		if(isset($data['shortest_time']))$tmp['shortest_time'] = floatval($data['shortest_time']);
		if(isset($data['join_count']))   $tmp['join_count'] = intval($data['join_count']);
		if(isset($data['no']))           $tmp['no']         = intval($data['no']);
		if(isset($data['uid']))          $tmp['uid']        = $data['uid'];
		if(isset($data['increase']))     $tmp['increase']   = $data['increase'];
		return $tmp;
	}
	
	/**
	 * @return Cut_Dao_Goods
	 */
	private static function _getDao() {
		return Common::getDao("Cut_Dao_Goods");
	}
}
