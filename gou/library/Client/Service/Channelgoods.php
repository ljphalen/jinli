<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author huangsg
 *
 */
class Client_Service_Channelgoods {
	
	//缓存1天。过期后从接口重新拿数据
	protected static $cacheTime = 86400;
	
	/**
	 * 获取商品列表数据
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取商品信息
	 * @param unknown $id
	 * @return boolean|mixed
	 */
	public static function getGoods($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 根据第三方商品ID获取商品信息
	 * @param string $goodsID  格式：第三方标识串_商品ID
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getGoodsBySign($goodsID){
		if (empty($goodsID)) return false;
		return self::_getDao()->getsBy(array('goods_id'=>$goodsID));
	}
	
	/**
	 * 添加商品
	 * @param unknown $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addGoods($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改商品
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateGoods($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function updateGoodsHits($id, $column){
		return self::_getDao()->increment($column, array('id'=>intval($id)));
	}
	
	/**
	 * 删除商品
	 * @param unknown $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteGoods($id){
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * sort
	 * @param array $sort
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
	 * @param array $ids
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updates($ids, $data) {
	    if (!is_array($data) || !is_array($ids)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updates('id', $ids, $data);
	}

	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['category_name'])) $tmp['category_name'] = $data['category_name'];
		if(isset($data['goods_type'])) $tmp['goods_type'] = intval($data['goods_type']);
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['market_price'])) $tmp['market_price'] = $data['market_price'];
		if(isset($data['market_price_min'])) $tmp['market_price_min'] = $data['market_price_min'];
		if(isset($data['sale_price_min'])) $tmp['sale_price_min'] = $data['sale_price_min'];
		if(isset($data['sale_price'])) $tmp['sale_price'] = $data['sale_price'];
		if(isset($data['supplier'])) $tmp['supplier'] = $data['supplier'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['module_id']) && isset($data['cid'])) $tmp['module_channel'] = intval($data['module_id']).'_'.intval($data['cid']);
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		return $tmp;
	}

	/**
	 * 获取统计的短链接
	 * @param $version_id
	 * @param $value
	 * @return string
	 */
	public static function getShortUrl($version_id, $value) {
		list($model_id, $channel_id) = explode('_', $value['module_channel']);

		$channel_codes = Stat_Service_Log::$V_ARRAY;
		$configs = Common::getConfig('tejiaConfig');
		$config = $configs[$value['supplier']];
		$channel_code = $config[$channel_codes[$version_id].'_channel_code'];
		$goodsUrl = html_entity_decode($value['link']);
//		$goodsUrl .= strpos($goodsUrl, '?') === false ? '?' : '&';	//追加渠道号码

		//需要特殊处理
		if ($value['supplier'] == 6) {
			$goodsUrl = str_replace('gionee3', 'gionee8', html_entity_decode($value['link']));
		}

//		http://m.meilishuo.com/share/item/3043108215?nmref=NM_s10452_0_&channel=40106
		if ($value['supplier'] == 9) {
			$goodsUrl = str_replace('NM_s10452_0_', 'NM_s10452_0_'.$config[$channel_codes[$version_id].'_channel_code'], html_entity_decode($value['link']));
		}

		$formart = $config[$channel_codes[$version_id].'_formart'];
		$goodsUrl = sprintf($formart, $goodsUrl);
		return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $goodsUrl, $value['title'], $channel_code);
	}

	/**
	 *
	 * @return Client_Dao_Channelgoods
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Channelgoods");
	}
}