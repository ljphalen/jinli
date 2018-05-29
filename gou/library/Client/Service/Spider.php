<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * miigou spider
 * @author tiansh
 *
 */
class Client_Service_Spider{

	public static $channels = array(
		  	'jd'         => array('channel_id'=>3, 'domain' => 'jd.com', 'name' => '京东商城', 'logo'=>'jd.png'),
		  	'tmall'      => array('channel_id'=>2, 'domain' => 'tmall.com', 'name' => '天猫', 'logo'=>'tmall.png'),
		  	'tmall_hk'   => array('channel_id'=>2, 'domain' => 'tmall.hk', 'name' => '天猫', 'logo'=>'tmall.png'),
		  	'taobao'     => array('channel_id'=>1, 'domain' => 'taobao.com', 'name' => '淘宝', 'logo'=>'taobao.png'),
		  	'mmb'        => array('channel_id'=>4, 'domain' => 'mmb.cn', 'name' => '买卖宝', 'logo'=>'mmb.png'),
		  	'vip'        => array('channel_id'=>15, 'domain' => 'vip.com', 'name' => '唯品会', 'logo'=>'vip.png'),
		  	'meilishuo'  => array('channel_id'=>16, 'domain' => 'meilishuo.com', 'name' => '美丽说', 'logo'=>'meilishuo.png'),
		  	'mogujie'    => array('channel_id'=>17, 'domain' => 'mogujie.com', 'name' => '蘑菇街', 'logo'=>'mogujie.png'),
		  	'yhd'        => array('channel_id'=>18, 'domain' => 'yhd.com', 'name' => '1号店', 'logo'=>'yhd.png'),
			'dangdang'   => array('channel_id'=>14, 'domain' => 'dangdang.com', 'name' => '当当网', 'logo'=>'dangdang.png'),
		  	'zch168'     => array('channel_id'=>19, 'domain' => 'zch168.com', 'name' => '中彩汇', 'logo'=>'zch168.png'),
		  	'meituan'    => array('channel_id'=>20, 'domain' => 'meituan.com', 'name' => '美团网', 'logo'=>'meituan.png'),
		  	'aicai'      => array('channel_id'=>21, 'domain' => 'aicai.com', 'name' => '爱彩网', 'logo'=>'aicai.png'),
		  	'taohwu'     => array('channel_id'=>22, 'domain' => 'taohwu.com', 'name' => '桃花坞商城', 'logo'=>'taohwu.png'),
		  	'ytao'       => array('channel_id'=>23, 'domain' => 'ytao.cn', 'name' => '移淘商城', 'logo'=>'ytao.png'),
		 	'aizhigu'    => array('channel_id'=>24, 'domain' => 'aizhigu.com.cn', 'name' => '爱之谷商城', 'logo'=>'aizhigu.png'),
		  	'tieyou'     => array('channel_id'=>25, 'domain' => 'tieyou.com', 'name' => '铁友网', 'logo'=>'tieyou.png'),
		  	'moonbasa'   => array('channel_id'=>26, 'domain' => 'moonbasa.com', 'name' => '梦芭莎', 'logo'=>'moonbasa.png'),
		  	'zg51'       => array('channel_id'=>27, 'domain' => 'zg51.net', 'name' => '掌购无忧', 'logo'=>'zg51.png'),
		  	'lashou'     => array('channel_id'=>28, 'domain' => 'lashou.com', 'name' => '拉手网', 'logo'=>'lashou.png'),
		  	'nuomi'      => array('channel_id'=>29, 'domain' => 'nuomi.com', 'name' => '百度糯米网', 'logo'=>'nuomi.png'),
		 	'ly'         => array('channel_id'=>30, 'domain' => 'ly.com', 'name' => '同程网', 'logo'=>'ly.png'),
		  	'lefeng'     => array('channel_id'=>31, 'domain' => 'lefeng.com', 'name' => '乐蜂网', 'logo'=>'lefeng.png'),
		  	'mbaobao'    => array('channel_id'=>32, 'domain' => 'mbaobao.com', 'name' => '麦包包', 'logo'=>'mbaobao.png'),
		  	'paixie'     => array('channel_id'=>33, 'domain' => 'paixie.net', 'name' => '拍鞋网', 'logo'=>'paixie.png'),
		  	'amazon'     => array('channel_id'=>34, 'domain' => 'amazon.cn', 'name' => '亚马逊', 'logo'=>'amazon.png'),
		  	'suning'     => array('channel_id'=>35, 'domain' => 'suning.com', 'name' => '苏宁易购', 'logo'=>'suning.png'),
		  	'gome'       => array('channel_id'=>36, 'domain' => 'gome.com.cn', 'name' => '国美在线', 'logo'=>'gome.png'),
		  	'qunar'      => array('channel_id'=>37, 'domain' => 'qunar.com', 'name' => '去哪儿网', 'logo'=>'qunar.png'),
		  	'dianping'   => array('channel_id'=>38, 'domain' => 'dianping.com', 'name' => '大众点评', 'logo'=>'dianping.png'),
		  	'yixiangsc'  => array('channel_id'=>39, 'domain' => 'yixiangsc.com', 'name' => '易享商城', 'logo'=>'yixiangsc.png'),
		  	'tiantian'   => array('channel_id'=>40, 'domain' => 'tiantian.com', 'name' => '天天网', 'logo'=>'tiantian.png'),
		  	'm18'        => array('channel_id'=>41, 'domain' => 'm18.com', 'name' => 'M18', 'logo'=>'m18.png'),
		  	'gionee'     => array('channel_id'=>42, 'domain' => 'gionee.com', 'name' => '金立', 'logo'=>'gionee-default.png'),
	);

