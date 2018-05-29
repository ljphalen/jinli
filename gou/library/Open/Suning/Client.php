<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 苏宁开放平台接口-基类
 *
 * @author ryan
 */

class Open_Suning_Client{

    /**
     * 应用访问key
     */
    private $appKey;

    /**
     * 应用访问密钥
     */
    private $appSecret;

    /**
     * 服务器访问地址
     */
    private $serverUrl;

    /**
     * 请求、响应格式
     */
    private $format='json';

    private static $apiVersion = VERSION;

    private static $checkRequest = CHECK_REQ;

    private static $signMethod = "md5";

    private static $connectTimeout = 5;

    private static $readTimeout = 30;

    private static $userAgent = USER_AGENT;

    private static $sdkVersion = SDK_VERSION;

    private static $accessToken = '';


    /**
     * 网盟订单信息单笔查询
     * 名称   	         类型	  是否必须	示例值	描述
     * @param string $orderCode  Y	1001434507	订单号
     * @return mixed
     *
     *   名称	                    类型 	描述
     *   orderCode                	String	订单号
     *   payMode	                String	支付方式
     *   payTime	                String	支付时间
     *   orderSubmitTime         	String	下单时间
     *   orderLineNumber         	String	订单行项目号
     *   orderLineStatusDesc        String	订单行项目状态
     *   orderLineStatusChangeTime	String	行项目状态更新时间
     *   orderLineOrigin          	String	订单行来源
     *   productName	            String	商品名称
     *   saleNum                 	String	商品数量
     *   payAmount                  String	实付金额
     *   orderLineFlag           	String	订单行标记
     *   childAccountId	            String	子推广账号ID
     */
    public static function getOrder($orderCode) {
        $req = new Open_Suning_Request();
        $req -> setParam('orderCode',$orderCode);
        $req -> setBizName('getOrder');
        $req -> setApiMethodName("suning.netalliance.order.get");
        $req -> setCheckParam('false');
        $client = new Open_Suning_Client();
        $resp = $client -> execute($req);
        return $resp;
    }

    /**
     * 网盟订单信息批量查询
     * 名称	                        类型	  是否必须	示例值	         描述
     * @param string $startTime     String	N	2014-09-09 12:00:00	查询开始时间
     * @param string $endTime       String	N	2014-09-10 12:00:00	查询结束时间
     * @param int $pageNo           String	N	1	                页码
     * @param int $pageSize         String	N	10                  每页条数
     * @param int $orderLineStatus  String	N	1	                订单行项目状态
     * @return mixed
     *
     *  名称	                        类型	    描述
     *  orderCode	                String	订单号
     *  payMode                 	String	支付方式
     *  payTime	                    String	支付时间
     *  orderSubmitTime         	String	下单时间
     *  orderLineNumber         	String	订单行项目号
     *  orderLineStatusDesc     	String	订单行项目状态
     *  orderLineStatusChangeTime	String	行项目状态更新时间
     *  orderLineOrigin         	String	订单行来源
     *  productName              	String	商品名称
     *  saleNum                 	String	商品数量
     *  payAmount               	String	实付金额
     *  orderLineFlag              	String	订单行标记
     *  childAccountId             	String	子推广账号ID
     */
    public static function queryOrder($startTime="",$endTime="",$pageNo=1,$pageSize=30,$orderLineStatus=1) {
        $req = new Open_Suning_Request();
        $req -> setParam('startTime',$startTime);
        $req -> setParam('endTime',$endTime);
        $req -> setPageSize((string)$pageSize);
        $req -> setPageNo((string)$pageNo);
        $req -> setBizName('queryOrder');
        $req -> setApiMethodName("suning.netalliance.order.query");
        $req -> setCheckParam('false');
        $client = new Open_Suning_Client();
        $resp = $client -> execute($req);
        return $resp;
    }

    /**
     * 网盟订单结算信息查询
     * 名称	                   类型   是否必须	  示例值	             描述
     * @param $orderLineNumber String	Y	1001434507,1001434508	订单行项目号
     * @return mixed
     *
     *  名称	                        类型	    描述
     *  orderCode                 	String	订单号
     *  orderLineNumber	            String	订单行项目号
     *  orderLineStatusDesc      	String	订单行项目状态
     *  orderLineStatusChangeTime	String	行项目状态更新时间
     *  commissionRatio	            String	佣金比例
     *  payAmount	                String 	实付金额
     *  prePayCommission	        String	预付佣金金额
     *  needPayCommission	        String	应付佣金金额
     *  productName	                String	商品名称
     *  productFirstCatalog     	String	商品一级目录
     *  productSecondCatalog	    String	商品二级目录
     *  productThirdCatalog     	String	商品三级目录
     *  productFourthCatalog    	String	商品四级目录
     *  isWholesale	                String	是否批发
     *  isGrant	                    String	是否发放
     *  childAccountId	            String	子推广账号ID
     */
    public static function getOrderSettle($orderLineNumber) {
        $req = new Open_Suning_Request();
        $req -> setPageSize("10");
        $req -> setPageNo("1");
        $req -> setParam('orderLineNumber',$orderLineNumber);
        $req -> setBizName('getOrderSettle');
        $req -> setApiMethodName("suning.netalliance.ordersettle.get");
        $req -> setCheckParam('false');
        $client = new Open_Suning_Client();
        $resp = $client -> execute($req);
        return $resp;
    }


