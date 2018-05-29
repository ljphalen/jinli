<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 汇率 class
 * @author Terry
 */
class Util_Exchange{

	/**
	 * 获取实时汇率
	 * @param string $from_Currency CNY
	 * @param string $to_Currency USD
	 * @return num|bool
	 */
	public static function getExchangeRate($from_Currency = 'CNY',$to_Currency = 'USD')
	{
        $cache = Common::getCache();
        $data  = $cache->get("exchange_rate");
        if (empty($data)) $data = static::getAllRateList();
        $rate = $data[$to_Currency] / $data[$from_Currency];
        return $rate;
	}


    /**
     *
     * @return array 所有的汇率均为对USD
     */
    private static function getAllRateList()
    {
        $url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20json%20where%20url%3D%22http%3A%2F%2Ffinance.yahoo.com%2Fwebservice%2Fv1%2Fsymbols%2Fallcurrencies%2Fquote%3Fformat%3Djson%22&format=json&diagnostics=true&callback=";
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        $content = json_decode($data,1);
        if(empty($content['query']['results']['list']['resources'])){
            return false;
        }
        $list = $content['query']['results']['list']['resources'];
        foreach ($list as $v) {
            $tmp = $v['resource']['fields'];
            $k   = explode('/', $tmp['name'])[1];
            if (empty($k)) continue;
            $temp[$k] = $tmp['price'];
        }
        $temp["USD"]=1;
        $cache      = Common::getCache();
        $cache_time = self::getCacheTime();
        $cache->set("exchange_rate", $temp, $cache_time);

        return $temp;
    }


    private static function getCacheTime()
    {
        $diff = time() - strtotime("12:30");
        if ($diff > 0) {
            $cache_time = 3600 * 24 - $diff;
        } else {
            $cache_time = 0 - $diff;
        }
        return $cache_time;
    }
}