	/**
	 * 根据url来获取对应的logo
	 * @param string $channel
	 * @return string logo url
	 */
	public static function getChannelLogo($channel){
		$channels = Common::resetKey(Client_Service_Spider::$channels, 'domain');
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$channel = parse_url($channel, PHP_URL_HOST);
		foreach($channels as $key=>$item){
			if(stripos(strrev($channel), strrev($key)) === 0){
				return sprintf('%s/apps/gou/pic/collect/%s', $staticroot, $item['logo']);
			}
		}
		return sprintf('%s/apps/gou/pic/collect/%s', $staticroot, 'gionee-default.png');
	}

	/**
	 * 根据url来获取对应的名称
	 * @param string $channel
	 * @return string
	 */
	public static function getChannelName($channel){
		$channels = Common::resetKey(Client_Service_Spider::$channels, 'domain');
		$channel = parse_url($channel, PHP_URL_HOST);
		foreach($channels as $key=>$item){
			if(stripos(strrev($channel), strrev($key)) === 0){
				return $item['name'];
			}
		}
		return $channel;
	}

	public static function channel($channelId) {
		$channel = "";
		foreach (self::$channels as $key => $value) {
			if ($value["channel_id"] == $channelId) {
				$channel = $key;
				break;
			}
		}
		return $channel;
	}

	public static function channels($key="") {
	        return $key ? self::$channels[$key] : self::$channels;
	}
	/**	
	* parse url
	* @param string $url
	*/
	public static function parseUrl($url) {
	    if(!$url) return false;
	    $chl = self::getChannel($url);

	    $type = $ret = '';
	    //tmall taobao mmb
	    if($chl) {
	        switch ($chl) {
	            case 'tmall':
	                list($channel ,$type, $ret) = self::parseTmallUrl($url);
	                break;
                case 'tmall_hk':
                    list($channel ,$type, $ret) = self::parseTmallUrl($url);
                    break;
	            case 'taobao':
	                list($channel ,$type, $ret) = self::parseTaobaoUrl($url);
	                break;
	            case 'mmb':
	                list($channel ,$type, $ret) = self::parseMmbUrl($url);
	                break;
	            case 'jd':
	                list($channel ,$type, $ret) = self::parseJdUrl($url);
	                break;
	            default:
	                list($channel ,$type, $ret) = self::parseWebUrl($url);
	        }
	    } else {
	        //网页
	        $parse = parse_url($url);
	        $domain = sprintf('%s://%s', $parse['scheme'], $parse['host']);
	        list($channel ,$type, $ret) = array('other' ,'web', $domain);
	    }
        
        return array($channel ,$type, $ret);
	}

	public static function getSameStyle($params,$platform='taobao'){
		$api = new Api_Miigou_Data();
		return $api->getSamestyle($params,$platform);
	}

	public static function getItemByType($type,$item_id){
		if($type=='shop')return Third_Service_Shop::getBy(array('shop_id'=>$item_id));
		if($type=='goods')return Third_Service_Goods::get($item_id);
		if($type=='web')return Third_Service_Web::getBy(array('url_id'=>$item_id));
	}