    /**
     * 构造方法
     */
    function __construct() {
        $this->setKeySecret();
    }

    /**
     * 封装头信息及签名
     *
     * $param array $params
     *
     * @return array
     */
    private function generateSignHeader($params) {
        $signString = '';
        foreach($params as $k => $v){
            $signString .= $v;
        }
        unset($k, $v);
        $signMethod = self::$signMethod;
        $signString = $signMethod($signString);

        // 组装头文件信息
        $signDataHeader = array(
          "Content-Type: text/xml; charset=utf-8",
          "AppMethod: " . $params['method'],
          "AppRequestTime: " . $params['date'],
          "Format: " . $this -> format,
          "signInfo: " . $signString,
          "AppKey: " . $params['app_key'],
          "VersionNo: " . $params['api_version'],
          "User-Agent: " . self::$userAgent,
          "Sdk-Version: " . self::$sdkVersion
        );

        if(! empty(self::$accessToken)){
            $signDataHeader['access_token'] = self::$accessToken;
        }

        return $signDataHeader;
    }

    /**
     * @param $url
     * @param null $postFields
     * @param array $header
     * @return mixed
     * @throws Exception
     */
    public static function curl($url, $postFields = null, $header = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // https 请求
        if(strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https"){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            throw new Exception(curl_error($ch), 0);
        }else{
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if(200 !== $httpStatusCode){
                throw new Exception($response, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $response;
    }



    /**
     * @param Open_Suning_Request $request
     * @return mixed
     */
    public function execute(Open_Suning_Request $request) {
        $checkParam = $request -> getCheckParam();
        if($checkParam){
            try{
                $request -> check();
            }catch(Exception $e){
                print_r($e ->__toString());
            }
        }

        // 获取业务参数
        $paramsArray = $request -> getApiParams();
        if(empty($paramsArray)){
            $paramsArray = '';
        }

        $paramsArray = array('sn_request' => array('sn_body' => array(
          "{$request -> getBizName()}" => $paramsArray
        )));
        if($this->format == "json"){
            $apiParams = json_encode($paramsArray);
        }else{
            $apiParams = ArrayToXML::parse($paramsArray["sn_request"],"sn_request");
        }
//		echo $apiParams."\n";

        // 组装系统参数
        $sysParams["secret_key"] = $this -> appSecret;
        $sysParams["method"] = $request -> getApiMethodName();
        $sysParams["date"] = date('Y-m-d H:i:s');
        $sysParams["app_key"] = $this -> appKey;
        $sysParams["api_version"] = self::$apiVersion;
        $sysParams["post_field"] = base64_encode($apiParams);

        // 头信息(内含签名)
        $signHeader = self::generateSignHeader($sysParams);

        unset($sysParams);

        // 发起HTTP请求
        try{
            $resp = self::curl($this -> serverUrl, $apiParams, $signHeader);
        }catch(Exception $e){
            print_r($e ->__toString());
        }

        return $resp;
    }

    /**
     * 设置返回的数据格式，json或xml
     *
     * $param string $out
     * $return void
     */
    public static function setFormatOutput($out) {
        self::$format = strtolower($out);
    }
    public function setKeySecret() {
        $this->appKey       = Common::getConfig('apiConfig', 'suning_app_key');
        $this->appSecret    = Common::getConfig('apiConfig', 'suning_app_secret');
        $this->serverUrl    = Common::getConfig('apiConfig', 'suning_server_url');
    }

    /**
     * OAuth授权必须设置
     *
     * @param unknown $accessToken
     *        	$return void
     */
    public static function setAccessToken($accessToken) {
        self::$accessToken = $accessToken;
    }

    public function getAppKey() {
        return $this -> appKey;
    }

    public function getAppSecret() {
        return $this -> appSecret;
    }

    public function getServerUrl() {
        return $this -> serverUrl;
    }

    public function getFormat() {
        return $this -> format;
    }

    public function setAppKey($appKey) {
        $this -> appKey = $appKey;
    }

    public function setAppSecret($appSecret) {
        $this -> appSecret = $appSecret;
    }

    public function setServerUrl($serverUrl) {
        $this -> serverUrl = $serverUrl;
    }

    public function setFormat($format) {
        $this -> format = $format;
    }
}
