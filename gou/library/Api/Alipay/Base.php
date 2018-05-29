<?php
class Api_Alipay_Base
{
    //合作者身份ID
	public $partner = '2088211816772519';	
	//收款邮箱
	public $accountName = 'findjoyinc@findjoy.cn';
	//key
	public $key = '4mcb5l5cm3lta2ey0mmt6fooxo4c0nl2';
	//请求参数格式 	
	public $format  = "xml";
	
	//$gatewayUrl
	public $gatewayUrl = '';
	
	//加密方式
	protected $sec_id	 = "0001";
	//加密方式
	protected $sign_type = "";
	//接口版本号	
	protected $apiVersion = "2.0";
	//唯一的request id
	protected $req_id = '';
	//支持的字符集
	protected $_input_charset = 'utf-8';
	
	//请求模式
	protected $transport = 'http';
	
	//private pem file
	protected $rsaPriFile;
	
	//public pem file
	protected $rsaPubFile;
	
	//cacert pem file
	protected $cacertFile;
	
	/**
	 * 构造方法
	 */
	public function __construct() {
	    $this->rsaPriFile = Common::getConfig('siteConfig', 'alipayRsaPriFile');
	    $this->rsaPubFile = Common::getConfig('siteConfig', 'alipayRsaPubFile' );
	    $this->cacertFile = Common::getConfig('siteConfig', 'cacertFile' );
	    $this->req_id = date('YmdHis', Common::getTime());
	    $this->initParams();
	}
	
	
	/**
	 * 预留 init
	 */
	public function initParams(){
	    
	}
	

	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	public function createLinkstring($para) {
	    $arg  = "";
	    while (list ($key, $val) = each ($para)) {
	        $arg.=$key."=".$val."&";
	    }
	    //去掉最后一个&字符
	    $arg = substr($arg,0,count($arg)-2);
	
	    //如果存在转义字符，那么去掉转义
	    if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
	    return $arg;
	}
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	public function createLinkstringUrlencode($para) {
	    $arg  = "";
	    while (list ($key, $val) = each ($para)) {
	        $arg.=$key."=".urlencode($val)."&";
	    }
	    //去掉最后一个&字符
	    $arg = substr($arg,0,count($arg)-2);
	
	    //如果存在转义字符，那么去掉转义
	    if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
	    return $arg;
	}
	/**
	 * 除去数组中的空值和签名参数
	 * @param $para 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 */
	public function paraFilter($para) {
	    $para_filter = array();
	    while (list ($key, $val) = each ($para)) {
	        if($key == "sign" || $key == "sign_type" || $val == "")continue;
	        else	$para_filter[$key] = $para[$key];
	    }
	    return $para_filter;
	}
	/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
	 */
	public function argSort($para) {
	    ksort($para);
	    reset($para);
	    return $para;
	}
	
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	public function buildRequestMysign($para_sort) {
	    //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	    $prestr = $this->createLinkstring($para_sort);
	    
	    $mysign = "";
	    switch (strtoupper(trim($this->sign_type))) {
	        case "MD5" :
	            $mysign = Api_Alipay_Md5::md5Sign($prestr, $this->key);
	            break;
	        case "RSA" :
	            $mysign = Api_Alipay_Rsa::rsaSign($prestr, $this->rsaPriFile);
	            break;
	        case "0001" :
	            $mysign = Api_Alipay_Rsa::rsaSign($prestr, $this->rsaPriFile);
	            break;
	        default :
	            $mysign = "";
	    }
	
	    return $mysign;
	}
	
	/**
	 * 生成要请求给支付宝的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组
	 */
	function buildRequestPara($para_temp) {
	    //除去待签名参数数组中的空值和签名参数
	    $para_filter = $this->paraFilter($para_temp);
	
	    //对待签名参数数组排序
	    $para_sort = $this->argSort($para_filter);
	
	    //生成签名结果
	    $mysign = $this->buildRequestMysign($para_sort);
	
	    //签名结果与签名方式加入请求提交参数组中
	    $para_sort['sign'] = $mysign;
	    if($para_sort['service'] != 'alipay.wap.trade.create.direct' && $para_sort['service'] != 'alipay.wap.auth.authAndExecute') {
	        $para_sort['sign_type'] = strtoupper(trim($this->sign_type));
	    }
	    return $para_sort;
	}
	