	public static function check($uid,$url){

		list($channel ,$type, $name) = self::parseUrl(html_entity_decode($url));

		$types=array('goods'=>2,'shop'=>3, 'web'=>4);
		if ($type == 'web') $name=Third_Service_Web::getItemId($url);

		return array(
		  'type'     => $types[$type],
		  'uid'      => $uid,
		  'channel'  => $channel,
		  'item_id'  => $name
		);
	}

	/**
	 * get data
	 * @param string $url
	 * @return boolean
	 */
	public static function getData($id,$url="") {
		$row = User_Service_Favorite::get($id);

		if(empty($row))return false;
	    $api = new Api_Miigou_Data();
		$types=array(2=>'goods',3=>'shop',4=>'web');
		if($row['item_id']){
			$item = static::getItemByType($types[$row['type']],$row['item_id']);
			if(!empty($item)){
				User_Service_Favorite::updatePrice($item['goods_id'],$item['price']);
				return true;
			}
		}
		unset($row['id']);
		$row['status']=1;
		$row['request_count']=1;
		switch ($types[$row['type']]) {
			case 'goods':
				$id = Third_Service_Goods::addGoods($row);
				$api->goodsInfo($row['item_id'], $id, $row['channel']);
				break;
			case 'shop':
				$id = Third_Service_Shop::addShop($row);
				$api->shopInfo($row['item_id'], $id, 'favorite', $row['channel']);
				break;
			case 'web':
				$row['url'] = $url;
				$id = Third_Service_Web::addWeb($row,$row['channel']);
				$api->web($url, $id);
				break;
			default:
				break;
		}
	}
	
	
	/**
	 * parse tmall url
	 * @param unknown_type $url
	 * @return boolean|string
	 */
    public static function parseTmallUrl($url) {
	    $parse = parse_url($url);
	    
	    //tmall goods detail
	    if(strpos($parse['host'], 'detail.m.tmall') !== false) {
	        parse_str($parse['query'], $get_array);
	        return array('tmall', 'goods', $get_array['id']);
	    }
	    
	    if(strpos($parse['host'], 'm.tmall') !== false && strpos($parse['path'], 'shop/') !== false && strpos($parse['query'], 'shop_id') !== false) {
	        parse_str($parse['query'], $get_array);
	        return array('tmall', 'shop', $get_array['shop_id']);
	    }
	    
	    //tmall shop
	    $shop_id = self::get_shop_id($url);
	    if($shop_id) return array('tmall' ,'shop', $shop_id);
	    
	    $channel = self::channels('tmall');
	    return array('tmall' ,'web', $channel['name']);
	}
	
	/**
	 * parse taobao url
	 * @param unknown_type $url
	 * @return boolean|string
	 */
	public static function parseTaobaoUrl($url) {
	    $parse = parse_url($url);
	    
	    //taobao goods
	    if(strpos($parse['host'], 'taobao.com') !== false && strpos($parse['path'], 'awp/core/detail') !== false) {
	        parse_str($parse['query'], $get_array);
	        return array('taobao', 'goods', $get_array['id']);
	    }
	    
	    if((strpos($parse['host'], 'm.taobao.com') !== false || strpos($parse['path'], 'm.taobao.com') !== false )&& strpos($parse['path'], 'shop/shop_index') !== false) {
	        parse_str($parse['query'], $get_array);
	        return array('taobao', 'shop', $get_array['shop_id']);
	    }
	    
	    if(strpos($parse['host'], 'm.taobao.com') !== false && strpos($parse['path'], 'shop/') !== false && strpos($parse['query'], 'shop_id') !== false) {
	        parse_str($parse['query'], $get_array);
	        return array('taobao', 'shop', $get_array['shop_id']);
	    }
	    
	    //http://shop60552065.m.taobao.com/
	    if(strpos($parse['host'], 'm.taobao.com') !== false) {
	        preg_match('/shop(\d+).m.taobao.com/',$parse['host'],$match);
	        if($match[1]) return array('taobao', 'shop', $match[1]);
	    }
	     
	    
	    //taobao shop
	    //$shop_id = self::get_shop_id($url);
	    
	    //if($shop_id) return array('taobao_shop', $shop_id);
	    $channel = self::channels('taobao');
	    return array('taobao', 'web', $channel['name']);
	}
	
