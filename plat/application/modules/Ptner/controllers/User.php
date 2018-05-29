<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UserController extends Ptner_BaseController {
	
	public $actions = array(
			'loginUrl'=>'/ptner/user/login',
			'registerUrl'=>'/ptner/user/register',
			'registerPost1Url'=>'/ptner/user/register_post_step1',
			'registerPost2Url'=>'/ptner/user/register_post_step2',
			'loginPostUrl'=>'/ptner/user/login_post',
			'settingPostUrl'=>'/ptner/user/setting_post',
			'addUrl'=>'/ptner/user/ad',
			'regionUrl'=>'/ptner/user/region',
			'manageUrl'=>'/ptner/user/manage',
            'managePostUrl'=>'/ptner/user/manage_post',
            'tjUrl'=>'/ptner/user/tj',
			'logoutUrl'=>'/ptner/user/logout',
            'smsUrl'=>'/ptner/user/sms',
            'tjUrl'=>'/ptner/user/tj',
            'previewUrl'=>'/ptner/user/preview',
            'previewPostUrl'=>'/ptner/user/preview_post',
            'authUrl'=>'/auth',
		);
	public function loginAction() {
	}
	
    public function indexAction() {
    }

    public function previewAction() {
        $cache = Common::getCache();
        $prekey = $this->user."PRE";
        $udata = $cache->get($prekey);
        $this->assign('udata', $udata);

        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));
        $this->assign('ptner', $ptner);

        $this->display("../../../Auth/views/auth/index");
        exit;
    }

    public function preview_postAction() {
        $info = $this->getInput(array('title', 'theme', 'logo', 'baner'));
        $cache = Common::getCache();
        $prekey = $this->user."PRE";
        $ret = $cache->set($prekey, $info);
        if (!$ret) $this->output(-1, '预览失败.');
        $this->output(0, '');
    }
    
    public function tjAction() {
        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));
        $device = Wifi_Service_Device::getBy(array('uid'=>$ptner['id']));

        $edate = date('Y-m-d');
        $sdate = date('Y-m-d', strtotime("-30 days"));

        $this->assign('device', $device);
        $rs = Wifi_Service_Stat::getPvList($sdate, $edate, array('ht'=>$device['ht']));
        $this->assign('rs', $rs);


        list($total, $users) = Wifi_Service_User::getList(0, 20, array('ht'=>$device['ht']));
        $this->assign('users', $users);
    }
    
    public function manageAction() {
    	$ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));
        $data = json_decode($ptner['data'], true);
        $this->assign('data', $data);
    	$this->assign('ptner', $ptner);

        $device = Wifi_Service_Device::getBy(array('uid'=>$ptner['id']));
        $this->assign('device', $device);
    }

    public function manage_postAction() {
        $info = $this->getInput(array('title', 'logo', 'baner', 'theme'));
        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));
	    $ret = Wifi_Service_Ptner::update(array('data'=>json_encode($info)), $ptner['id']);
    	if(!$ret) $this->output(-1, '更新失败');
        $this->output(0, '更新成功.');
    }
    
    public function settingAction() {
        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));
        $device = Wifi_Service_Device::getBy(array('uid'=>$ptner['id']));
        $this->assign('device', $device);
        $this->assign('ptner', $ptner);
    }

    public function smsAction() {
        $phone = $this->getInput("phone");
        if (!$phone) $this->output(-1,"手机号码不能为空.");
        if (!preg_match('/^1[3458]\d{9}$/', $phone)) $this->output(-1, "手机号码格式不正确");

        $cache = Common::getCache();

        $skey = $phone."-SC";
        $code = $cache->get($skey);
        if ($code) {
            $this->output(-1, "短信已经发送,3分钟内有效,请不要重复发送.");
        }
        $rcode = mt_rand(100000, 999999);
        $ret = Common::sms($phone, "商户注册,请输入验证码".$rcode."完成注册");

        if ($ret->state !== 200) $this->output(-1, "验证码发送失败.");
        $cache->set($skey, $rcode, 120);
        $this->output(0, '短信已经发送,3分钟内有效,请注意查收.');
    }



    public function routerAction() {
    	$ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));
    	$rs = Wifi_Service_Device::getsBy(array('uid'=>$ptner['id']));
    	$this->assign('rs', $rs);
    }
    
    public function setting_PostAction() {
    	$infos = $this->getInput(array('hs_enable', 'hs_timeout'));
    	if ($infos['hs_enable'] && $infos['hs_enable'] == 'on') {
    		$infos['hs_enable'] = 1;
    	} else {
    		$infos['hs_enable'] = 0;
    	}
    	$ptner = Wifi_Service_Ptner::getBy(array('phone'=>$this->user));

    	$ret = Wifi_Service_Device::updateBy($infos, array('uid'=>$ptner['id']));
    	if (!$ret) $this->output('-1', '操作失败', array());

        $data = $this->getInput(array('login_mode', 'login_passwd', 'weixin_name', 'weixin_passwd'));
        $ret = Wifi_Service_Ptner::update($data, $ptner['id']);
        if (!$ret) $this->output('-1', '操作失败', array());

    	$this->output(0, '操作成功', array());
    }
    
    public function uploadAction() {
    	$ret = Common::upload('img', 'pics');
    	$type = $this->getInput("type");
    	if ($ret['code'] != 0) $this->output(-1, "上传失败.");
    	

    	$this->output(0, "success", $ret['data']);
    }
    
    public function registerAction() {
    	$stype = Common::getConfig('stypeConfig');
    	$province = Wifi_Service_Region::getsBy(array('parent_id'=>1));
    	$this->assign('province', $province);
    	$this->assign('stype', $stype);
    }
    
    public function regionAction() {
    	$id = intval($this->getInput('id'));
    	$region = Wifi_Service_Region::getsBy(array('parent_id'=>$id));
    	$this->output(0, '', $region);
    }
    
    /**
     * 注册 
     */
    public function register_post_step1Action() {
    	$info = $this->getInput(array('username', 'phone', 'passwd', 'repasswd', 'code'));
		if (!$info['username']) $this->output(-1, '用户名不能为空');
    	if (!$info['passwd']) $this->output(-1, '密码不能为空.');
		if (!$info['phone']) $this->output(-1, '手机号不能为空');


        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$info['phone']));
        if ($ptner) $this->output(-1, '用户已经存在,请直接登录.', array());

        /*$cache = Common::getCache();
        $scode = $cache->get($info['phone']."-SC");
        if ($scode != $info['code']) $this->output(-1, "验证码不正确.");
		
    	if(md5($info['passwd']) != md5($info['repasswd'])) {
			$this->output(-1, '重复密码不正确.', array('repasswd'));
		}*/

    	$ret = Wifi_Service_Ptner::add($info);
    	if (!$ret) $this->output(-1, '注册失败，请重试.', array());
    	$this->output(0, '注册成功.', array('sign'=>Common::encrypt($ret)));
    }
    
    /**
     * 注册
     */
    public function register_post_step2Action() {
    	$info = $this->getInput(array('sign', 's_name', 's_type', 'province_id', 'city_id', 'area_id', 's_detail'));
    	if (!$info['sign']) $this->output(-1, '非法请求');
    	if (!$info['s_name']) $this->output(-1, '商铺名称不能为空');
    	if (!$info['s_type']) $this->output(-1, '商户类型不能为空.');
    	if (!$info['province_id'] || !$info['city_id'] || !$info['area_id']) $this->output(-1, '所属商圈不能为空');
    	if (!$info['s_detail']) $this->output(-1, '详细地址不能为空');

    	$id = Common::encrypt(html_entity_decode($info['sign']), 'DECODE');
    	$ptner = Wifi_Service_Ptner::get($id);
    	if (!$ptner) $this->output(-1, '用户不存在,请重新注册.', array());
    	$ret = Wifi_Service_Ptner::update(array(
    				's_name'=>$info['s_name'],
    				's_type'=>$info['s_type'],
    				's_address'=>sprintf("%d,%d,%d", $info['province_id'], $info['city_id'], $info['area_id']),
    				's_detail'=>$info['s_detail']
    			), $ptner['id']);
    	if (!$ret) $this->output(-1, '资料更新失败，请重试.', array());
    	$this->output(0,'注册成功,请登录.', array());
    }
    
    /**
     * 登录
     */
    public function login_postAction() {
    	$info = $this->getInput(array('username', 'passwd'));
    	if (!$info['username']) $this->output(-1, '用户名不能为空.');
    	if (!$info['passwd']) $this->output(-1, '登录密码不能为空.');
    	
    	$ptner = Wifi_Service_Ptner::getBy(array('phone'=>$info['username']));
    	if (!$ptner) {
	    	$ptner = Wifi_Service_Ptner::getBy(array('username'=>$info['username']));
    	}
    	
    	if (!$ptner) $this->output(-1, '帐号不存在，请先注册.', array());
    	if (!$ptner['status']) $this->output(-1, '帐号未审核通过，请联系管理员进行审核.', array());
    	
    	if (md5($info['passwd']) != $ptner['passwd']) {
    		$this->output(-1, '用户或者密码不正确.', array());
    	}
    	$cache = Common::getCache();
    	$cache->set(session_id(), $ptner['phone']);
    	$this->output(0, '登录成功.', array());
    }
    
    public function logoutAction() {
    	$cache = Common::getCache();
    	$cache->delete(session_id());
    	$this->redirect('/ptner/user/login');
    }
}
