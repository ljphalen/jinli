<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AuthController extends Front_BaseController {
	
	public $perpage = 20;
	
	public $actions = array(
			'tokenUrl'=>'/auth/auth/token',
			'ptnerUrl'=>'/auth/auth/ptner',
			'finishUrl'=>'/auth/auth/finish',
			'smsUrl'=>'/auth/auth/sms',
            'statUrl'=>'/api/wifi/stat'
	);
	

	public function indexAction() {
        //
		$params = $this->getInput(array('ht', 'uht', 'refer'));
		if (!$params['ht']) $this->output(-1, 'with error ht.');
		 
		$device = Wifi_Service_Device::getBy(array('ht'=>$params['ht']));
	
		if (!$device['uid']) exit('ht is not exists.');
		$ptner = Wifi_Service_Ptner::get($device['uid']);

		$user = Wifi_Service_User::getBy(array('uht'=>$params['uht']));

        $cache = Common::getCache();
        $pkey = $params['uht']."-PL";
        $this->assign('phone', $cache->get($pkey));

        $udata =json_decode($ptner['data'], true);
        $this->assign('udata', $udata);
		$this->assign('user', $user);
		$this->assign('params', $params);
		$this->assign('ptner', $ptner);		 
	}

    public function statAction() {

    }
	
	public function ptnerAction() {
		$news = file_get_contents("http://api.k.sohu.com/api/open/channel/newsList.go?channelId=1&page=1%20&picScale=3&showContent=0");
		$news = json_decode($news, true);
		$this->assign("news", $news['articles']);
	}
	
	public function finishAction() {
		$s = $this->getInput('s');
		$pstr = Common::encrypt($s, 'DECODE');
		list($phone, $ht, $uht) = explode(":", $pstr);

        $user = Wifi_Service_User::getBy(array('uht'=>$uht, 'ht'=>$ht));
        if ($user) {
            Wifi_Service_User::update(array('hits'=>$user['hits']+1, 'last_visist'=>Common::getTime()), $user['id']);
        } else {
            if ($phone) {
                $d = Wifi_Service_Device::getBy(array('ht'=>$ht));
                Wifi_Service_User::add(array(
                    'ht'=>$ht,
                    'hits'=>1,
                    'last_visist'=>Common::getTime(),
                    'ptner_id'=>$d['uid'],
                    'phone'=>$phone,
                    'uht'=>$uht,
                ));
            }
        }
	}

	public function smsAction() {
		$phone = $this->getInput("phone");
        $uht = $this->getInput("uht");

		if (!$phone) $this->output(-1,"手机号码不能为空.");
		if (!preg_match('/^1[3458]\d{9}$/', $phone)) $this->output(-1, "手机号码格式不正确");
		
		$cache = Common::getCache();
		
		$skey = $phone."-SL";
		$code = $cache->get($skey);
		if ($code) {
			$this->output(-1, "短信已经发送,3分钟内有效,请不要重复发送.");
		}
		$rcode = mt_rand(100000, 999999);
		$ret = Common::sms($phone, "欢迎使用HotWifi,手机一次认证,永久有效,以后您在HotWifi范围内都无需认证,直接上网,您的认证码:".$rcode);
		
		if ($ret->state !== 200) $this->output(-1, "验证码发送失败.");

		$cache->set($skey, $rcode, 120);
        $cache->set($uht."-PL", $phone, 120);
		$this->output(0, '短信已经发送,3分钟内有效,请注意查收.');
	}
    
    public function tokenAction() {
    	$params = $this->getInput(array('ht', 'uht', 'refer'));
    	$user = Wifi_Service_User::getBy(array('uht'=>$params['uht']));
    	
    	if (!$user) {
    		$device = Wifi_Service_Device::getBy(array('ht'=>$params['ht']));
			$ptner = Wifi_Service_Ptner::get($device['uid']);

    		if ($ptner['login_mode'] == 2) {
				$params['passwd'] = $this->getInput('passwd');
				if ($params['passwd'] != $ptner['login_passwd']) $this->output(-1, '认证密码不正确.');
    		} else if ($ptner['login_mode'] == 3) {
    			$params['phone'] = $this->getInput('phone');
    			$params['code'] = $this->getInput('code');
				if (!preg_match('/^1[3458]\d{9}$/', $params['phone'])) $this->output(-1, "手机号码格式不正确");
		    
		    	$cache = Common::getCache();
		    	
		    	$skey = $params['phone']."-SL";
		    	$code = $cache->get($skey);
		    	if ($params['code'] != $code) {
		    		$this->output(-1, '验证码不正确.');
		    	}
    		} else if ($ptner['login_mode'] == 4) {
                $params['passwd'] = $this->getInput('passwd');
                if ($params['passwd'] != $ptner['weixin_passwd']) $this->output(-1, '认证密码不正确.');
            }
    	}
    	
    	$token = self::_genToken();
    	$s = urlencode(Common::encrypt(sprintf("%s:%s:%s", $params['phone'], $params['ht'], $params['uht'])));
    	$url = sprintf("%s%s?s=%s", $this->webroot, $this->actions['finishUrl'], $s);
    	$this->output(0,'', array('token'=>$token, 'url'=>$url));
    }
    
    private static function _genToken() {
    	$token_time = time();
    	$token_livetime = 180;
    	$slat_string = "rainkide@gmail.com";
    	$token = md5(intval($token_time/$token_livetime) . $slat_string);
    	return $token;
    }
}
