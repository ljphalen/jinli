<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * meilishuo goods api
 * @author tiansh
 *
 */
class Api_Meilishuo_Goods{
	
	/**
	 * 
	 * @param array $params
	 */
	public static function getGoods(array $params) {
	    $na_id = Common::getConfig('apiConfig', 'meilishuo_na_id');
	    $appkey = Common::getConfig('apiConfig', 'meilishuo_appkey');
	    
	    $params = array_merge(
    	    array(
    	        'na_id' => $na_id,
    	        'appkey'=>$appkey,
    	        'goodstype'=>0,    	        
    	    ),
	        $params
	    );
		return self::_getResponse('http://adsapi.meilishuo.com/goods/get_items_info?', $params);
	}
	
	/**
	 * 
	 * @param string $url
	 * @param array $params
	 * @return
	 */
	private static function _getResponse($url, $params) {
		$params = http_build_query($params);	
		$response = Util_Http::post($url . $params);
		
		//logs
		$log_data = array(
			'mark'=>$params[''],
			'api_type'=>'meilishuo_goods',
			'url'=>$url,
			'request'=>$params, 
			'response'=>$response->data,
			'create_time'=>Common::getTime()
		);
		// Gou_Service_ApiLog::add($log_data);
		Common::getQueue()->push("api_log", $log_data);
		
		if ($response->state !== 200) {
			Common::log(array($url, $params, $response), 'meilishuo_response.log');
			return false;
		}
		$ret = json_decode($response->data, true);
		return $ret;
	}	
}