	/**
	 * parse mmb url
	 * @param unknown_type $url
	 * @return boolean|string
	 */
	public static function parseMmbUrl($url) {
	    $parse = parse_url($url);
	    if(strpos($parse['host'], 'mmb.cn') !== false && strpos($parse['path'], '/product.do') !== false) {
	        parse_str($parse['query'], $get_array);
	        return array('mmb' ,'goods', $get_array['id']);
	    }
	    if(strpos($parse['host'], 'mmb.cn') !== false && strpos($parse['path'], '/product/id_') !== false) {
	        preg_match('/id_([0-9]+).*?\.htm/',$parse['path'],$match);
	        return array('mmb', 'goods', $match[1]);
	    }
	    
	    $channel = self::channels('mmb');
	    return array('mmb' ,'web', $channel['name']);
	}

	/**
	 * @param $url
	 * @return array
	 */
	public static function parseJdUrl($url) {
	    $parse = parse_url($url);
	    if(strpos($parse['host'], 'jd.com') !== false && strpos($parse['path'], '/product/') !== false) {
	        preg_match('/\/product\/([0-9]+)\.html/',$parse['path'],$match);
	        return array('jd', 'goods', $match[1]);
	    }

	    if(strpos($parse['host'], 'jd.com') !== false && strpos($parse['path'], '/ware/view.action') !== false) {
	        preg_match('/wareId\=([0-9]+)/',$parse['query'],$match);
	        return array('jd', 'goods', $match[1]);
	    }

	    if(strpos($parse['host'], 'jd.com') !== false && strpos($parse['path'], '/m/index-') !== false) {
	        preg_match('/\/m\/index-([0-9]+).htm/',$parse['path'],$match);
	        return array('jd', 'shop', $match[1]);
	    }
	    $channel = self::channels('jd');
	    return array('jd' ,'web', $channel['name']);
	}

	/**
	 * parse mmb url
	 * @param unknown_type $url
	 * @return boolean|string
	 */
	public static function parseWebUrl($url) {
	    $parse = parse_url($url);
	    $channels = self::channels();
		$channel_name = '';
		$channel = '';
	    foreach ($channels as $key=>$value) {
	        if(strstr($parse['host'], $value['domain'])) {
	            $channel_name = $value['name'];
				$channel = $key;
	            break;
	        }	       
	    }
	    return array($channel ,'web', $channel_name);
	}
	
	/**
	 * 
	 * @param get channel
	 * @return boolean|string
	 */
	public static function getChannel($url){
	    if(!$url) return false;
	    $channels = self::channels();
	    foreach ($channels as $key=>$value){
	        if(strpos($url, $value['domain']) !== false) {
	            $channel = $key;
	            break;
	        }
	    }
	    return $channel;
	}
	
	
	/**
	 * get taoke shop info
	 * @param unknown_type $shop_id
	 * @param unknown_type $item_id
	 */
	public static function get_shop_info($shop_id, $item_id) {
	    $topApi  = new Api_Top_Service();
	    $shop = $topApi->getTbkShopInfo(array('sids'=>$shop_id, 'is_mobile'=>'true'));
	    if($shop) {
	        $data = array(
	        'data'=>json_encode(array('title'=>$shop['shop_title'], 'image'=>$shop['pic_url'])),
	        'title'=>$shop['shop_title'],
	        'image'=>$shop['pic_url']
	        );
	        User_Service_Favorite::update($data, $item_id);
	    }
	}
	
	
	/**
	 * get shop id
	 * @param unknown_type $url
	 * @return string
	 */
	public static function get_shop_id($url)	{
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL,            $url);
	    curl_setopt($ch, CURLOPT_HEADER,         true);
	    curl_setopt($ch, CURLOPT_NOBODY,         true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.94 Safari/537.36');
	    curl_setopt($ch, CURLOPT_REFERER, 'http://www.taobao.com');
	
	    $response = curl_exec($ch);
	    curl_close($ch);
	    //$headers = split("\n", $headers);
	     
	    $headers = array();
	
	    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
	
	    foreach (explode("\r\n", $header_text) as $i => $line){
	        if ($i === 0){
	            $headers['http_code'] = $line;
	        }else{
	            list ($key, $value) = explode(': ', $line);
	
	            $headers[$key] = $value;
	        }
	    }
	     
	    return $headers['Shop_Id'] ? base64_decode($headers['Shop_Id']) : '';
	}
}
