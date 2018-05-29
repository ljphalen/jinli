<?php
/***********************# 云之讯开发DEMO rest_slc.php #************************************/

/**
 * @author 云之讯
 * @date   2014-07-17
 *
 *    参数意义说明
 *    $accountSid 主账号
 *    $accountToken 主账号token
 *    $clientNumber 子账号
 *    $appId 应用的ID
 *    $clientType 计费方式。0  开发者计费；1 云平台计费。默认为0.
 *    $charge 充值或回收的金额
 *    $friendlyName 昵称
 *    $mobile 手机号码
 *    $isUseJson 是否使用json的方式发送请求和结果。否则为xml。
 *    $start 开始的序号，默认从0开始
 *    $limit 一次查询的最大条数，最小是1条，最大是100条
 *    $responseMode 返回数据个格式。"JSON" "XML"
 *    $chargeType  0 充值；1 回收。
 *    $fromClient 主叫的clientNumber
 *    $toNumber 被叫的号码
 *    $toSerNum 被叫显示的号码
 *    $verifyCode 验证码内容，为数字和英文字母，不区分大小写，长度4-8位
 *    $displayNum 被叫显示的号码
 *    $templateId 模板Id
 *
 */
class Vendor_Tel {
    public $host    = "https://api.ucpaas.com";
    public $port    = "";
    public $softver = "2014-06-30";

    public function __construct() {
        $conf               = Gionee_Service_Config::getValue('voip_config');
        $data               = json_decode($conf, true);
        $this->show_number  = $data['show_number'];
        $this->timestamp    = date("YmdHis") + 7200;
        $this->accountSid   = $data['accountSid'];
        $this->accountToken = $data['accountToken'];
        $this->appId        = $data['appId'];
        $this->url          = $this->host . ":" . $this->port . "/" . $this->softver . "/Accounts/" . $this->accountSid;
    }

    //返回签名
    private function getSig() {
        $sig = $this->accountSid . $this->accountToken . $this->timestamp;
        return strtoupper(md5($sig));
    }

    // 生成授权信息
    private function getAuth() {
        $src = $this->accountSid . ":" . $this->timestamp;
        return trim(base64_encode($src));
    }

    /*
     * 主账号信息查询
     * $accountSid  主账号ID
     * @param $accountToken  string  true 主账号的Token
     * @request method  GET
     */
    public function getAccountInfo() {
        $http_method = 'GET';
        $signature   = $this->getSig();
        $url         = "?sig=" . $signature;
        $respArr     = $this->createHttpReq($url, '', $http_method);
        return !empty($respArr['account']) ? $respArr['account'] : false;
    }

    /**
     *申请client账号
     *
     * @param $clientType   计费方式。0  开发者计费；1 云平台计费。默认为0.
     * @param $charge       充值的金额
     * @param $friendlyName 昵称
     * @param $mobile       手机号码
     *
     * @request post
     */

    public function applyClient($clientType, $charge, $friendlyName, $mobile) {
        $http_method = 'POST';
        // 获取签名
        $signature = $this->getSig();
        $url       = "/Clients?sig=" . $signature;

        $body_arr = array(
            'client' => array(
                'appId'        => $this->appId,
                'clientType'   => $clientType,
                'charge'       => $charge,
                'friendlyName' => $friendlyName,
                'mobile'       => $mobile,
            ),
        );

        $body    = json_encode($body_arr);
        $respArr = $this->createHttpReq($url, $body, $http_method);
        return !empty($respArr['client']) ? $respArr['client'] : false;
    }


    /*
     * @param 释放client账号
     * @param $accountSid  主账号ID
     * @param $accountToken 主账号的Token
     * @param $clientNumber 子账号
     * @param $appId 应用ID
     * @param $request post
     */
    public function ReleaseClient($clientNumber) {

        $http_method = 'POST';
        // 获取签名
        $signature = $this->getSig();
        $url       = "/dropClient?sig=" . $signature;

        $body_arr = array(
            'client' => array(
                'clientNumber' => $clientNumber,
                'appId'        => $this->appId,
            ),
        );
        $body     = json_encode($body_arr);

        $respArr = $this->createHttpReq($url, $body, $http_method);
        return !empty($respArr) ? true : false;
    }


