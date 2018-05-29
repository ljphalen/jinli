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
		$from_Currency = urlencode($from_Currency);
		$to_Currency = urlencode($to_Currency);
		$url = "download.finance.yahoo.com/d/quotes.html?s=".$from_Currency.$to_Currency."=X&f=sl1d1t1ba&e=.html";
		$ch = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$rawdata = curl_exec($ch);
		curl_close($ch);
		$data = explode(',', $rawdata);

		if(!empty($data)) return $data[1];
		return false;
	}
}

?>
