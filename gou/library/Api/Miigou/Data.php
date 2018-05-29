<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Api_Miigou_Data {

    public $webUrl = '/api/other/get';

	public $shopUrl = array(
	  'taobao' => '/api/taobao/shop',
	  'tmall' => '/api/tmall/shop',
	  'jd' => '/api/jd/shop',
	);
	public $goodsUrl = array(
	  'taobao' => '/api/taobao/item',
	  'tmall' => '/api/tmall/item',
	  'jd' => '/api/jd/item',
	  'mmb' => '/api/mmb/item',
	);
	public $sameStyleUrl = array(
	  'taobao' => '/api/taobao/samestyle',
	  'tmall' => '/api/tmall/samestyle',
	  'jd' => '/api/jd/samestyle',
	);
	public $sameStyleCallbackUrl = array(
	  'taobao' => '/api/apk_spider/getSameStyle',
	  'tmall' => '/api/apk_spider/getSameStyle',
	  'jd' => '/api/apk_spider/getSameStyle',
	);
	public $goodsCallbackUrl=array(
	  'mmb' => '/api/apk_spider/getMmbGoods',
	  'taobao' => '/api/apk_spider/getTaobaoGoods',
	  'tmall' => '/api/apk_spider/getTaobaoGoods',
	  'jd' => '/api/apk_spider/getJdGoods',
	);
	public $shopCallbackUrl=array(
	  'mmb' => '/api/apk_spider/getShop',
	  'tmall' => '/api/apk_spider/getShop',
	  'taobao' => '/api/apk_spider/getShop',
	  'jd' => '/api/apk_spider/getShop',
	);
    //callback
    public $webCallbackUrl = '/api/apk_spider/getWeb';

	public function getApiUrl(){
		return Common::getConfig('apiConfig', 'spider_url');
	}

	/**
	 * 根据id 查询店铺信息
	 * @return array
	 */
	public function shopInfo($shop_id, $item_id, $type = 'favorite',$platform='tmall') {
		$api_url=sprintf("%s%s?id=%d",$this->getApiUrl(),$this->shopUrl[$platform],$shop_id);
	    $callback = sprintf('%s%s?item_id=%s&type=%s',Common::getWebRoot(),$this->shopCallbackUrl[$platform], $item_id,$type);
	    $url = sprintf('%s&callback=%s',$api_url, urlencode($callback));
//		Common::log(array('type'=>'shop','platform' => $platform, 'url' => $api_url,'callback'=>urldecode($callback)), 'spider.log');
		Util_Http::get($url);
	}

	public function getSameStyle($params,$platform='taobao') {
	    $api_url = sprintf('%s%s?%s',$this->getApiUrl(),$this->sameStyleUrl[$platform],http_build_query($params));
	    $callback = sprintf('%s%s?type=%s&platform=%s',Common::getWebRoot(),$this->sameStyleCallbackUrl[$platform], 'sameStyle',$platform);
	    $url = sprintf('%s&callback=%s',$api_url,urlencode($callback));
//		Common::log(array('type'=>'same','platform'=>$platform,'url'=>$api_url,'callback'=>urldecode($callback)), 'spider.log');
	    $res=Util_Http::get($url);
	    if($res->state==200) return json_decode($res->data, true);
	    return array();
	}

	/**
	 * mmb信息
	 * @return array
	 */
	public function goodsInfo($goods_id, $item_id, $platform='taobao')
	{
	    $api_url  =  sprintf('%s%s?id=%d',$this->getApiUrl(),$this->goodsUrl[$platform],$goods_id);
		$callback = sprintf('%s%s?item_id=%s',Common::getWebRoot(),$this->goodsCallbackUrl[$platform],$item_id);
	    $url = sprintf('%s&callback=%s',$api_url,urlencode($callback));
//		Common::log(array('type'=>'goods','platform'=>$platform,'url' =>$api_url,'callback'=>urldecode($callback), 'url'=>$url), 'spider.log');
	    Util_Http::get($url);
	}
	
	/**
	 * web
	 * @return array
	 */
	public function web($url, $item_id) {
	    $api_url = sprintf('%s%s?url=%s',$this->getApiUrl(),$this->webUrl,urlencode($url));
		$callback = urlencode(Common::getWebRoot().$this->webCallbackUrl.'?item_id='.$item_id);
		$url = sprintf('%s&callback=%s',$api_url,$callback);
//		Common::log(array('type'=>'web','url'=>$api_url,urldecode($callback)), 'spider.log');
	    Util_Http::get($url);
	}
}
