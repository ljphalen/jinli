<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * taobao top api
 * @author rainkid
 *
 */
class Api_Top_Base extends Api_Top_Client {
	public $containerUrl = "http://container.open.taobao.com/container";
	
	/**
	 * 过滤url
	 * @param string $url
	 */
	protected function filterUrl($url) {
		return $url;
	}
	
	/**
	 * 登录URL
	 * 
	 * @param string $callbackUrl
	 * @param string $appkey
	 * @return string
	 */
	public function getLoginUrl($callbackUrl, $appkey = null) {
		is_null($appkey) && $appkey = $this->appkey;

		$authUrl = sprintf('%s?appkey=%s&back_url=%s',
			$this->containerUrl,
			$appkey,
			urlencode($callbackUrl)// 回调地址
		);
		
		return sprintf('%s?redirect_url=%s',
			$this->filterUrl('https://login.taobao.com/member/login.jhtml'),
			urlencode($authUrl)
		);
	}
	
	/**
	 * 付款URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getTradePayUrl($bizOrderId) {
		return sprintf('%s?biz_order_id=%s&biz_type=200',
			$this->filterUrl('http://trade.taobao.com/trade/pay.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 交易详情URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getTradeItemDetailUrl($bizOrderId) {
		return sprintf('%s?bizOrderId=%s&his=false',
			$this->filterUrl('http://trade.taobao.com/trade/detail/trade_item_detail.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 确认收货URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getTradeConfirmGoodsUrl($bizOrderId) {
		return sprintf('%s?biz_order_id=%s',
			$this->filterUrl('http://trade.taobao.com/trade/confirm_goods.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 查看物流URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getOrderLogisticsDetailUrl($bizOrderId) {
		return sprintf('%s?trade_id=%s',
			$this->filterUrl('http://wuliu.taobao.com/user/order_detail_new.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 评分URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getRemarkSellerUrl($bizOrderId) {
		return sprintf('%s?trade_id=%s',
			$this->filterUrl('http://rate.taobao.com/remark_seller.jhtml'),
			$bizOrderId
		);
	}
	
	/**
	 * 申请退款URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getFillRefundAgreementUrl($bizOrderId) {
		return sprintf('%s?bizOrderId=%s',
			$this->filterUrl('http://refund.taobao.com/fill_refundAgreement.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 订单快照URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getTradeSnapDetailUrl($bizOrderId) {
		return sprintf('%s?tradeID=%s',
			$this->filterUrl('http://trade.taobao.com/trade/detail/tradeSnap.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 投诉维权URL
	 * 
	 * @param string $bizOrderId 淘宝订单号
	 * @return string
	 */
	public function getRightsplatUrl($bizOrderId) {
		return sprintf('%s?trade_id=%s',
			$this->filterUrl('http://support.taobao.com/myservice/rightsplat/alert_rights.htm'),
			$bizOrderId
		);
	}
	
	/**
	 * 查看用户店铺URL
	 * 
	 * @param string $userNumberId 用户id
	 * @return string
	 */
	public function getViewMallUrl($userNumberId) {
		return sprintf('%s?user_number_id=%s',
			$this->filterUrl('http://store.taobao.com/mall/view_mall.htm'),
			$userNumberId
		);
	}
	
	/**
	 * 获取taobaoke商品手机链接
	 * @param unknown_type $num_iid
	 */
	public function getGoodsMobileUrl($num_iid, $pid) {
		return sprintf('http://a.m.taobao.com/i%d.htm?pid=%d',
			$num_iid, $pid
		);
	}
	
	/**
	 * 解析top_parameters
	 */
	public function parseParameters($topParameters) {
		$result = array();
		$topParameters = str_replace(' ', '+', $topParameters);
		parse_str(PwString::iconv('gbk', 'utf-8', base64_decode($topParameters)), $result);
		return $result;
	}
	
	/**
	 * From object to array.
	 */
	public function toArray($data) {
		if (is_object($data)) $data = get_object_vars($data);
		return is_array($data) ? array_map(array($this, 'toArray'), $data) : $data;
	}
	
	/**
	 * 获取授权地址
	 */
	public function getVerifyUrl($back_url, $extra_param = array()) {
		$verify_url = $this->containerUrl . "?appkey=" . $this->appkey . "&back_url=" . urlencode($back_url);
		if (is_array($extra_param)) {
			$verify_url .= '&' . http_build_query($extra_param);
		}
		return $verify_url;
	}
	
	/**
	 * @desc 验证回调签名函数
	 * 在base64出现空格后,会出bug,曾经出现过一次,现在无法重现....
	 * 待进一步研究后会修复此BUG
	 */
	public function verifyTopResponse($topParameters, $topSession, $topSign, $appKey = null, $appSrecet = null) {
		$appKey || $appKey = $this->appkey;
		$appSrecet || $appSrecet = $this->secretKey;
		return $topSign == base64_encode(md5($appKey . $topParameters . $topSession . $appSrecet, true));
	}
	
	/**
	 * 获取卖家店铺的基本信息
	 * @param string $nick 卖家昵称
	 * @return array
	 */
	public function getMallInfo($nick) {
		$item = $this->taobao_mall_get(array(
			'fields' => 'sid,cid,nick,title,desc,bulletin,pic_path,created,modified,mall_score,remain_count,all_count,used_count',
			'nick' => $nick,
		));
		return isset($item['mall']) ? $item['mall'] : array();
	}
	
	/**
	 * 获取卖家店铺的基本信息
	 * @param string $nick 卖家昵称
	 * @return array
	 */
	public function getShopInfo($nick) {
		$item = $this->taobao_shop_get(array(
				'fields' => 'sid,cid,nick,title,desc,bulletin,pic_path,created,modified,shop_score',
				'nick' => $nick,
		));
		return isset($item['shop']) ? $item['shop'] : array();
	}
	
	/**
	 * 获取前台展示的店铺类目
	 *  @return array
	 */
	public function getShopCatsList() {
		$list = $this->taobao_shopcats_list_get(array(
				'fields' => 'cid,parent_cid,name,is_parent',
		));
		return isset($list['shop_cats']['shop_cat']) ? $list['shop_cats']['shop_cat'] : array();
	}
	
	/**
	 * 淘宝客店铺搜索
	 *  @return array
	 */
	public function getTaobaokeShops(array $params) {
		$params = array_merge(
				$params,
				array(
						'fields' => 'seller_nick,user_id,shop_title,click_url,commission_rate,seller_credit,shop_type,total_auction,auction_count',
				)
		);
		$shops = $this->taobao_taobaoke_shops_get($params);
		return isset($shops['taobaoke_shops']['taobaoke_shop']) ? $shops['taobaoke_shops']['taobaoke_shop'] : array();
	}
	
