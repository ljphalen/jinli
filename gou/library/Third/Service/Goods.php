<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_Service_Goods{

    public static $channel=array(
         	1=>'淘宝',
         	2=>'天猫',
         	3=>'京东',
         	4=>'买卖宝'
    );
    public static $status = array(
    		0=>'未抓取',
    		1=>'抓取中',
    		2=>'已抓取',
    		4=>'已下架',
    );

	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;

		list(,$goods) = Third_Service_GoodsUnipid::getList($page  , $limit , $params);
		foreach ($goods as $v) {
			$tmp  =static::get($v['goods_id']);
			if(!empty($tmp))$rows[] = $tmp;
		}

		return array(count($rows), $rows);
	}

	public static function getGoodsUrl($goods_id,$channel_id){
		switch ($channel_id) {
			case 1://淘宝
				return sprintf('http://h5.m.taobao.com/awp/core/detail.htm?id=%s', $goods_id);
				break;
			case 2://天猫
				return sprintf('http://detail.m.tmall.com/item.htm?id=%s', $goods_id);
				break;
			case 3://京东
				return sprintf('http://m.jd.com/product/%s.html', $goods_id);
				break;
			case 4://买卖宝
				return sprintf('http://mmb.cn/wap/touch/html/product/id_%s.htm', $goods_id);
				break;
			default:
				break;
		}
		return false;
	}

    public static function getImageUrl($image){
        $staticroot = Common::getAttachPath();
        if(strpos($image, 'http://')===false&&!empty($image))$image = $staticroot.$image;
        return $image;
    }

    /**
     * @param $data
     * @return bool|string
     */
	public static function addGoods($data) {
		if (!is_array($data)) return false;

		$extra = array();
        //过滤data字段json数据
        if(!empty($data["data"])){
            self::_cookExtra($extra, json_decode($data["data"], true));
            $data["data"] = json_encode($extra);
        }
        //过滤数据
		$data = self::_cookData($data);
        $data['update_time'] = Common::getTime();
        $goods_id = $data['goods_id'];
        unset($data['goods_id']);

        //如果已经存在，直接更新数据
		$ret = self::_getDao($goods_id)->getBy(array("goods_id" => $goods_id));
        //降价提醒
		if(!empty($data['price'])){
			$new_price=$ret['price']?$ret['price']:0.00;
			//if current price is lower than pre time plus 1
			User_Service_Favorite::updatePrice($goods_id, $data['price'], $new_price>$data['price']+1);
		}

		if ($ret) {
			$extra = json_decode($ret["data"], true);
			self::_cookExtra($extra, json_decode($data["data"], true));
			$data["data"] = json_encode($extra);
			Third_Service_GoodsUnipid::update($data, $goods_id);
			self::_getDao($goods_id)->updateBy($data, array("goods_id" => $goods_id));
			return $ret["id"];
		}

        $data['goods_id'] = $goods_id;

		//新插入的数据 ,插入索引表
		Third_Service_GoodsUnipid::replace($data);
		$res = self::_getDao($goods_id)->insert($data);
		if (!$res) {
			return false;
		}
		return self::_getDao($goods_id)->getLastInsertId();
	}

	/**
	 * mutil insert
	 * @param $data
	 * @return bool|int
	 */
	public static function insert($data){
		if (!is_array($data)) return false;
		foreach ($data as $key => $value) {
			self::addGoods($value);
		}
		return true;
	}

    /**
     * @param int $key
     * @param array $data
     * @return bool|int
     */
	public static function mInsert($key,$data){
		if (!is_array($data)) return false;
		return self::_getDao($key)->mutiInsertByKeys($data);
	}


    /**
     * @param $goods_id
     * @return bool|mixed
     */
	public static function get($goods_id) {
	    if(!$goods_id) return false;
	    return self::_getDao($goods_id)->getBy(array("goods_id"=>$goods_id));
	}

    /**
     * @param $data
     * @param $goods_id
     * @return bool
     */
	public static function update($data, $goods_id) {
	    if (!$goods_id) return false;
	    $data = self::_cookData($data);
	    Third_Service_GoodsUnipid::update($data, $goods_id);
	    return self::_getDao($goods_id)->updateBy($data, array("goods_id"=>$goods_id));
	}

    /**
     * @param $goods_id
     * @return bool
     */
	public static function delete($goods_id) {
	    return self::_getDao($goods_id)->deleteBy(array("goods_id"=>$goods_id));
	}

	private static function _cookExtra(&$org, $data) {
		if(isset($data['score']))       $org["score"]       = $data["score"];
		if(isset($data['pay_num']))     $org["pay_num"]     = $data["pay_num"];
		if(isset($data['comment_num'])) $org["comment_num"] = $data["comment_num"];
		if(isset($data['area']))        $org["area"]        = $data["area"];
		if(isset($data['shop_title']))  $org["shop_title"]  = $data["shop_title"];
		if(isset($data['shop_level']))  $org["shop_level"]  = $data["shop_level"];
		return $org;
	}

    /**
     * @param $data
     * @return array
     */
	private static function _cookData($data) {
		$channels = Client_Service_Spider::channels($data['channel']);

		$tmp = array();
		if(isset($data['id']))              $tmp['id']               = $data['id'];
		if(isset($data['channel_id']))      $tmp['channel_id']       = $data['channel_id'];
		if(isset($data['channel']))         $tmp['channel_id']       = $channels['channel_id'];
		if(isset($data['goods_id']))        $tmp['goods_id']         = $data['goods_id'];
		if(isset($data['item_id']))         $tmp['goods_id']         = $data['item_id'];
		if(isset($data['unique_pid']))      $tmp['unique_pid']       = $data['unique_pid'];
		if(isset($data['title']))           $tmp['title']            = $data['title'];
		if(isset($data['price']))           $tmp['price']            = $data['price'];
		if(isset($data['img']))             $tmp['img']              = $data['img'];
		if(isset($data['data']))            $tmp['data']             = $data['data'];
		if(isset($data['request_count']))   $tmp['request_count']    = $data['request_count'];
		if(isset($data['status']))          $tmp['status']           = $data['status'];
		if(isset($data['update_time']))     $tmp['update_time']      = $data['update_time'];
		if(isset($data['sort']))            $tmp['sort']             = $data['sort'];
		if(isset($data['favorite_count']))  $tmp['favorite_count']   = $data['favorite_count'];
		if(isset($data['system']))          $tmp['system']           = $data['system'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Third_Dao_Goods
	 */
	private static function _getDao($goods_id) {
		$dao = new Third_Dao_Goods();
		$dao->goods_id = $goods_id;
		return $dao;
	}
}
