<?php
/**
 * 百度安全检测
 * @author jiazhu
 * @version 2014-01-03
 *
 */
class SafeBaiduModel extends SafeModel
{
	protected $trueTableName = "apk_safe";
	
	protected $_security_key = '';		//第三方分配key
	protected $_tpl = '';				//安全实验室分配给合作方
	
	private $_scan_url = 'http://scan.safe.baidu.com/api/v2/tpl/apkscan';		//百度扫描URL http://xxx/xx?tpl=xxxx&sign=3cbad7bd1ffcbb13c02d8b5480bc511e
	
	
	/**
	 * 向百度发送请求扫描
	 * @param unknown_type $apk_id
	 */
	public function requestScan($apk_info)
	{
		//获得apk info
		
		
		/*	组合数据
		 *  request = {
				"scanlist":[
				{"md5":"3cba7d5...181e","url":"http://a.cn/1.apk","meta":{}},
				{"md5":"980bdc1...a17f","url":"http://a.cn/2.apk","meta":{}},
				],
				"priority":"1"
				}
		 */
		//apk地址临时处理
		if ($_SERVER['SERVER_ADDR'] == '211.157.185.89')
		{
			$apk_path = 'http://211.157.185.89/Data/Attachments/dev/apks/'.$apk_info['file_path'];
		}else
		{
			$apk_path = Helper("Apk")->get_url('apk').$apk_info['file_path'];
		}
		
		$request = array(
			'scanlist' => array(
									array('md5' => $apk_info['apk_md5'],'url' => $apk_path)
								),
			'priority' => "0",
		);
		$post = array('request' =>json_encode($request));
		$sign_data = array(
							 'tpl' => C('SAFE_BAIDU_TPL'),
							 'request' => json_encode($request)
		);
		//生成签名
		$sign = $this->generateSig($sign_data, C('SAFE_BAIDU_SECURITY'));
		
		//发送请求
		$url = $this->_scan_url.'?tpl='.C('SAFE_BAIDU_TPL').'&sign='.$sign;
		$request_res = curl_post($url,$post);
		$request_arr = json_decode($request_res,true);
		//var_dump($request_res,$post,$url);
		$deal_status = $request_arr['error_code'] == 0?ApkSafeModel::DEAL_STATUS_SEND:ApkSafeModel::DEAL_STATUS_ERROR;
		
		return array('deal_status' => $deal_status, 'request_res' => $request_res);
	}
	
	/**
	 * 扫描结果回调
	 * @param string $sign 签名
	 * @param string $data 返回数据json串
	 * scanResult = {
			"resultList":[
			{
			"error_code": 0,
			"error_info": "no error"
			"md5": "3cbad7bd1ffcbb13c02d8b5480bc511e",
			"safe_type": "1",
			"virus" : [
			{“name”: "Trojan!KungFu.A@Android", “info”:"该应用能..."},
			{“name”: "Adware!Youmi.C@Android", “info”:"该应用属于..."}],
			"privacy": ["位置追踪", "读取联系人", "读取短信彩信"],
			"risk": ["恶意扣费", "隐私窃取", "系统破坏"],
			"adinfo":[{"name":"优米", "action":["推送广告"], "type":["通知栏广告","广告条"]}, ]
			"iapinfo":[{"name":"谷歌", "action":["信用卡支付","话费支付"], "type":["xxx",""]}, ]
			"statsinfo":[{"name":"Google Analytics", "action":["",""], "type":["",""]},]
			},
			{..},
			]
			}

	 */
	public function scanResult($sign,$data)
	{
		//处理data
		$data_arr = json_decode($data,true);
		$result_list = $data_arr['resultList'];
		if (is_array($result_list))
		{
			foreach ($result_list as $key=> $val)
			{
				$safe_status = $val['error_code'] ==0?ApkSafeModel::STATUS_SUC:ApkSafeModel::STATUS_FAIL;
				$apk_md5 = $val['md5'];
				$new_data = array('safe_status' => $safe_status,'response_res' => $val);
				$this->dealScan($apk_md5,$new_data,parent::SITE_BAIDU);
			
			}
		}		
		return true;
	}
	
	public function rescan($id)
	{
		$apk_info = D('Dev://Apks')->find($id);
		
		$url = 'http://scan.safe.baidu.com/api/v1/apk/report';
		$request = array('apikey'=>C('SAFE_BAIDU_SECURITY') ,'samples' => $apk_info['apk_md5']);
		$res = curl_post($url,$request);
		return $res;
	}
	
	/**
	 * 生成百度签名
	 * @param array $params
	 * @param string $secret
	 */
	public function generateSig($params, $secret) 
	{
		 $str = '';
		 //签名字符串
		 //先将参数以其参数名的字母升序进行排序
		 ksort($params);
		 //遍历排序后的参数数组中的每一个 key/value 对
		 foreach( $params as $k => $v ) 
		 {
			//为 key/value 对生成一个 key=value 格式的字符串,并拼接字符串
			if ($k != 'sign') {
				$str .= "$k=$v";
		 	}
		 	
		  } 	
		   //将签名密钥拼接到签名字符串最后面
		   $str .= $secret;
		    //返回待签名字符串的 md5 签名值
		  return md5($str);
		

	}

//==========================文档返回内容暂时 ===============
	/*
	 * 数字类型，“1”："Clean"，安全应用
				“2”："LowRisk"，低风险应用
				“3”："HighRisk"，高风险应用
				“4”："Malicious"，恶意应用
	 */ 
	public static  function safeType($val=null)
	{
		$arr = array(
					   1 => '安全应用',
					   2 => '低风险应用',
					   3 => '高风险应用',
					   4 => '恶意应用',
					   );
		if ($val === null)
		{
			return $arr;
		}else
		{
			return $arr[$val];
		}
		
	}
	
	
	/**
	 * 内容展现
	 * @param json $json 返回json数据
	 */
	public static function show($json)
	{
		if (empty($json)) return false;
		$str = '';
		$data = json_decode($json,true);
		
		if (!empty($data))
		{
			$str .= '<b>文件md5:</b> '.$data['md5']."<br/>";
			$str .= '<b>安全类型:</b> '.self::safeType($data['safetype'])."<br/>";
			$str .= '<b>隐私泄露:</b> '.$data['privacy']."<br/>";
			$str .= '<b>病毒列表:</b> '.$data['virus']."<br/>";
			$str .= '<b>广告信息:</b> '.$data['adinfo']."<br/>";
			
		}
		return $str;
	}
}