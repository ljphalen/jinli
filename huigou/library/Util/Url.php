<?php 
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Util_Url {
	/**
	 * url字符串转化为数组格式
	 *
	 * 效果同'argsToUrl'相反
	 * @param string $url
	 * @param boolean $decode 是否需要进行url反编码处理
	 * @param string $separator url的分隔符
	 * @return array
	 */
	public static function urlToArgs($url, $decode = true, $separator = '&=') {
		if (strlen($separator) !== 2) return array();
		if (false !== $pos = strpos($url, '?')) $url = substr($url, $pos + 1);
		$url = explode($separator[0], trim($url, $separator[0]));
		$args = array();
		if ($separator[0] === $separator[1]) {
			$_count = count($url);
			for ($i = 0; $i < $_count; $i += 2) {
				if (!isset($url[$i + 1])) {
					$args[] = $decode ? rawurldecode($url[$i]) : $url[$i];
					continue;
				}
				$_k = $decode ? rawurldecode($url[$i]) : $url[$i];
				$_v = $decode ? rawurldecode($url[$i + 1]) : $url[$i + 1];
				$args[$_k] = $_v;
			}
		} else {
			foreach ($url as $value) {
				if (strpos($value, $separator[1]) === false) {
					$args[] = $decode ? rawurldecode($value) : $value;
					continue;
				}
				list($__k, $__v) = explode($separator[1], $value);
				$args[$__k] = $decode && $__v ? rawurldecode($__v) : $__v;
			}
		}
		return $args;
	}
	
	/**
	 * 将数组格式的参数列表转换为Url格式，并将url进行编码处理
	 *
	 * <code>参数:array('b'=>'b','c'=>'index','d'=>'d')
	 * 分割符: '&='
	 * 转化结果:&b=b&c=index&d=d
	 * 如果分割符为: '/' 则转化结果为: /b/b/c/index/d/d/</code>
	 * @param array $args
	 * @param boolean $encode 是否进行url编码 默认值为true
	 * @param string $separator url分隔符 支持双字符,前一个字符用于分割参数对,后一个字符用于分割键值对
	 * @return string
	 */
	public static function argsToUrl($args, $encode = true, $separator = '&=') {
		if (strlen($separator) !== 2) return;
		$_tmp = '';
		foreach ((array) $args as $key => $value) {
			$value = $encode ? rawurlencode($value) : $value;
			if (is_int($key)) {
				$value && $_tmp .= $value . $separator[0];
				continue;
			}
			$key = ($encode ? rawurlencode($key) : $key);
			$_tmp .= $key . $separator[1] . $value . $separator[0];
		}
		return trim($_tmp, $separator[0]);
	}
}
