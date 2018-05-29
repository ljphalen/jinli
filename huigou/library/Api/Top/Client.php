<?php
if (! defined ( 'BASE_PATH' )) exit ( 'Access Denied!' );

/**
 *
 * @author rainkid
 *        
 */
class Api_Top_Client {
	public $appkey;
	public $secretKey;
	public $gatewayUrl = "http://gw.api.taobao.com/router/rest";
	public $format = "xml";
	protected $signMethod = "md5";
	protected $apiVersion = "2.0";
	protected $sdkVersion = "top-sdk-php-20110518";
	protected function generateSign($params) {
		ksort ( $params );
		
		$stringToBeSigned = $this->secretKey;
		foreach ( $params as $k => $v ) {
			if ("@" != substr ( $v, 0, 1 )) {
				$stringToBeSigned .= "$k$v";
			}
		}
		unset ( $k, $v );
		$stringToBeSigned .= $this->secretKey;
		
		return strtoupper ( md5 ( $stringToBeSigned ) );
	}
	protected function _curl($url, $postFields = null) {
		if (is_array ( $postFields ) && 0 < count ( $postFields )) {
			$postFiles = array ();
			
			foreach ( $postFields as $k => $v ) {
				if ("@" === substr ( $v, 0, 1 )) { // 判断是不是文件上传
					$postFiles [$k] = file_get_contents ( substr ( $v, 1 ) );
				}
			}
			$postBody = Util_Http::preparePostBody ( $postFields, $postFiles );
			$response = Util_Http::post ( $url, $postBody ['data'], $postBody ['headers'] );
		} else {
			$response = Util_Http::get ( $url );
		}
		
		return $response;
	}
	protected function curl($url, $postFields = null) {
		$response = $this->_curl ( $url, $postFields );
		if (200 !== $response->state) {
			throw new Exception ( $response->message, $response->state );
		}
		
		return $response->data;
	}
	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
		$logData = array (
				date ( "Y-m-d H:i:s" ),
				$apiName,
				$this->appkey,
				$localIp,
				PHP_OS,
				$this->sdkVersion,
				$requestUrl,
				$errorCode,
				str_replace ( "\n", "", $responseTxt ) 
		);
	}
	protected function logBizError($resp) {
		$logData = array (
				date ( "Y-m-d H:i:s" ),
				$resp 
		);
	}
	public function execute($request, $session = null) {
		// 组装系统参数
		$sysParams ["app_key"] = $this->appkey;
		$sysParams ["v"] = $this->apiVersion;
		$sysParams ["format"] = $this->format;
		$sysParams ["sign_method"] = $this->signMethod;
		$sysParams ["method"] = $request->getApiMethodName ();
		$sysParams ["timestamp"] = date ( "Y-m-d H:i:s" );
		$sysParams ["partner_id"] = $this->sdkVersion;
		
		if (null != $session) {
			$sysParams ["session"] = $session;
		}
		
		// 获取业务参数
		$apiParams = $request->getApiParas ();
		
		// 签名
		$sysParams ["sign"] = $this->generateSign ( array_merge ( $apiParams, $sysParams ) );
		
		// 系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ( $sysParams as $sysParamKey => $sysParamValue ) {
			$requestUrl .= "$sysParamKey=" . urlencode ( $sysParamValue ) . "&";
		}
		$requestUrl = substr ( $requestUrl, 0, - 1 );
		
		// 发起HTTP请求
		try {
			$resp = $this->curl ( $requestUrl, $apiParams );
		} catch ( Exception $e ) {
			$this->logCommunicationError ( $sysParams ["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode (), $e->getMessage () );
			return false;
		}
		
		// 解析TOP返回结果
		$respWellFormed = false;
		if ("json" == $this->format) {
			$respObject = json_decode ( $resp );
			if (null !== $respObject) {
				$respWellFormed = true;
				foreach ( $respObject as $propKey => $propValue ) {
					$respObject = $propValue;
				}
			}
		} else if ("xml" == $this->format) {
			$respObject = @simplexml_load_string ( $resp );
			if (false !== $respObject) {
				$respWellFormed = true;
			}
		}
		
		// 返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			$this->logCommunicationError ( $sysParams ["method"], $requestUrl, "HTTP_RESPONSE_NOT_WELL_FORMED", $resp );
			return false;
		}
		
		// 如果TOP返回了错误码，记录到业务错误日志中
		if (isset ( $respObject->code )) {
			$this->logBizError ( $resp );
		}
		return $respObject;
	}
}