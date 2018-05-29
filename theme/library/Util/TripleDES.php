<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Util_TripleDES {
    var $App_Id = '';
    var $App_key = '';
    var $App_Name = 'theme';
    var $Assoc_Type = 'gionee';
    var $App_Host = 'id.amigo.cn';
    var $App_Port_psecret = '';
    var $App_Uri = '/account/psecret.do';
    var $APP_Uri_verify = '/oauth/access_token';
    var $APP_verifydo = '/account/ass/verify.do';
    var $App_Timestamp = '';
    var $Method = 'POST';
    var $App_Nonce = '';
    var $App_Mac = '';
    var $App_Mod = '0';//0为测试模式，1为正式环境
    var $mobile = 0;

    function __construct() {
        $this->App_Id = Common::getConfig("appConfig", "appid");
        $this->App_key = Common::getConfig("appConfig", "appkey");
        $this->App_Mod = (ENV == 'product') ? 1 : 0;
        $this->App_Port_psecret = (ENV == 'product') ? '443' : '6443';
 
        $this->App_Timestamp = time();
    	$this->mobile = $this->ami_checkmobile();
    }
    
    public function genIvParameter() {
        return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_TRIPLEDES,MCRYPT_MODE_CBC), MCRYPT_RAND);
    }

    private function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize); // in php, strlen returns the bytes of $text
        return $text . str_repeat(chr($pad), $pad);
    }
    private function pkcs5Unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    public function encryptText($plain_text, $key, $iv) {
        $padded = Util_TripleDES::pkcs5Pad($plain_text, mcrypt_get_block_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC));
        return mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $padded, MCRYPT_MODE_CBC, $iv);
    }

    public function decryptText($cipher_text, $key, $iv) {
        $plain_text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $cipher_text, MCRYPT_MODE_CBC, $iv);
        return Util_TripleDES::pkcs5Unpad($plain_text);
    }
    public function base64url_decode($base64url) {
        $base64 = strtr($base64url, '-_', '+/');
        $plainText = base64_decode($base64);
        return $plainText;
    }
    public function base64url_encode($str) {
        $plainText = base64_encode($str);
        $plainText = strtr($plainText, '+/', '-_');
        return $plainText;
    }
    public function decode($secretKey64, $ciphertext) {
        if(!$secretKey64 || !$ciphertext) {
            return '';
        }
        $cipherBytes = $this->base64url_decode($ciphertext);
        $keyIVPair = $this->takeKeyIv($secretKey64);
        return $this->decryptText($cipherBytes, $keyIVPair['key'], $keyIVPair['iv']);
    }

    public function getChrs($data) {
        if(!is_array($data)) {
            if($data <= 128) {
                $data = $data + 256;
            }
            return chr($data);
        } else {
            $str = '';
            foreach($data as $value) {
                $str .= $this->getChrs($value);
            }
            return $str;
        }
    }
    public function getBytes($str) {
        $len = strlen($str);
        $bytes = array();
           for($i=0;$i<$len;$i++) {
               if(ord($str[$i]) >= 128){
                   $byte = ord($str[$i]) - 256;
               }else{
                   $byte = ord($str[$i]);
               }
            $bytes[] =  $byte ;
        }
        return $bytes;
    }
    public function takeKeyIv($secretKey64) {
        $bytes = $this->getBytes($this->base64url_decode($secretKey64));
        $array = array();
        $key = $iv = array();
        for($i = 0; $i < 24; $i++) {
            $array['key'][] = $bytes[$i];
        }
        for($i = 24; $i < 32; $i++) {
            $array['iv'][] = $bytes[$i];
        }
        $array['key'] = $this->getChrs($array['key']);
        $array['iv'] = $this->getChrs($array['iv']);

        return $array;

    }
    //add get key
    private function get_key() {
        $this->App_Mac = $this->oauth_hmacsha1();
        $url = 'https://'.($this->App_Mod ? '' : 't-').$this->App_Host.($this->App_Port_psecret && !$this->App_Mod ? ':'.$this->App_Port_psecret : '').$this->App_Uri;

        $headerstr = 'MAC id="'.$this->App_Id.'",';
        $headerstr .= 'ts="'.$this->App_Timestamp.'",';
        $headerstr .= 'nonce="'.$this->App_Nonce.'",';
        $headerstr .= 'mac="'.$this->App_Mac.'"';

        $header[]= 'Authorization:'.$headerstr;
        $header[] = 'Content-Type:application/x-www-form-urlencoded';
        $r = $this->vpost($url, http_build_query($data), $header);
        return $this->check_return($r);
    }

    public function check_cookie($cookie) {
        global $_G;
        if(!$cookie['ACC'] || !$cookie['VAL']) {
            return false;
        }
        $data = $this->get_key();
        if (empty($data)) {
        	return false;
        }

        $plaintext = $this->decode($data['sek'], $cookie['ACC']);
        //echo $plaintext;exit;
        $hashBytes = sha1($plaintext.$data['shs'], 1);
        $curHashText = $this->base64url_encode($hashBytes);
        //Common::v($plaintext);
        if($cookie['VAL'] === $curHashText) { 
            return json_decode($plaintext, 1);
        } else {
            $var = 'ACC';
            $var1 = 'VAL';
            if($this->mobile) {
                $httponly = false;
            }
            $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
            $life = $life > 0 ? $this->App_Timestamp + $life : ($life < 0 ? $this->App_Timestamp - 31536000 : 0);
            $path = $httponly && PHP_VERSION < '5.2.0' ? '/; HttpOnly' : '/';

            if(PHP_VERSION < '5.2.0') {
                setcookie($var, '', $life, $path, null, $secure);
                setcookie($var1, '', $life, $path, null, $secure);
            } else {
                setcookie($var, '', $life, $path, null, $secure, $httponly);
                setcookie($var1, '', $life, $path, null, $secure, $httponly);
            }
            return false;
        }
        exit;
    }
    //add get key end
    public function check_return($r) {
    	global $_G;
        $data = json_decode($r, 1);
        if(empty($data['sek'])) {
        	if ($_G['ami_fromapp'] == 'game') {
        		return;
        	} else {
	            //showmessage('Server error:'.$data['wid'].'<br>Code:'.$data['r']);
                echo 'Server error:'.$data['wid'].'<br>Code:'.$data['r'];
        	}
        } else {
            return $data;
        }
    }
    //get mac name
    public function oauth_hmacsha1() {
        $this->App_Nonce = $this->make_randstr();
        $text = $this->App_Timestamp."\n".$this->App_Nonce."\n".$this->Method."\n".$this->App_Uri."\n".(!$this->App_Mod ? 't-' : '').$this->App_Host."\n".$this->App_Port_psecret."\n\n";
        return base64_encode($this->hmacsha1($this->App_key, $text));  
    }

    public function hmacsha1($key, $data) {  
        $blocksize=64;
        $hashfunc='sha1';
        if (strlen($key)>$blocksize)
            $key=pack('H*', $hashfunc($key));
            $key=str_pad($key,$blocksize,chr(0x00));
            $ipad=str_repeat(chr(0x36),$blocksize);
            $opad=str_repeat(chr(0x5c),$blocksize);
            $hmac = pack(
                'H*',$hashfunc(
                    ($key^$opad).pack(
                        'H*',$hashfunc(
                            ($key^$ipad).$data
                        )
                    )
                )
            );
        return $hmac;
    }
    public function vpost($url, $data, $header = array()){ // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
           echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
    public function make_randstr($length = 16) {   // 密码字符集，可任意添加你需要的字符
        $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z', '0','1','2','3','4','5','6','7','8','9');   // 在 $chars 中随机取 $length 个数组元素键名  
        $keys = array_rand(array_keys($chars), $length);
        $password = '';
        for($i = 0; $i < $length; $i++) {// 将 $length 个数组元素连接成字符串
            $password .= $chars[$keys[$i]];
        }
        return $password;
    } 

    //get mac name end

    public function ami_checkmobile() {
    	$mobile = array();
    	static $touchbrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
    			'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
    			'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
    			'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
    			'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
    			'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
    			'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
    	static $mobilebrowser_list =array('windows phone');
    	static $wmlbrowser_list = array('cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom',
    			'pantech', 'dopod', 'philips', 'haier', 'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
    			'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');
    
    	$pad_list = array('pad', 'gt-p1000');
    
    	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    
    	if($this->dstrpos($useragent, $pad_list)) {
    		return false;
    	}
    	if(($v = $this->dstrpos($useragent, $mobilebrowser_list, true))){
    		return '1';
    	}
    	if(($v = $this->dstrpos($useragent, $touchbrowser_list, true))){
    		return '2';
    	}
    	if(($v = $this->dstrpos($useragent, $wmlbrowser_list))) {
    		return '3'; //wml版
    	}
    	$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
    	if($this->dstrpos($useragent, $brower)) return false;
    
    	return false;
    }

    /** 
     * 字符串方式实现 preg_match("/(s1|s2|s3)/", $string, $match) 
     * @param string $string 源字符串 
     * @param array $arr 要查找的字符串 如array('s1', 's2', 's3') 
     * @param bool $returnvalue 是否返回找到的值 
     * @return bool 
     */  
    function dstrpos($string, &$arr, $returnvalue = false) {  
        if(empty($string)) return false;  
        foreach((array)$arr as $v) {  
            if(strpos($string, $v) !== false) {  
                $return = $returnvalue ? $v : true;  
                return $return;  
            }  
        }  
        return false;  
    }

    function get_token($code){
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        $data = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $adminroot.'/Admin/Index/index',
        );

        $this->App_Nonce = $this->make_randstr();
        $text = $this->App_Timestamp."\n".$this->App_Nonce."\n".$this->Method."\n".$this->APP_Uri_verify."\n".(!$this->App_Mod ? 't-' : '').$this->App_Host."\n".$this->App_Port_psecret."\n\n";

        $this->App_Mac = base64_encode($this->hmacsha1($this->App_key, $text));
        
        //$url = 'http://t-id.amigo.cn/oauth/access_token';
        $url = 'https://'.($this->App_Mod ? '' : 't-').$this->App_Host.($this->App_Port_psecret && !$this->App_Mod ? ':'.$this->App_Port_psecret : '').$this->APP_Uri_verify;

        $headerstr = 'MAC id="'.$this->App_Id.'",';
        $headerstr .= 'ts="'.$this->App_Timestamp.'",';
        $headerstr .= 'nonce="'.$this->App_Nonce.'",';
        $headerstr .= 'mac="'.$this->App_Mac.'"';

        $header[]= 'Authorization:'.$headerstr;
        $header[] = 'Content-Type:application/x-www-form-urlencoded';
        $r = $this->vpost($url, http_build_query($data), $header);

        return $r;
    }

    function verify_do($r){
        $this->APP_Uri_verify = '/account/verify.do';
        $this->App_Nonce = $this->make_randstr();
        $text = $this->App_Timestamp."\n".$this->App_Nonce."\n".$this->Method."\n".$this->APP_Uri_verify."\n".(!$this->App_Mod ? 't-' : '').$this->App_Host."\n".$this->App_Port_psecret."\n\n";

        $this->App_Mac = base64_encode($this->hmacsha1($this->App_key, $text));
        
        $url = 'https://'.($this->App_Mod ? '' : 't-').$this->App_Host.($this->App_Port_psecret && !$this->App_Mod ? ':'.$this->App_Port_psecret : '').$this->APP_Uri_verify;

        $headerstr = 'MAC id="'.$this->App_Id.'",';
        $headerstr .= 'ts="'.$this->App_Timestamp.'",';
        $headerstr .= 'nonce="'.$this->App_Nonce.'",';
        $headerstr .= 'mac="'.$this->App_Mac.'"';

        $header[]= 'Authorization:'.$headerstr;
        $header[] = 'Content-Type: application/json';
        $r = $this->vpost($url, $r, $header);

        return json_decode($r, true);
    }
}
?>
