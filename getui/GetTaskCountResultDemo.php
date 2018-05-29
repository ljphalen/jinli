<?php
//数据在30分钟提供查询

//更新时间2013.10.22

//任务实际推送数和接收数查询Demo
header("Content-Type: text/html; charset=utf-8");
	$rep = getPushResult("http://sdk.open.api.igexin.com/api.htm","tpDVam96sY8pxhwBupJ462","TBokfpttQJ6aHIhBE9y867","GT_1017_gJs4GvJxZV77gdgBKsuvO9");
	var_dump($rep);
	function getPushResult($url,$appKey,$masterSecret,$taskId){
		$params = array();
		$params["action"] = "getPushMsgResult";
		$params["appkey"] = $appKey;
		$params["taskId"] = $taskId;
		$sign = createSign($params,$masterSecret);
		$params["sign"] = $sign;
		$data=json_encode($params);
		$result = httpPost($url,$data);
		return $result;
	}
	function createSign($params,$masterSecret){
		$sign=$masterSecret;
		foreach ($params as $key => $val){
			if (isset($key)  && isset($val) ){
				if(is_string($val) || is_numeric($val) ){ // 针对非 array object 对象进行sign
					$sign .= $key . ($val); //urldecode
				}
			}
		}
		return md5($sign);
	}
	function httpPost($url,$data) {
 		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, 'GeTui PHP/1.0');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
		//请求失败有3次重试机会
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}
?>