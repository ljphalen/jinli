<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * RSA加密
 * the last known user to change this file in the repository
 * @author rainkide<rainkide@gmail.com>
 * @copyright Copyright &copy; 2003-2011 phpwind.com
 * @license 
 */
/**
 * 产生对称密钥
 *$res = openssl_pkey_new();
 *openssl_pkey_export($res,$pri);
 *$d= openssl_pkey_get_details($res);
 *$pub = $d['key'];
 *var_dump($pri,$pub);
 */
class Util_Rsa {
	public static $outAlgorithm = 'base64_encode'; //bin2hex方式无法用本类中的verify方法校验,专用于UUID校验(对方是nodejs)
	public static $signAlgorithm = 'SHA1'; //默认是SHA1
	
	
	/**
	 * 组装
	 * @param unknown_type $url
	 * @param array $param
	 * @param string $pemFile	私钥文件
	 * @return
	 */
	public static function buildRequest(array $param, $pemFile) {
		$sign = self::build_mysign($sort_array, $pemFile, self::$signAlgorithm);
		if (!$sign) return false;
		return self::create_valuestr($param) . '&sign=' . urlencode($sign);
	}
	
	/**
	 * 验证签名
	 * @param array $param
	 * @param unknown_type $sign
	 * @param unknown_type $pemFile
	 * @return
	 */
	public static function verifySign(array $param, $sign, $pemFile) {
		$param = self::para_filter($param);
		$sort_array = self::arg_sort($param);
		
		$prestr = self::create_valuestr($sort_array);
		return self::verify($prestr, $sign, $pemFile);
	}
	
	/**生成签名结果
	 * $array要签名的数组
	 * return 签名结果字符串
	 */
	public function build_mysign($sort_array, $pemFile, $signAlgorithm) {
		$sort_array = self::para_filter($sort_array);
		$sort_array = self::arg_sort($sort_array);
// 		print_r(json_encode($sort_array));
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = self::create_valuestr($sort_array);
// 		print_r($prestr);
		//调用RSA签名方法
		$mysgin = self::sign($prestr, $pemFile);
		return $mysgin;
	}
	
	/********************************************************************************/
	
	/**RSA签名
	 * $data签名数据(需要先排序，然后拼接)
	 * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
	 * 最后的签名，需要用base64编码
	 * return Sign签名
	 */
	public function sign($data, $pemFile) {
		//读取私钥文件
		if (!is_file($pemFile)) return false;
		$priKey = file_get_contents($pemFile);
		//转换为openssl密钥，必须是没有经过pkcs8转换的私钥
		$res = openssl_get_privatekey($priKey);
		//调用openssl内置签名方法，生成签名$sign
		openssl_sign($data, $sign, $res, self::$signAlgorithm);
		//释放资源
		openssl_free_key($res);
		//base64编码
		if (self::$outAlgorithm == 'bin2hex') {
			$sign = bin2hex($sign);
		} else {
			$sign = base64_encode($sign);
		}
		return $sign;
	}
	
	/********************************************************************************/
	
	/**RSA验签
	 * $data待签名数据(需要先排序，然后拼接)
	 * $sign需要验签的签名,需要base64_decode解码
	 * 验签用支付宝公钥
	 * return 验签是否通过 bool值
	 */
	public function verify($data, $sign, $pemFile) {
		//读取支付宝公钥文件
		if (!is_file($pemFile)) return false;
		$pubKey = file_get_contents($pemFile);
		
		//转换为openssl格式密钥
		$res = openssl_get_publickey($pubKey);
		
		//调用openssl内置方法验签，返回bool值
		$sign = base64_decode($sign);
		$result = (bool) openssl_verify($data, $sign, $res, self::$signAlgorithm);
		
		//释放资源
		openssl_free_key($res);
		
		//返回资源是否成功
		return $result;
	}
	
	/********************************************************************************/
	
	/**解密
	 * $content为需要解密的内容
	 * 解密用商户私钥
	 * 解密前，需要用base64将内容还原成二进制
	 * 将需要解密的内容，按128位拆开解密
	 * return 解密后内容，明文
	 */
	public function decrypt($content, $pemFile) {
		//读取商户私钥
		if (!is_file($pemFile)) return false;
		$priKey = file_get_contents($pemFile);
		
		//转换为openssl密钥，必须是没有经过pkcs8转换的私钥
		$res = openssl_get_privatekey($priKey);
		
		//密文经过base64解码
		$content = base64_decode($content);
		
		//声明明文字符串变量
		$result = '';
		
		//循环按照128位解密
		for ($i = 0; $i < strlen($content) / 128; $i++) {
			$data = substr($content, $i * 128, 128);
			
			//拆分开长度为128的字符串片段通过私钥进行解密，返回$decrypt解析后的明文
			openssl_private_decrypt($data, $decrypt, $res);
			
			//明文片段拼接
			$result .= $decrypt;
		}
		
		//释放资源
		openssl_free_key($res);
		
		//返回明文
		return $result;
	}
	
	/********************************************************************************/
	
	/**把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * $array 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	public function create_linkstring($array) {
		$arg = "";
		while (list($key, $val) = each($array)) {
			$arg .= $key . "=" . $val . "&";
		}
		//去掉最后一个&字符
		$arg = substr($arg, 0, count($arg) - 2);
		return $arg;
	}
	
	/**
	 * 把数组中value链接起来
	 * @param array $array
	 * @return 拼接完成以后的字符串
	 */
	public function create_valuestr($array) {
		$arg = "";
		while (list($key, $val) = each($array)) {
			$arg .= $val;
		}
		return $arg;
	}
	
	/********************************************************************************/
	
	/**除去数组中的空值和签名参数
	 * $parameter 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 */
	public function para_filter($parameter) {
		$para = array();
		while (list($key,$val) = each($parameter)) {
			if ($key == "sign" || $key == "sign_type" || $val == "")
				continue;
			else
				$para[$key] = $parameter[$key];
		}
		return $para;
	}
	
	/********************************************************************************/
	
	/**对数组排序
	 * $array 排序前的数组
	 * return 排序后的数组
	 */
	public function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

}