    /*
     *获取client账号
     * @param $accountSid  主账号ID
     * @param $accountToken 主账号的Token
     * @param $appId 应用ID
     * @param $start 开始的序号，默认从0开始
     * @param $limit 一次查询的最大条数，最小是1条，最大是100条
     * @request post
     */
    public function getClientList($start, $limit) {
        $signature = $this->getSig();
        $url       = "/clientList?sig=" . $signature;

        $body_arr = array(
            'client' => array(
                'start' => $start,
                'appId' => $this->appId,
                'limit' => $limit,
            ),
        );

        $body    = json_encode($body_arr);
        $respArr = $this->createHttpReq($url, $body, 'POST');
        return !empty($respArr['client']) ? $respArr['client'] : false;
    }


    /*
     * client信息查询 注意为GET方法
     * @param $accountSid  主账号ID
     * @param $accountToken 主账号的Token
     * @param $appId 应用ID
     * @param $start 开始的序号，默认从0开始
     * @param $limit 一次查询的最大条数，最小是1条，最大是100条
     * @request get
     */
    public function getClientInfo($mobile) {
        $http_method = 'GET';
        $signature   = $this->getSig();
        $client      = array(
            'appId'  => $this->appId,
            'mobile' => $mobile,
            'sig'    => $signature,
        );
        $url         = "/ClientsByMobile?" . http_build_query($client);
        unset($client['sig']);
        $bodyArr = array(
            'client' => $client,
        );
        $body    = json_encode($bodyArr);
        $respArr = $this->createHttpReq($url, $body, $http_method);
        return !empty($respArr['client']) ? $respArr['client'] : false;
    }

    public function getMobileInfo($clientNumber) {
        $http_method = 'GET';
        $signature   = $this->getSig();
        $client      = array(
            'appId'        => $this->appId,
            'clientNumber' => $clientNumber,
            'sig'          => $signature,
        );
        $url         = "/Clients?" . http_build_query($client);
        unset($client['sig']);
        $bodyArr = array(
            'client' => $client,
        );
        $body    = json_encode($bodyArr);
        $respArr = $this->createHttpReq($url, $body, $http_method);
        return !empty($respArr['client']) ? $respArr['client'] : false;
    }


    /*
     * 应用话单下载
     * accountSid 主账号ID
     * accountToken 主账号Token
     * appId 应用ID
     * date 枚举类型 day 代表前一天的数据（从00:00 – 23:59）；week代表前一周的数据(周一 到周日)；month表示上一个月的数据
     * @request post
     */
    public function getBillList($date) {
        $signature = $this->getSig();
        $url       = "/billList?sig=" . $signature;
        $body_arr  = array(
            'appBill' => array(
                'appId' => $this->appId,
                'date'  => $date,
            ),
        );
        $body      = json_encode($body_arr);
        $respArr   = $this->createHttpReq($url, $body, 'POST');
        return !empty($respArr['accountBill']) ? $respArr['accountBill'] : false;
    }

    /*
     * client话单下载
     * accountSid 主账号ID
     * accountToken 主账号Token
     * appId 应用ID
     * clientNumber 子账号ID
     * date 枚举类型 day 代表前一天的数据（从00:00 – 23:59）；week代表前一周的数据(周一 到周日)；month表示上一个月的数据
     *request post
     */

    public function getClientBillList($clientNumber, $date) {
        $signature = $this->getSig();
        $url       = "/Clients/billList?sig=" . $signature;

        $body_arr = array(
            'clientBill' => array(
                'appId'        => $this->appId,
                'clientNumber' => $clientNumber,
                'date'         => $date,
            ),
        );
        $body     = json_encode($body_arr);
        $respArr  = $this->createHttpReq($url, $body, 'POST');
        return !empty($respArr['accountBill']) ? $respArr['accountBill'] : false;
    }

    /*
     *
     * @$client充值
     * @$accountSid 主账号ID
     * @$accountToken 主账号Token
     * @$appId 应用ID
     * @$clientNumber 子账号ID
     * @$chargeType  0 充值；1 回收。
     * @$charge 充值的金额
     * @request post
     */

    public function chargeClient($clientNumber, $chargeType, $charge) {
        $http_method = 'POST';
        // 获取签名
        $signature = $this->getSig();
        $url       = "/chargeClient?sig=" . $signature;

        $body_arr = array(
            'client' => array(
                'appId'        => $this->appId,
                'clientNumber' => $clientNumber,
                'chargeType'   => $chargeType,
                'charge'       => $charge,
            ),
        );

        $body = json_encode($body_arr);

        $respArr = $this->createHttpReq($url, $body, $http_method);
        if (!$respArr) {
            return false;
        }
        return $respArr ? true : false;
    }