	/**
	 * 生成要请求给支付宝的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组字符串
	 */
	function buildRequestParaToString($para_temp) {
	    //待请求参数数组
	    $para = $this->buildRequestPara($para_temp);
	
	    //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
	    $request_data = $this->createLinkstringUrlencode($para);
	
	    return $request_data;
	}
	
	
	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果
	 * @param $para_temp 请求参数数组
	 * @return 支付宝处理结果
	 */
	public function buildRequestHttp($para_temp)
	{
	    $sResult = '';
	    
	    //待请求参数数组字符串
	    $request_data = $this->buildRequestPara($para_temp);
	    /* $request_data = $this->createLinkstringUrlencode($request_data);
	    print_r($request_data);die;  */
	    //远程获取数据
	    $sResult = $this->getHttpResponsePOST($this->gatewayUrl, $this->cacertFile, $request_data,trim(strtolower($this->_input_charset)));
	    return $sResult;
	}
	
	/**
	 * 远程获取数据，POST模式
	 * 注意：
	 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
	 * @param $url 指定URL完整路径地址
	 * @param $cacert_url 指定当前工作目录绝对路径
	 * @param $para 请求的数据
	 * @param $input_charset 编码格式。默认值：空值
	 * return 远程输出的数据
	 */
	function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {
	
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	    curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
	    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
	    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
	    $responseText = curl_exec($curl);
	    
	    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	    curl_close($curl);
	
	    return $responseText;
	}
	
	/**
	 * 远程获取数据，GET模式
	 * 注意：
	 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
	 * @param $url 指定URL完整路径地址
	 * @param $cacert_url 指定当前工作目录绝对路径
	 * return 远程输出的数据
	 */
	function getHttpResponseGET($url, $cacert_url) {
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	    curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
	    $responseText = curl_exec($curl);
	    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	    curl_close($curl);
	
	    return $responseText;
	}
	
	/**
	 * 建立请求，以表单HTML形式构造（默认）
	 * @param $para_temp 请求参数数组
	 * @param $method 提交方式。两个值可选：post、get
	 * @param $button_name 确认按钮显示文字
	 * @return 提交表单HTML文本
	 */
	function buildRequestForm($para_temp, $method, $button_name) {
	    //待请求参数数组
	    $para = $this->buildRequestPara($para_temp);
	
	    $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gatewayUrl."_input_charset=".trim(strtolower($this->_input_charset))."' method='".$method."'>";
	    while (list ($key, $val) = each ($para)) {
	        $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
	    }
	    
	    //submit按钮控件请不要含有name属性
	    $sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
	
	    $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
	
	    return $sHtml;
	}
	
	
	/**
	 * 解析远程模拟提交后返回的信息
	 * @param $str_text 要解析的字符串
	 * @return 解析结果
	 */
	function parseResponse($str_text) {
        //以“&”字符切割字符串
        $para_split = explode('&',$str_text);
        //把切割后的字符串数组变成变量与数值组合的数组
        foreach ($para_split as $item) {
            //获得第一个=字符的位置
            $nPos = strpos($item,'=');
            //获得字符串长度
            $nLen = strlen($item);
            //获得变量名
	        $key = substr($item,0,$nPos);
	        //获得数值
	        $value = substr($item,$nPos+1,$nLen-$nPos-1);
	        //放入数组中
	        $para_text[$key] = $value;
	    }
	    if(!empty($para_text['res_data'])) {
	        //解析加密部分字符串
	        if($this->sign_type == '0001') {
	            $para_text['res_data'] = Api_Alipay_Rsa::rsaDecrypt($para_text['res_data'], $this->rsaPriFile);
	        }
	        //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
	        $doc = new DOMDocument();
	        $doc->loadXML($para_text['res_data']);
	        $para_text['request_token'] = $doc->getElementsByTagName( "request_token" )->item(0)->nodeValue;
	    }	
	    return $para_text;
	}
	
	
	/**
	 * rsa解密
	 */
	
	function rsaDecrypt($data){
	    return Api_Alipay_Rsa::rsaDecrypt($data, $this->rsaPriFile);
	}

}