	/**
	 * 获取单个用户信息
	 * @param string $nick 昵称
	 * @return array
	 */
	public function getUserInfo($nick) {
		if (strpos($nick, '*') !== false) return array();
		
		$item = $this->taobao_user_get(array(
			'fields' => 'user_id,uid,nick,sex,buyer_credit,seller_credit,location,created,last_visit,birthday,type,has_more_pic,item_img_num,item_img_size,prop_img_num,prop_img_size,auto_repost,promoted_type,status,alipay_bind,consumer_protection,alipay_account,alipay_no,real_name,avatar,liangpin,sign_food_seller_promise,has_mall,is_lightning_consignment,vip_info,sign_consumer_protection,phone,mobile,email,magazine_subscribe,vertical_market,manage_book,online_gaming',
			'nick' => $nick,
		));
		return isset($item['user']) ? $item['user'] : array();
	}
	
	/**
	 * 获取卖家信息
	 * @param string $nick 昵称
	 * @return array
	 */
	public function getSellerInfo($nick) {
		if (strpos($nick, '*') !== false) return array();
		$item = $this->taobao_user_seller_get(array(
				'fields' => 'user_id,uid,nick,sex,buyer_credit,seller_credit,location,created,last_visit,birthday,type,has_more_pic,item_img_num,item_img_size,prop_img_num,prop_img_size,auto_repost,promoted_type,status,alipay_bind,consumer_protection,avatar,liangpin,sign_food_seller_promise,has_shop,is_lightning_consignment,has_sub_stock,is_golden_seller,vip_info,email,magazine_subscribe,vertical_market,online_gaming',
				'nick' => $nick,
		));
		print_r($item);die;
		return isset($item['user']) ? $item['user'] : array();
	}
	
	/**
	 * 获取单个用户信息
	 * @param string $nick 昵称
	 * @return array
	 */
	public function getUserInfos(array $nicks) {
		$filtedNicks = array();
		foreach ($nicks as $nick) {
			if (strpos($nick, '*') !== false) continue;
			$filtedNicks[] = $nick;
		}
		if (empty($filtedNicks)) return array();
		
		$items = $this->taobao_users_get(array(
			'fields' => 'user_id,uid,nick,sex,buyer_credit,seller_credit,location,created,last_visit,birthday,type,has_more_pic,item_img_num,item_img_size,prop_img_num,prop_img_size,auto_repost,promoted_type,status,alipay_bind,consumer_protection,alipay_account,alipay_no,real_name,avatar,liangpin,sign_food_seller_promise,has_mall,is_lightning_consignment,vip_info,sign_consumer_protection,phone,mobile,email,magazine_subscribe,vertical_market,manage_book,online_gaming',
			'nicks' => implode(',', $filtedNicks),
		));
		
		if (!isset($items['users'])) return array();
		
		if (isset($items['users']['user']['nick'])) {
			$items['users']['user'] = array($items['users']['user']);
		}
		return $items;
	}
	
	/**
	 * 根据查询条件查询淘宝的商品
	 * 
	 * @param array $params taobao.items.search 参数
	 * @return array
	 */
	public function searchItems(array $params) {
		//if (!array_key_exists('q', $params)) return array();
		$params = array_merge(
			$params,
			array(
				'fields' => 'detail_url,num_iid,title,nick,type,desc,skus,props_name,created,is_lightning_consignment,is_fenxiao,template_id,cid,seller_cids,props,input_pids,input_str,pic_url,num,valid_thru,list_time,delist_time,stuff_status,location,price,post_fee,express_fee,ems_fee,has_discount,freight_payer,has_invoice,has_warranty,has_showcase,modified,increment,approve_status,postage_id,product_id,auction_point,property_alias,item_imgs,prop_imgs,outer_id,is_virtual,is_taobao,is_ex,is_timing,videos,is_3D,score,volume,one_station,second_kill,auto_fill,violation,is_prepay,ww_status,wap_desc,wap_detail_url,after_sale_id,cod_postage_id,sell_promise',
			)
		);
		
		$items = $this->taobao_items_search($params);
		
		if (!isset($items['item_search']['items']['item'])) return array();
		
		if (isset($items['item_search']['items']['item']['num_iid'])) {
			$items['item_search']['items']['item'] = array($items['item_search']['items']['item']);
		}
		return $items;
	}
	
	/**
	 * 得到单个商品信息
	 * @param number $numIid 商品数字ID 
	 * @return array
	 */
	public function getItemInfo($numIid) {
		$fields = array(
			/**
			 * 必选字段
			 */
			'detail_url','num_iid','title' ,'desc' ,'sku','props_name','props','cid','pic_url','num','volume',
			'price','post_fee','express_fee','ems_fee','has_discount','freight_payer','postage_id',
			'nick','score','created','location','item_img','prop_imgs','product_id','second_kill','is_virtual',
			
			/**
			 * 可选字段
			 */
			'type','promoted_service','has_invoice','has_warranty',
			'is_lightning_consignment','is_fenxiao','template_id','seller_cids','input_pids','input_str',
			'valid_thru','list_time','delist_time','stuff_status',
			'has_showcase','modified','increment','auto_repost','approve_status',
			'auction_point','property_alias','outer_id','is_taobao','is_ex','is_timing',
			'videos','is_3D','one_station','auto_fill','violation','appkey','callbackUrl',
			'is_prepay','ww_status','wap_desc','wap_detail_url','after_sale_id','cod_postage_id','sell_promise'
		);
		$item = $this->taobao_item_get(array(
			'fields' => implode(",",$fields),
			'num_iid' => $numIid,
		));
		$item =  isset($item['item']) ? $item['item'] : array();
		if (isset($item['item_imgs']['item_img']['id'])) {
			$item['item_imgs']['item_img'] = array($item['item_imgs']['item_img']);
		}
		return $item;
	}
	
