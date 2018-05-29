<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Api_Gionee_Account {

    //获取账号的配置文件信息
    public static function getConfig() {
        return Common::getConfig('apiConfig', 'oauth');
    }

    public static function getUrl($val) {
        return sprintf('https://%s:%s%s', self::getHost(), self::getPort(), $val);
    }

    //获取服务器时间
    public static function getServiceTime() {
        $url     = self::getUrl('/time.do');
        $request = Util_Http::post($url, array('Content-Type' => 'application/json'));
        return $request->headers;
    }

    //生成APP-TOKEN
    public static function getAppToken() {
        $config = Api_Gionee_Account::getConfig();
        $appKey = $config['gionee_user_appkey'];
        $appId  = $config['gionee_user_appid'];
        $string = self::_getShuttleString();
        $url    = self::getUrl('/api/token/getapptoken.do');
        $mac    = self::_getMacSignature($appKey, $string);
        $data   = json_encode(array('id' => $appId, 'ts' => time(), 'nonce' => $string, 'mac' => $mac));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    //获取图形验证码
    public static function getImageCode() {
        $url    = self::getUrl('/account/refreshgvc.do');
        $data   = json_encode(array('vty' => 'vtext'));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * 图形码验证
     *
     * @param string tn  电话号码
     * @param string vid 图形码状态
     */
    public static function registerByGvc($tn, $vid, $vtx, $vty = 'vtext') {
        $url    = self::getUrl('/api/gsp/register_by_gvc.do');
        $data   = json_encode(array('tn' => $tn, 'vid' => $vid, 'vtx' => $vtx, 'vty' => $vty));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * 获取短信验证码
     */
    public static function getSmsVerifyCode($mobile, $imgCode) {
        $url    = self::getUrl('/api/gsp/get_sms_for_register.do');
        $data   = json_encode(array('tn' => $mobile, 's' => $imgCode));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * 短信注册
     */
    public static function registerBySms($mobile, $verifyCode, $imgCode) {
        $url    = self::getDomain('api/gsp/register_by_smscode.do');
        $data   = json_encode(array('tn' => $mobile, 'sc' => $verifyCode, 's' => $imgCode));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * 设置密码
     *
     * @param string $pwd  要设置的密码
     * @param string $code 短信验证码
     */

    public static function setLoginPassword($pwd, $code) {
        $url = self::getDomain('api/gsp/register_by_pass.do');
        //增加注册接口中使用的appid
        $config = self::getConfig();
        $a      = $config['gionee_user_appid'];
        $data   = json_encode(array('s' => $code, 'p' => $pwd, 'a' => a));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * 生成MAC签名
     *
     * @param string  $host   主域名
     * @param int     $port   端口
     * @param string  $url    链接
     * @param string  $appkey MACKey
     * @param unknown $string 随机字符串
     * @param unknown $time   时间
     * @param string  $method 提交方式
     */
    private static function _getMacSignature($appKey, $string) {
        $time      = time();
        $method    = 'POST';
        $url       = '/api/token/getapptoken.do';
        $host      = self::getHost();
        $port      = self::getPort();
        $array     = array($time, $string, $method, $url, $host, $port);
        $macString = implode("\n", $array) . "\n\n";
        $uft8Str   = mb_convert_encoding($macString, 'UTF-8');

        $hmacStr = base64_encode(hash_hmac('sha1', $uft8Str, $appKey));
        return urlencode($hmacStr);
    }

    private static function _getShuttleString($len = 8) {
        $character = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghigklmnopqrstuvwxyz0123456789";
        $chaos     = str_shuffle($character);
        return substr($chaos, 0, $len);
    }

    /**
     * Gionee 密码加密算法(帐号系统)
     *
     * @param string $p
     *
     * @return string
     */
    public static function encode_password($p) {
        return strtoupper(sha1($p));
    }

    /**
     * Gionee 接口host
     * @return string
     */
    public static function getHost() {
        return stristr(ENV, 'product') ? 'id.gionee.com' : 't-id.gionee.com';
    }

    /**
     * Gionee 接口port
     * @return string
     */
    public static function getPort() {
        return stristr(ENV, 'product') ? '443' : '6443';
    }


    /**
     * 刷新/获取验证码
     * @return vda,vid
     */
    public static function refreshGVC() {
        $url    = self::getUrl('/account/refreshgvc.do');
        $data   = json_encode(array('vty' => 'vtext'));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 获取短信(注册)
     *
     * @param string $tn
     * @param string $s
     *
     * @return array
     */
    public static function getSmsForRegister($tn, $s) {
        $url    = self::getUrl('/api/gsp/get_sms_for_register.do');
        $data   = json_encode(array('tn' => $tn, 's' => $s));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }


    /**
     * Gionee 验证短信(注册)
     *
     * @param string $tn
     * @param string $sc
     *
     * @return array
     */
    public static function registerBySmsCode($tn, $sc, $s) {
        $url    = self::getUrl('/api/gsp/register_by_smscode.do');
        $data   = json_encode(array('tn' => $tn, 'sc' => $sc, 's' => $s));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 设置密码(注册)
     *
     * @param string $s 验证短信 session
     * @param string $p 设置的密码
     *
     * @return array
     */
    public static function registerByPass($s, $p) {
        $url = self::getUrl('/api/gsp/register_by_pass.do');
        //增加注册接口中使用的appid
        $config = Api_Gionee_Account::getConfig();
        $a      = $config['gionee_user_appid'];
        $data   = json_encode(array('s' => $s, 'p' => $p, 'a' => $a));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 身份认证(普通登录)
     *
     * @param string $tn
     * @param string $p
     * @param string $vid
     * @param string $vtx
     * @param string $vty
     */
    public static function auth($tn, $p, $vid, $vtx, $vty = 'vtext') {
        $url = self::getUrl('/api/gsp/auth_for_player.do');
        //增加注册接口中使用的appid
        $config = Api_Gionee_Account::getConfig();
        $a      = $config['gionee_user_appid'];
        $data   = array('a' => $a);
        $auth   = "Basic " . base64_encode($tn . ':' . $p);
        if ($vid && $vtx && $vty) $data = array('a' => $a, 'vid' => $vid, 'vtx' => $vtx, 'vty' => $vty);
        $data   = json_encode($data);
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization' => $auth));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 身份认证(mac签名自动登录) 1.5.0 使用
     *
     * @param string $uuid
     * @param string $ts
     * @param string $noce
     * @param string $mac
     *
     */
    public static function auth2($uuid, $ts, $nonce, $mac) {
        $url    = self::getUrl('/account/auth.do');
        $auth   = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $uuid, $ts, $nonce, $mac);
        $result = Util_Http::post2($url, '', array('Content-Type' => 'application/json', 'Authorization' => $auth));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 身份认证(mac签名自动登录)1.5.1 开始使用
     * 1.5.1 增加 appid
     *
     * @param string $uuid
     * @param string $ts
     * @param string $noce
     * @param string $mac
     *
     */
    public static function auth3($uuid, $ts, $nonce, $mac) {
        $url = self::getUrl('/api/gsp/autologon_for_player.do');
        //增加注册接口中使用的appid
        $config = Api_Gionee_Account::getConfig();
        $a      = $config['gionee_user_appid'];
        $auth   = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $uuid, $ts, $nonce, $mac);
        $data   = json_encode(array('a' => $a));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization' => $auth));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 帐号修改密码
     *
     * @param string $tn
     * @param string $p
     * @param string $newp
     * @param string $vid
     * @param string $vtx
     * @param string $vty
     */
    public static function passmodify($tn, $p, $newp, $vid, $vtx, $vty = 'vtext') {
        $url  = self::getUrl('/account/pass/modify.do');
        $data = array('p' => $newp);
        $auth = "Basic " . base64_encode($tn . ':' . $p);
        if ($vid && $vtx && $vty) $data = array_merge($data, array('vid' => $vid, 'vtx' => $vtx, 'vty' => $vty));
        $data   = json_encode($data);
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization' => $auth));
        return json_decode($result->data, true);
    }


    /**
     * Gionee 用于请求服务器获取第三方联合登录凭证
     *
     * @param string $a    第三方应用appid
     * @param string $uuid 金立账号中心用户唯一标识
     * @param string $pk   金立账号中心用户passkey
     */
    public static function assoc($a, $uuid, $pk) {
        $path   = '/account/ass/assoc.do';
        $url    = self::getUrl($path);
        $ts     = time();
        $host   = self::getHost();
        $port   = self::getPort();
        $nonce  = self::make_nonce();
        $text   = $ts . "\n" . $nonce . "\nPOST\n" . $path . "\n" . $host . "\n" . $port . "\n\n";
        $mac    = self::hmacsha1($pk, $text);
        $auth   = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $uuid, $ts, $nonce, $mac);
        $data   = json_encode(array('a' => $a));
        $result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization' => $auth));
        return json_decode($result->data, true);
    }

    /**
     * Gionee 生成账号mac签名数据
     *
     * @param string $text    待加密的字符串
     *                        $ts."\n".$nonce."\nPOST\n".$url."\n".$host."\n".$port."\n\n";
     * @param string $pk      passkey 字符串;
     */
    public static function generateMac($pk, $text) {
        return base64_encode(self::hmacsha1($pk, $text));
    }

    /**
     * Gionee 生成passkey
     *
     * @param string $uuid
     * @param string $passPlain 加过密的密码
     *
     * @return string
     */
    public static function createPasskey($uuid, $passPlain) {
        $passKey = self::encode_password($uuid . ':' . $passPlain);
        return $passKey;
    }

    /**
     * Gionee Mac加密算法
     *
     * @param string $key
     * @param string $data
     *
     * @return string
     */
    public static function hmacsha1($key, $data) {
        $blocksize = 64;
        $hashfunc  = 'sha1';
        if (strlen($key) > $blocksize) $key = pack('H*', $hashfunc($key));
        $key  = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        return base64_encode($hmac);
    }

    /**
     * noce 随机字符串默认长度为9位
     *
     * @param number $length
     *
     * @return string
     */
    private static function make_nonce($length = 9) {
        $str = md5(uniqid(mt_rand(), true));
        return substr($str, 0, 8);
    }
}