    /*
     *
     * 双向回拨
     * $fromClient 主叫的clientNumber
     * @request post
    */
    public function callBack($fromClient, $toNumber) {
        $http_method = 'POST';
        // 获取签名
        $signature = $this->getSig();
        $url       = "/Calls/callBack?sig=" . $signature;

        $body_arr = array(
            'callback' => array(
                'appId'      => $this->appId,
                'fromClient' => $fromClient,
                'to'         => $toNumber,
                'fromSerNum' => $this->show_number,
            ),
        );
        $body     = json_encode($body_arr);
        $respArr  = $this->createHttpReq($url, $body, $http_method);
        Gionee_Service_VoIP::log(array(__METHOD__, $url, $body, $respArr));
        return !empty($respArr['callback']['createDate']) ? $respArr['callback']['createDate'] : false;
    }

    /*
     * 语音验证码
     * @$accountSid 主账号ID
     * @$accountToken 主账号Token
     * @$appId 应用ID
     * @$verifyCode 验证码内容，为数字和英文字母，不区分大小写，长度4-8位
     * @$toNumber 被叫的号码
     */
    public function voiceCode($verifyCode, $toNumber) {

        $http_method = 'POST';
        // 获取签名
        $signature = $this->getSig();
        $url       = "/Calls/voiceCode?sig=" . $signature;
        $body_arr  = array(
            'voiceCode' => array(
                'appId'      => $this->appId,
                'verifyCode' => $verifyCode,
                'to'         => $toNumber,
            ),
        );
        $body      = json_encode($body_arr);
        $respArr   = $this->createHttpReq($url, $body, $http_method);
        return $respArr['voiceCode'];
    }

    /**
     * 短信验证码（模板短信）
     * @$accountSid 主账号ID
     * @accountToken 主账号Token
     * @appId        应用ID
     * @toNumber     被叫的号码
     * @templateId   模板Id
     *
     * @param        <可选> 内容数据，用于替换模板中{数字}
     *
     * @request      post
     */

    public function templateSMS($toNumbers, $templateId, $param) {
        $http_method = 'POST';
        // 获取签名
        $signature = $this->getSig();
        $url       = "/Messages/templateSMS?sig=" . $signature;
        $body_arr  = array(
            'templateSMS' => array(
                'appId'      => $this->appId,
                'templateId' => $templateId,
                'param'      => $param,
                'to'         => $toNumbers,
            ),
        );
        $body      = json_encode($body_arr);
        $respArr   = $this->createHttpReq($url, $body, $http_method);
        return $respArr['templateSMS'];
    }

    // 生成HTTP报文
    public function createHttpReq($url, $body, $http_method) {
        $opts         = array();
        $opts['http'] = array();
        // 拼装包头
        $headers             = array(
            "method" => $http_method,                // 设置发送HTTP请求方式
        );
        $headers['header']   = array();
        $headers['header'][] = "Authorization: " . $this->getAuth();
        $headers[]           = 'Accept:application/json';
        $headers['header'][] = 'Content-Type:application/json;charset=utf-8';

        if (!empty($body)) {
            $headers['header'][] = 'Content-Length:' . strlen($body);
            $headers['content']  = $body;
        }

        $opts['http'] = $headers;
        // 创建文本流
        $context = stream_context_create($opts);
        $output  = file_get_contents($this->url . $url, false, $context);
        $info    = json_decode($output, true);
        if ($info['resp']['respCode'] != '000000') {
            Gionee_Service_ErrLog::add(array(
                'type' => 'voip_ucpaas',
                'msg'  => $url . '<br>' . $body . '<br>' . $output
            ));
            return false;
        }

        return !empty($info['resp']) ? $info['resp'] : array();
    }


    public function getClientNumber($number) {
        $info = $this->getClientInfo($number);
        if (empty($info['clientNumber'])) {
            $info = $this->applyClient(0, 0, $number, $number);
        }
        return $info;
    }

    public function errCode($no) {
        $arr = array(
            102104 => '测试应用不允许创建client',
            102105 => '应用不属于该主账号',
            104100 => '主叫clientNumber为空',
            104101 => '主叫clientNumber未绑定手机号',
            103116 => '绑定手机号失败',
            100001 => '余额不足',
            100005 => '访问IP不合法',
            100006 => '手机号不合法',
            100015 => '号码不合法',
            100699 => '系统内部错误',
        );
        return isset($arr[$no]) ? $arr[$no] : '';
    }

    private function _log($args) {
        if (ENV == 'develop' || ENV == 'test') {

            $path = '/data/3g_log/tel_ucpaas/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $logFile = $path . date('Ymd');
            $logText = date('Y-m-d H:i:s') . ' ' . $args . "\n";
            error_log($logText, 3, $logFile);
        }
    }

}


?>