	/**
	 * 获取多个商品信息
	 * @param array $numIids
	 */
	public function getItemInfos($numIids) {
		$fields = array(
				'detail_url','num_iid','title'/* ,'desc' */,'sku','props_name','props','cid','pic_url','num','volume',
				'price','post_fee','express_fee','ems_fee','has_discount','freight_payer','postage_id',
				'nick','score','created','location','item_img','prop_imgs','product_id','second_kill','is_virtual',
		);
		$ret = $this->taobao_items_list_get(array(
				'fields' => implode(",",$fields),
				'num_iids' => implode(",", $numIids),
		));
		return isset($ret['items']['item']) ? $ret['items']['item'] : array();
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getTaokeItemInfo($params) {
		$params = array_merge(
				$params,
				array(
				'fields' => 'num_iid,nick,title,price,item_location,seller_credit_score,click_url,mall_click_url,pic_url,commission_rate,commission,commission_num,commission_volume,volume',
				'nick' => $this->taobaokeNick,
				)
		);
		$ret = $this->taobao_taobaoke_items_detail_get($params);
		if (!$ret['total_results']) return array();
		return $ret['taobaoke_item_details']['taobaoke_item_detail'];
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getTbkItemInfo($params) {
		$params = array_merge(
				$params,
				array(
						'fields' => 'num_iid,nick,title,price,item_location,seller_credit_score,click_url,mall_click_url,pic_url,commission_rate,commission,commission_num,commission_volume,volume',
						'nick' => $this->taobaokeNick,
				)
		);
		$ret = $this->taobao_tbk_items_detail_get($params);
		if (!$ret['total_results']) return array();
		return $ret['tbk_items']['tbk_item'];
	}
	
	/**
	 * 获取单个运费模板
	 * @param string $nick 昵称
	 * @param int $postageId 邮费模板id
	 * @return array
	 */
	public function getPostage($nick, $postageId) {
		$postage = $this->taobao_postage_get(array(
			'fields' => 'postage_id,name,memo,created,modified,post_price,post_increase,express_price,express_increase,ems_price,ems_increase,postage_modes,postage_mode.id,postage_mode.type,postage_mode.dests,postage_mode.price,postage_mode.increase',
			'nick' => $nick,
			'postage_id' => $postageId,
		));
		
		if (!isset($postage['postage'])) return array();
		
		if (isset($postage['postage']['postage_modes']['postage_mode']['id'])) {
			$postage['postage']['postage_modes']['postage_mode'] = array($postage['postage']['postage_modes']['postage_mode']);
		}
		
		return $postage['postage'];
	}
	
	/**
     * 获取指定运费模版  (2012-1-1 上线的新接口)
     * 
     * @param nick $nick
     * @param postage_id $postage_id
     * @author zhangyin
     */
    public function getDeliveryTemplate($nick, $template_id) {
        $taobao_result = $this->taobao_delivery_template_get(array(
            'fields'       => 'template_id,template_name,created,modified,supports,assumer,valuation,query_express,query_ems,query_cod,query_post',
            'user_nick'    => $nick, // 卖家昵称
            'template_ids' => $template_id
        ));

        return $taobao_result;
    }
	
	/**
	 * 获取单个商品的所有 SKU
	 * @param number $numIid 商品的数字IID
	 * @return array
	 */
	public function getItemSkus($numIid) {
		$skus = $this->taobao_item_skus_get(array(
			'fields' => 'sku_id,num_iid,properties,quantity,price,outer_id,created,modified,status,extra_id,memo',
			'num_iids' => $numIid,
		));
		
		if (!isset($skus['skus'])) return array();
		
		if (isset($skus['skus']['sku']['num_iid'])) {
			$skus['skus']['sku'] = array($skus['skus']['sku']);
		}
		
		return $skus;
	}
	
	/**
	 * 获取单笔交易的部分信息
	 * @param $tid 交易编号
	 */
	public function getTradeInfo($tid, $topSession = null) {
		// 获取单个商品的详细信息
		$item = $this->taobao_trade_get(array(
			'fields' => 'seller_nick, buyer_nick, title, type, created, tid, seller_rate, buyer_rate, status, payment, discount_fee, adjust_fee, post_fee, total_fee, pay_time, end_time, modified, consign_time, buyer_obtain_point_fee, point_fee, real_point_fee, received_payment, commission_fee, buyer_memo, seller_memo, alipay_no, buyer_message, pic_path, num_iid, num, price, cod_fee, cod_status, shipping_type, orders.title, orders.pic_path, orders.price, orders.num, orders.num_iid, orders.sku_id, orders.refund_status, orders.status, orders.oid, orders.total_fee, orders.payment, orders.discount_fee, orders.adjust_fee, orders.sku_properties_name, orders.item_meal_name, orders.outer_sku_id, orders.outer_iid, orders.buyer_rate, orders.seller_rate',
			'tid' => $tid,
			'session' => $topSession,
		));
		if (isset($item['code'])) return $item; 
		if (!isset($item['trade'])) return array();
		
		if (isset($item['trade'])) {
			$item = $item['trade'];
		}
		if (isset($item['orders'])) {
			$item['order'] = $item['orders']['order'];
			unset($item['orders']);
		}
		return $item;
	}
	
	/**
	 * 获取淘宝的商品评价列表
	 * @param $pageSize 每页显示的条数，允许值：5、10、20、40
	 */
	public function getTraderates($numIid, $page = 1, $pageSize = 20) {
		// 获取单个商品的详细信息
		$item = $this->taobao_item_get(array(
			'fields' => 'nick',
			'num_iid' => $numIid,
		));
		if (!isset($item['item']['nick'])) return null;
		
		// 通过商品id查询对应的评价信息
		$traderates = $this->taobao_traderates_search(array(
			'num_iid' => $numIid,
			'seller_nick' => $item['item']['nick'],
			'page_no' => $page,
			'page_size' => $pageSize,
		));
		
		if (!isset($traderates['trade_rates']['trade_rate'])) return array();
		
		if (isset($traderates['trade_rates']['trade_rate']['tid'])) {
			$traderates['trade_rates']['trade_rate'] = array($traderates['trade_rates']['trade_rate']);
		}
		return $traderates;
	}
	
	/**
	 * 获取淘宝的商品评价总数
	 */
	public function getTraderateNum($numIid) {
		$requestUrl = 'http://count.taobao.com/counter2';
		$requestUrl .= '?keys=ICE_3_feedcount-' . $numIid . '&t=' . Common::getTime();
		$requestUrl .= '&callback=TMall.mods.SKU.Stat.setReviewCount';
		
		try {
			$resp = $this->curl($requestUrl);
		} catch (Exception $e) {
			$this->logCommunicationError('getTraderateNum', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return 0;
		}
		
		if ($resp) {
			preg_match('/:([0-9]+)\}/', $resp, $match);
			if (count($match) > 1) {
				return intval($match[1]);
			}
		}
		return 0;
	}
	
	/**
	 * 根据淘宝信用等级返回等级图标
	 */
	public static function getLevelImg($level) {
		$leveimg = '';
		if ($level <= 5) {
			$leveimg = 's_red_' . $level . '.gif';
		} else if ($level <= 10) {
			$tmp = $level - 5;
			$leveimg = 's_blue_' . $tmp . '.gif';
		} else if ($level <= 15) {
			$tmp = $level - 10;
			$leveimg = 's_cap_' . $tmp . '.gif';
		} else if ($level <= 20) {
			$tmp = $level - 15;
			$leveimg = 's_crown_' . $tmp . '.gif';
		}
		return 'http://pics.taobaocdn.com/newrank/' . $leveimg;
	}
	
	/**
	 * 获取买家交易记录
	 */
	public function getTradelogs($numIid, $bidPage = 1, $pageSize = 15, $userId = null) {
		$result = array();
		//获取 userId
		if (is_null($userId)) {
			$item = $this->taobao_item_get(array(
				'fields' => 'nick',
				'num_iid' => $numIid,
			));
			if (!isset($item['item']['nick'])) return $result;
			
			$item = $this->taobao_user_get(array(
				'fields' => 'user_id',
				'nick' => $item['item']['nick'],
			));
			if (!isset($item['user']['user_id'])) return $result;
			$userId = $item['user']['user_id'];
		}
		
		$requestUrl = 'http://tbskip.taobao.com/json/show_buyer_list.htm';
		$requestUrl .= sprintf('?ends=%s000&item_id=%s&seller_num_id=%s&bid_page=%d&page_size=%d&is_start=true',
			Common::getTime(),
			$numIid,
			$userId,
			$bidPage,
			$pageSize
		);
		
		try {
			$resp = $this->curl($requestUrl);
		} catch (Exception $e) {
			$this->logCommunicationError('getTradelogs', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return $result;
		}
		
		if (!$resp) return $result;
		
		$resp = PwString::iconv('gbk', 'utf-8', $resp);
		
		//获取买家
		$buys_temp = strip_tags($resp, '<tr><br/><td><a><img>');
		$buys_temp = preg_replace('/<br\/>[^<]+/i', '', $buys_temp);
		
		$a = explode('<tr', $buys_temp);
		foreach ($a as $k => $v) {
			preg_match('/http:\/\/trade\..*taobao\.[^"]+/i', $v, $match1);
			if (empty($match1)) continue;
			
			$tradeLog = array();
			$tradeLog['url'] = trim($match1[0], '\\');
			
			preg_match_all('/<td[^>]*>(.*)<\/td>/iUs', $v, $match);
			if (isset($match[1]) && count($match[1]) != 6) continue;
			
			//匹配数据开始
			$tmp = explode('<img', $match[1][0]);
			
			if (preg_match('/taobao.com/i', $tmp[0])) {
				preg_match('/href="([^"]+)"[^>]*>(.*)<\/a.*href="([^"]+)"/iUs', $tmp[0], $m);
				$tradeLog['buyer_url'] = trim($m[1]);
				$tradeLog['buyer'] = trim($m[2]);
				$tradeLog['rate_url'] = trim($m[3]);
			} else {
				$tradeLog['buyer'] = trim($tmp[0]);
			}
			
			$imgs = array();
			for ($i = 1, $j = count($tmp); $i < $j; $i++) {
				$img = array();
				preg_match('/src="([^"]+)"/i', $tmp[$i], $m);
				isset($m[1]) && $img['src'] = $m[1];
				
				preg_match('/title="([^"]+)"/i', $tmp[$i], $m);
				isset($m[1]) && $img['title'] = $m[1];
				
				preg_match('/alt="([^"]+)"/i', $tmp[$i], $m);
				isset($m[1]) && $img['title'] = $m[1];
				//rate如果有链接
				if ($i == 1 && isset($tradeLog['rate_url'])) {
					$img['url'] = $tradeLog['rate_url'];
					unset($tradeLog['rate_url']);
				}
				
				$imgs[] = $img;
			}
			
			$tradeLog['imgs'] = $imgs;
			
			preg_match('/<a[^>]*>(.*)<\/a>(.*)$/iUs', $match[1][1], $tmp);
			isset($tmp[1]) && $tradeLog['title'] = trim($tmp[1]);
			isset($tmp[2]) && $tradeLog['sku'] = trim($tmp[2]);
			
			$tradeLog['price'] = trim($match[1][2]);
			$tradeLog['num'] = trim($match[1][3]);
			$tradeLog['time'] = trim($match[1][4]);
			$tradeLog['buy_status'] = trim($match[1][5]);
			
			$result['list'][] = $tradeLog;
		}
		
		//获取分页信息
		$buys_page = strip_tags($resp, '<a><span>');
		$b = explode('page-info', $buys_page);
		if (count($b) > 1) {
			preg_match_all('/>([0-9\…]+)</', $b[1], $mat);
			if (count($mat) > 1) {
				$result['paginator'] = $mat[1];
			}
		}
		
		return $result;
	}
	
	/**
	 * 抓取某个商品页面内容
	 * @param number $numIid
	 */
	public function getItemPage($numIid) {
		$requestUrl = 'http://item.taobao.com/item.htm';
		$requestUrl .= '?id=' . $numIid . '&t=' . Common::getTime();
		
		$resp = '';
		
		try {
			$resp = $this->curl($requestUrl);
		} catch (Exception $e) {
			$this->logCommunicationError('getItemPage', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
		}
		//return Desire_String::iconv('gbk', 'utf-8', $resp);
		return iconv('gbk', 'utf-8', $resp);
	}
	
	/**
	 * 获取买家交易记录总数
	 */
	public function getTradelogNum($numIid) {
		$resp = $this->getItemPage($numIid);
		
		if ($resp) {
			preg_match('/id="deal-record".*<em>(\d+)<\/em>/iUs', $resp, $match);
			if (count($match) > 1) {
				return intval($match[1]);
			}
		}
		return 0;
	}
	
	/**
	 * 同时获取多个用户的VIP等级
	 * @see http://baike.corp.taobao.com/index.php/%E8%8E%B7%E5%8F%96VIP%E7%94%A8%E6%88%B7%E7%AD%89%E7%BA%A7%E6%8E%A5%E5%8F%A3%EF%BC%88WEB%EF%BC%89
	 * 
	 * @param array $userIds
	 * @return array
	 */
	public function getUserVipLevels(array $userIds) {
		$requestUrl = 'http://vip.taobao.com/ajax/query_vip.do';
		$requestUrl .= '?uid=' . implode(':', $userIds) . '&src=qutao.com&cb=getUserVipLevel&t=' . Common::getTime();
		
		try {
			$resp = $this->curl($requestUrl);
		} catch (Exception $e) {
			$this->logCommunicationError('getUserVipLevels', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return array();
		}
		
		if ($resp) {
			//getUserVipLevel({"queryResult":[{"userId":"117688512","vipLevel":"3"},{"userId":"392048031","vipLevel":"3"}]});
			preg_match('/getUserVipLevel\((.*)\);$/', $resp, $match);
			if (count($match) > 1) {
				$tmp = json_decode($match[1], true);
				if (isset($tmp['queryResult']) && is_array($tmp['queryResult'])) return $tmp['queryResult'];
			}
		}
		return array();
	}
	
	/**
	 * 获取单个用户的VIP等级
	 * 
	 * @param number $userId
	 */
	public function getUserVipLevel($userId) {
		return current($this->getUserVipLevels(array($userId)));
	}
	
	/**
	 * 根据淘宝用户vip等级返回等级图标
	 */
	public static function getVipLevelImg($level, $small = false) {
		$leveimg = '';
		switch ($level) {
			case -2:
				break; //VIP普通会员(无图标)
			case -1:
				break; //VIP普通会员(无图标)
			case 0: //VIP荣誉会员(无图标) 
			case 1:
				$leveimg = 'v1';
				break; //VIP黄金会员
			case 2:
				$leveimg = 'v2';
				break; //VIP白金会员
			case 3:
				$leveimg = 'v2';
				break; //VIP钻石会员
			case 4:
				break; //vip4会员，暂时没有这类用户，名称也未定 
			case 10:
				$leveimg = 'v10'; //淘宝达人
			default: //体验VIP会员在该接口中无法体现
		}
		
		if (empty($leveimg)) return false;
		
		$small && $leveimg .= 's';
		return 'http://a.tbcdn.cn/app/marketing/vip/vip2/icons/' . $leveimg . '.gif';
	}
	
	/**
	 * 获取限时折扣信息
	 * @param number $numIid
	 * @param string $nick
	 */
	public function getLimitPromotionRate($numIid, $nick = null) {
		//默认无折扣
		$limitPromotionRate = array('type' => 'none');
		
		if (is_null($nick)) {
			$item = $this->taobao_item_get(array(
				'fields' => 'nick',
				'num_iid' => $numIid,
			));
			if (!isset($item['item']['nick'])) return $limitPromotionRate;
			$nick = $item['item']['nick'];
		}
		
		$item = $this->taobao_user_get(array(
			'fields' => 'user_id',
			'nick' => $nick,
		));
		if (!isset($item['user']['user_id'])) return $limitPromotionRate;
		
		$limitPromotionRate['nick'] = $nick;
		$limitPromotionRate['user_id'] = $item['user']['user_id'];
		
		$requestUrl = 'http://tbskip.taobao.com/limit_promotion_item.htm';
		$requestUrl .= '?auctionId=' . $numIid . '&userId=' . $item['user']['user_id'] . '&t=' . Common::getTime();
		
		try {
			$resp = $this->curl($requestUrl);
		} catch (Exception $e) {
			$this->logCommunicationError('getLimitPromotionRate', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return $limitPromotionRate;
		}
		
		if (empty($resp)) return $limitPromotionRate;
		
		//TB.LimitPromotion_8762509426_512671209 = '10小时3分钟;5.55;68.82';
		preg_match("/TB\.LimitPromotion.*'([^']+)'/is", $resp, $match);
		if (!isset($match[1])) return $limitPromotionRate;
		if (strpos($match[1], ';') === false) return $limitPromotionRate;
		list($time, $rate, $price) = explode(';', $match[1]);
		$time = str_replace(array(
				'小时', '分钟', '星期', '天', '年', '月'
			), array(
				' hours ', ' seconds ', ' weeks ', ' days ', ' years ', ' months '
			),
			PwString::iconv('gbk', 'utf-8', $time)
		);
		
		$limitPromotionRate['time'] = strtotime($time);
		$limitPromotionRate['rate'] = $rate;
		//$limitPromotionRate['price'] = $price;
		

		//获取限时折扣类型
		$pageResp = $this->getItemPage($numIid);
		if (empty($pageResp)) return $limitPromotionRate;
		
		if (preg_match('/id="J_EmLimitPromCountdown"/i', $pageResp)) {
			$limitPromotionRate['type'] = 'progress';
		} else if (preg_match('/valLimitPromInfo2:.*\{.*\}/is', $pageResp)) {
			$limitPromotionRate['type'] = 'timeLeft';
		}
		
		if ($limitPromotionRate['type'] == 'none') return $limitPromotionRate; //没找到限时折扣类型
		

		if (preg_match('/id="J_SpanPromLimitCount"[^>]*>(\d+)</i', $pageResp, $pageMatch)) {
			$limitPromotionRate['limitCount'] = $pageMatch[1];
		}
		
		return $limitPromotionRate;
	}
	
	/**
	 * 获取淘宝VIP会员的打折幅度
	 * @param number $numIid
	 * @return array array("VIP 金　卡"， "VIP 白金卡"， "VIP 钻石卡")
	 */
	public function getVipDiscountRegion($numIid) {
		$vipDiscountRegion = array();
		
		$pageResp = $this->getItemPage($numIid);
		if (empty($pageResp)) return $vipDiscountRegion;
		
		if (!preg_match('/id="J_VipPriceData"/i', $pageResp)) {
			return $vipDiscountRegion;
		}
		
		if (preg_match('/vipDiscountRegion: (\[[^\]]+\])/i', $pageResp, $pageMatch)) {
			if (!isset($pageMatch[1])) return $vipDiscountRegion;
			$vipDiscountRegion = json_decode($pageMatch[1], true);
		}
		
		return $vipDiscountRegion;
	}
	
	/**
	 * 物流流转信息查询
	 * @param number $tid 淘宝交易号
	 * @param string $sellerNick 卖家昵称 
	 * @return array
	 */
	public function getLogisticsTrace($tid, $sellerNick) {
		$logisticsTrace = $this->taobao_logistics_trace_search(array(
			'tid' => $tid,
			'seller_nick' => $sellerNick,
		));
		
		if (!isset($logisticsTrace['tid'])) return array();
		
		if (isset($logisticsTrace['trace_list']['transit_step_info']['status_desc'])) {
			$logisticsTrace['trace_list']['transit_step_info'] = array($logisticsTrace['trace_list']['transit_step_info']);
		}
		
		return $logisticsTrace;
	}
	
	/**
	 * 获取某件商品的淘宝客的链接地址
	 */
	public function getTaobaokeClickUrl($numIid) {
		$taobaoke = $this->getTaobaoke($numIid);
		if (isset($taobaoke['taobaoke_items']['taobaoke_item'][0]['click_url'])) {
			return $taobaoke['taobaoke_items']['taobaoke_item'][0]['click_url'];
		}
		
		return $this->filterUrl('http://item.taobao.com/item.htm?id=' . $numIid);
	}
	
	/**
	 * 获取多件商品的淘宝客信息
	 * @param array $numIids 淘宝客商品数字id串
	 */
	public function getTaobaokes($params) {
		$params = array_merge(
				$params,
				array(
				'fields' => 'commission_rate,iid,num_iid,title,nick,pic_url,price,click_url,commission,commission_num,commission_volume,mall_click_url,seller_credit_score,item_location,volume,taobaoke_cat_click_url,keyword_click_url,promotion_price',
				'nick' => $this->taobaokeNick,
				)
		);
		$items = $this->taobao_taobaoke_items_convert($params);
		
		if (!isset($items['taobaoke_items'])) return array();
		if (isset($items['taobaoke_items']['taobaoke_item']['num_iid'])) {
			$items['taobaoke_items']['taobaoke_item'] = array($items['taobaoke_items']['taobaoke_item']);
		}
		return $items;
	}
	
	/**
	 * 
	 * @param array $params
	 */
	public function getTaokeReport($params) {
		$params = array_merge($params, array(
				'fields' => 'app_key,outer_code,trade_id,pay_time,pay_price,num_iid,item_title,item_num,category_id,category_name,mall_title,commission_rate,commission,iid,seller_nick',
			));
		return $this->taobao_taobaoke_report_get($params);		
	}
	
	/**
	 * 获取某件商品的淘宝客信息
	 * @param string $numIid 淘宝客商品数字id串
	 */
	public function getTaobaoke($params) {
		$items = $this->getTaobaokes($params);
		
		if (!isset($items['taobaoke_items']['taobaoke_item'])) {
			return array();
		}
		return $items['taobaoke_items']['taobaoke_item'][0];
	}
	
	/**
	 * 淘宝店铺转换
	 */
	public function TaobaokeShopsConvert($params) {
		$params = array_merge(
				$params,
				array(
						'fields' => 'seller_nick,user_id,shop_title,click_url,commission_rate,seller_credit,shop_type,total_auction,auction_count',
				)
		);
		$shops = $this->taobao_taobaoke_shops_convert($params);
		$shop_list = $shops['taobaoke_shops']['taobaoke_shop'];
		if (!isset($shop_list)) return array();
		if (isset($shop_list['auction_count'])) {
			$shop_list['credit_img'] = $this->getLevelImg($shop_list['seller_credit']);
		} else {
			for ($i = 0, $j = count($shop_list); $i < $j; $i++) {
				$shop_list[$i]['credit_img'] = $this->getLevelImg($shop_list[$i]['seller_credit']);
			}
		}
		return $shop_list;
	}
	
	/**
	 * 商品关联推荐
	 */
	public function taobaokeItemsRelate($params) {
		$params = array_merge(
				$params,
				array(
						'fields' => 'num_iid,seller_id,nick,title,price,item_location,seller_credit_score,click_url,shop_click_url,pic_url,taobaoke_cat_click_url,keyword_click_url,coupon_rate,coupon_price	,coupon_start_time,coupon_end_time,commission_rate,commission,commission_num,commission_volume,volume,shop_type,promotion_price',
				)
		);
		$items = $this->taobao_taobaoke_items_relate_get($params);
		return isset($items['taobaoke_items']['taobaoke_item']) ? $items['taobaoke_items']['taobaoke_item'] : array();
	}
	
	/**
	 * 无线淘客商品转换
	 */
	public function taobaokeMobileItemsConvert($params) {
		$params = array_merge(
				$params,
				array(
						'fields' => 'num_iid,nick,title,price,item_location,seller_credit_score,click_url,shop_click_url,pic_url,commission_rate,commission,commission_num,commission_volume,volume,promotion_price',
				)
		);
		$items = $this->taobao_tbk_mobile_items_convert($params);
		if (!isset($items['tbk_items']['tbk_item'])) return array();
		if (isset($items['tbk_items']['tbk_item']['num_iid'])) {
			$items['tbk_items']['tbk_item'] = array($items['tbk_items']['tbk_item']);
		}
		return $items['tbk_items']['tbk_item'];
	}
	
	
	/**
	 * 根据查询条件查询淘宝客的商品
	 *
	 * @param array $params taobao.taobaoke.items.get 参数
	 * @return array
	 */
	protected function findTaobaokes(array $params) {
		$items = $this->getTaobaokeItems($this->taobaokeNick, $params);
		if (empty($items)) return array();
	
		if ($params['saller_info'] || $params['mall_info']) {
			$sellerNicks = array();
			for ($i = 0, $j = count($items['taobaoke_items']['taobaoke_item']); $i < $j; $i++) {
				if ($params['mall_info']) {
					$items['taobaoke_items']['taobaoke_item'][$i]['mall_detail'] = $this->getMallInfo($items['taobaoke_items']['taobaoke_item'][$i]['nick']);
				}
				$sellerNicks[] = $items['taobaoke_items']['taobaoke_item'][$i]['nick'];
			}
		}
		if ($params['saller_info']) {
			$sellerUserinfos = array();
			$users = $this->getUserInfos($sellerNicks);
			isset($users['users']['user']) || $users['users']['user'] = array();
			foreach ($users['users']['user'] as $user) {
				isset($user['nick']) && $sellerUserinfos[$user['nick']] = $user;
			}
		
			for ($i = 0, $j = count($items['taobaoke_items']['taobaoke_item']); $i < $j; $i++) {
				$items['taobaoke_items']['taobaoke_item'][$i]['seller_detail'] = isset($sellerUserinfos[$items['taobaoke_items']['taobaoke_item'][$i]['nick']]) ? $sellerUserinfos[$items['taobaoke_items']['taobaoke_item'][$i]['nick']] : $this->getUserInfo($items['taobaoke_items']['taobaoke_item'][$i]['nick']);
			}
		}
		return $items;
	}
	
	/**
	 * 根据查询条件查询淘宝的商品
	 *
	 * @param array $params taobao.items.search 参数
	 * @return array
	 */
	protected function findItems(array $params) {
		$items = $this->searchItems($params);
		if (empty($items)) return array();
	
		$sellerNicks = $taobaokeIds = array();
		for ($i = 0, $j = count($items['item_search']['items']['item']); $i < $j; $i++) {
			$items['item_search']['items']['item'][$i]['mall_detail'] = $this->getMallInfo($items['item_search']['items']['item'][$i]['nick']);
			$sellerNicks[] = $items['item_search']['items']['item'][$i]['nick'];
			$taobaokeIds[] = $items['item_search']['items']['item'][$i]['num_iid'];
		}
		//获得卖家信息
		$sellerUserinfos = array();
		$users = $this->getUserInfos($sellerNicks);
		isset($users['users']['user']) || $users['users']['user'] = array();
		foreach ($users['users']['user'] as $user) {
			isset($user['nick']) && $sellerUserinfos[$user['nick']] = $user;
		}
		//获得淘宝客信息
		$taobaokes = array();
		$taobaokeItems = $this->getTaobaokes($taobaokeIds);
		isset($taobaokeItems['taobaoke_items']['taobaoke_item']) || $taobaokeItems['taobaoke_items']['taobaoke_item'] = array();
		foreach ($taobaokeItems['taobaoke_items']['taobaoke_item'] as $taobaokeItem) {
			$taobaokes[$taobaokeItem['num_iid']] = $taobaokeItem;
		}
		//过滤数据
		for ($i = 0, $j = count($items['item_search']['items']['item']); $i < $j; $i++) {
			$items['item_search']['items']['item'][$i]['seller_detail'] = isset($sellerUserinfos[$items['item_search']['items']['item'][$i]['nick']]) ? $sellerUserinfos[$items['item_search']['items']['item'][$i]['nick']] : $this->getUserInfo($items['item_search']['items']['item'][$i]['nick']);
			isset($taobaokes[$items['item_search']['items']['item'][$i]['num_iid']]) && $items['item_search']['items']['item'][$i]['taobaoke_detail'] = $taobaokes[$items['item_search']['items']['item'][$i]['num_iid']];
		}
	
		return $items;
	}
	
	/**
	 * 获取淘宝的商品评价列表
	 * @param $pageSize 每页显示的条数，允许值：5、10、20、40
	 */
	protected function getTraderatesDetail($numIid, $page = 1, $pageSize = 20) {
		$traderates = parent::getTraderates($numIid, $page, $pageSize);
	
		if (empty($traderates)) return array();
	
		$sellerNicks = array();
		for ($i = 0, $j = count($traderates['trade_rates']['trade_rate']); $i < $j; $i++) {
			$sellerNicks[] = $traderates['trade_rates']['trade_rate'][$i]['nick'];
		}
		//获得买家信息
		$userinfos = $vips = $userVips = array();
		$users = $this->getUserInfos($sellerNicks);
		isset($users['users']['user']) || $users['users']['user'] = array();
		foreach ($users['users']['user'] as $user) {
			if (isset($user['nick'])) {
				$userinfos[$user['nick']] = $user;
				$vips[] = $user['user_id']; //去获取vip等级
				$userVips[$user['nick']] = $user['user_id']; //返回nick与user_id的map
			}
		}
	
		$userVipLevels = array();
		foreach ($this->getUserVipLevels($vips) as $vip) {
			$userVipLevels[$vip['userId']] = $vip['vipLevel'];
		}
	
		//过滤数据
		for ($i = 0, $j = count($traderates['trade_rates']['trade_rate']); $i < $j; $i++) {
			$traderates['trade_rates']['trade_rate'][$i]['nick_detail'] = isset($userinfos[$traderates['trade_rates']['trade_rate'][$i]['nick']]) ? $userinfos[$traderates['trade_rates']['trade_rate'][$i]['nick']] : $this->getUserInfo($traderates['trade_rates']['trade_rate'][$i]['nick']);
			if (isset($userVipLevels[$userVips[$traderates['trade_rates']['trade_rate'][$i]['nick']]])) $traderates['trade_rates']['trade_rate'][$i]['nick_detail']['buyer_credit']['vip_level'] = $userVipLevels[$userVips[$traderates['trade_rates']['trade_rate'][$i]['nick']]];
		}
		return $traderates;
	}
	
	/**
	 * 得到单个商品信息
	 * @param number $numIid 商品数字ID
	 * @return array
	 */
	protected function getItemInfoDetail($numIid) {
		$item = $this->getItemInfo($numIid);
		if (empty($item)) return array();
	
		$item['volume'] = $this->getTradelogNum($numIid);
		$item['mall_detail'] = $this->getMallInfo($item['nick']);
		$item['seller_detail'] = $this->getUserInfo($item['nick']);
		$item['skus_detail'] = $this->getItemSkus($numIid);
		$item['props_name_detail'] = $this->getSkuPropsNameMap($item['props_name']);
		$taobaoke = $this->getTaobaoke($item['num_iid']);
		isset($taobaoke['taobaoke_items']['taobaoke_item'][0]) && $item['taobaoke_detail'] = $taobaoke['taobaoke_items']['taobaoke_item'][0];
		if (isset($item['postage_id']) && $item['postage_id']) {
			$postage = $this->getPostage($item['nick'], $item['postage_id']);
			if ($postage) {
				$postageModes = array();
				//$provinces = proAreaCode::getProvinces();
				//$provinces = array_flip($provinces);
				$postage['postage_modes']['postage_mode'] || $postage['postage_modes']['postage_mode'] = array();
				foreach ($postage['postage_modes']['postage_mode'] as $postageMode) {
					foreach (explode(',', $postageMode['dests']) as $dest) {
						//$dest = isset($provinces[$dest]) ? $provinces[$dest] : $dest;
						$postageModes[$dest][$postageMode['type']] = array(
								'id' => $postageMode['id'],
								'increase' => $postageMode['increase'],
								'price' => $postageMode['price'],
						);
					}
				}
				$postage['postage_modes'] = $postageModes;
				$item['postage_detail'] = $postage;
			}
		}
		//限时折扣
		$item['promotion_detail'] = array();
		$item['promotion_detail']['limitPromotionRate'] = $this->getLimitPromotionRate($item['num_iid'], $item['nick']);
		$item['promotion_detail']['vipDiscountRegion'] = $this->getVipDiscountRegion($item['num_iid']);
	
		return $item;
	}
	
	/**
	 * 
	 * @param unknown_type $cid
	 * @return unknown
	 */
	public function getTaoBaoItemCats($cid = 0) {
		$params = array (
				'parent_cid' => intval($cid),
				'fields' => 'cid,parent_cid,name,is_parent,status,sort_order,last_modified',
		);
		$ret = $this->taobao_itemcats_get ( $params );
		if ($ret['code']) return array();
		return $ret;
	}
	
	/**
	 * 根据查询条件查询淘宝客的商品
	 * 
	 * @param string $nick
	 * @param array $params taobao.taobaoke.items.get 参数
	 * @return array
	 */
	public function getTaobaokeItems($nick, array $params) {
		if (!array_key_exists('keyword', $params) && !array_key_exists('cid', $params)) return array();
		$params = array_merge(
			$params,
			array(
				'fields' => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,mall_click_url,seller_credit_score,item_location,volume,promotion_price',
				'nick' => $nick
			)
		);
		$items = $this->taobao_taobaoke_items_get($params);
		if (!isset($items['taobaoke_items']['taobaoke_item'])) return array();
		
		if (isset($items['taobaoke_items']['taobaoke_item']['num_iid'])) {
			$items['taobaoke_items']['taobaoke_item'] = array($items['taobaoke_items']['taobaoke_item']);
		}
		return $items;
	}
	
	/**
	 * 无线淘宝客推广商品查询
	 *
	 * @param string $nick
	 * @param array $params taobao.taobaoke.mobile.items.get 参数
	 * @return array
	 */
	public function getTaobaokeMobileItems(array $params) {
		if (!array_key_exists('keyword', $params) && !array_key_exists('cid', $params)) return array();
		$params = array_merge(
				$params,
				array(
						'fields' => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume,promotion_price',
						'nick' => $this->taobaokeNick
				)
		);
		$items = $this->taobao_taobaoke_mobile_items_get($params);
		print_r($items);die;
		if (!isset($items['taobaoke_items']['taobaoke_item'])) return array();
	
		if (isset($items['taobaoke_items']['taobaoke_item']['num_iid'])) {
			$items['taobaoke_items']['taobaoke_item'] = array($items['taobaoke_items']['taobaoke_item']);
		}
		return $items;
	}
	
	
	/**
	 * 淘宝客推广商品查询
	 *
	 * @param string $nick
	 * @param array $params taobao.taobaoke.items.get 参数
	 * @return array
	 */
	public function taobaoTbkItemsGet(array $params) {
		if (!array_key_exists('keyword', $params) && !array_key_exists('cid', $params)) return array();
		$params = array_merge(
				$params,
				array(
						'fields' => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,mall_click_url,seller_credit_score,item_location,volume,promotion_price',
				)
		);
		$items = $this->taobao_tbk_items_get($params);
		if (!isset($items['tbk_items']['tbk_item'])) return array();
	
		if (isset($items['tbk_items']['tbk_item']['num_iid'])) {
			$items['tbk_items']['tbk_item'] = array($items['tbk_items']['tbk_item']);
		}
		return $items;
	}
	
	/**
	 * Sku名字对应关系
	 * @param string $propsName
	 */
	public function getSkuPropsNameMap($propsName) {
		if (empty($propsName)) return array();
		$propsNames = explode(';', $propsName);
		$propsNameArr = array();
		foreach ($propsNames as $k => $v) {
			$tmp = explode(':', $v);
			$propsNameArr[$tmp[0]] = $tmp[2];
			$propsNameArr[$tmp[1]] = $tmp[3];
		}
		return $propsNameArr;
	}
	
	/**
	 * VIP加密接口
	 * @param string $content 需要加密的明文 
	 * @return boolean|array = array('success', 'message');
	 * message: INPUT_ERROR: 参数错误, SYSTEM_ERROR: 系统错误,ENCRYPT_ERROR:加密错误 
	 */
	public function vipCooperEncrypt($content) {
		$requestUrl = $this->filterUrl('http://vip.taobao.com/cooper/coupon/encrypt.do');
		
		$postFields = array();
		$postFields['content'] = $content;
		$postFields['result'] = ''; //返回的json对象result={json对象}，
		

		try {
			$resp = $this->curl($requestUrl, $postFields);
		} catch (Exception $e) {
			$this->logCommunicationError('vipCooperEncrypt', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}
		
		return json_decode($resp, true);
	}
	
	/**
	 * $code: 任务编码 
	 * $userId: 用户数字ID（可选） 
	 * $nick: 用户nick（可选） 
	 * $accountNo: 支付宝No（可选,必须带0156结尾的） 
	 * @return boolean|array = array('success', 'message');
	 * 
	 * 注: userId,nick,accountNo必须包含一个 
	 * message: OVER_TIME: time超时（5分钟）, SIGN_ERROR: sign加密串错误或者参数不全 , SYSTEM_ERROR: 系统异常, COMPLETE: 任务完成成功, USER_NOT_FOUND: 找不到用户信息  
	 */
	public function vipCooperCompleteTask($code, $userId = null, $nick = null, $accountNo = null) {
		if (is_null($userId) && is_null($nick) && is_null($accountNo)) return false;
		
		$task = array();
		is_null($userId) || $task[] = sprintf('userId=%s', $userId);
		is_null($nick) || $task[] = sprintf('nick=%s', $nick);
		is_null($accountNo) || $task[] = sprintf('accountNo=%s', $accountNo);
		$task[] = sprintf('code=%s', $code);
		$task[] = sprintf('time=%s', Common::getTime());
		
		//do complete task
		$requestUrl = $this->filterUrl('http://vip.taobao.com/cooper/complete_task.do');
		
		$encrypt = $this->vipCooperEncrypt(implode('|', $task));
		
		if (empty($encrypt) || !isset($encrypt['message'])) return false;
		$postFields = array();
		$postFields['sign'] = $encrypt['message'];
		
		try {
			$resp = $this->curl($requestUrl, $postFields);
		} catch (Exception $e) {
			$this->logCommunicationError('vipCooperCompleteTask', $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}
		
		return json_decode($resp, true);
	}
	
	/**
	 * 缺省方法
	 * 替换top接口方法中的.为_
	 * TopClient::getInstance()->taobao_user_get(array(应用级输入参数));
	 */
	public function __call($functionName, $arguments) {
		if (strpos($functionName, '_') === false) return false;
		$arguments[0] = new Api_Top_Method(str_replace('_', '.', $functionName), $arguments[0]);
		$data = call_user_func_array(array($this, 'execute'), $arguments);
		$result = is_object($data) ? $this->toArray($data) : $data;
		if ($result['code']) {
			Common::log($result, 'api_error.log');
		}
		return $result;
	}
}