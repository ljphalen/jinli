<?php
/*
Sean Huber CURL library
This library is a basic implementation of CURL capabilities.
It works in most modern versions of IE and FF.
==================================== USAGE ====================================
It exports the CURL object globally, so set a callback with setCallback($func).
(Use setCallback(array('class_name', 'func_name')) to set a callback as a func
that lies within a different class)
Then use one of the CURL request methods:
get($url);
post($url, $vars); vars is a urlencoded string in query string format.
Your callback function will then be called with 1 argument, the response text.
If a callback is not defined, your request will return the response text.
*/
class CURL {
	var $callback = false;
	function setCallback($func_name) {
		$this->callback = $func_name;
	} 
	function doRequest($method, $url, $vars, $header_info = 1) {
		$header_info = !$header_info ? 0 : 1;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, $header_info);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		} 
		$data = curl_exec($ch);

		if ($data) {
			curl_close($ch);
			if ($this->callback) {
				$callback = $this->callback;
				$this->callback = false;
				return call_user_func($callback, $data);
			} else {
				return $data;
			} 
		} else {
			$err = curl_error($ch);
			curl_close($ch);
			return $err;
		} 
	} 
	function get($url, $header_info = 1) {
		return $this->doRequest('GET', $url, 'NULL', $header_info);
	} 
	function post($url, $vars, $header_info = 1) {
		return $this->doRequest('POST', $url, $vars, $header_info);
	} 
} 

?>