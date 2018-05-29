<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class SettingController extends User_BaseController {
	
	public $actions = array(
		'indexUrl' => '/user/setting/index',
		'editextendUrl' => '/user/setting/edit',
		'editPostUrl' => '/user/setting/edit_post',
	);
	
	public $sex = array(
			1 => '女',
			2 => '男'
	);
	
	public $tmp = array(
				'job'=> array(
						0 => '请选择',
						1 => '销售/采购',
						2 => 'IT/通讯',
						3 => '房地产/建筑',
						4 => '保险/金融',
						5 => '销售/采购',
						6 => '管理/人事行政',
						7 => '设计/媒体',
						8 => '生产/物流',
						9 => '机关单位/公务员',
						10 => '其他'
				),
				'love'=> array(
						0 => '请选择',
						1 => '游戏',
						2 => '购物',
						3 => '旅游',
						4 => '健身',
						5 => '美食',
						6 => '电子产品',
						7 => '电影',
						8 => '美妆',
						9 => '养生',
						10 => '宅',
						11 => '看书',
						12 => '写作',
						13 => '其他'
				),
				'age'=> array(
						0 => '请选择',
						1 => '18岁以下',
						2 => '19—25岁',
						3 => '26—35岁',
						4 => '36—45岁',
						5 => '46—55岁',
						6 => '56岁以上'
				)
		);
	
	public $perpage = 10;
	
	public function indexAction() {
			
		$user_info = $this->userInfo;
		list($year, $month, $day) = explode('-', $user_info['birthday']);
		
		$extend = Gou_Service_UserExtend::getUserExtendByUserId($user_info['id']);
		$this->assign('year', $year);
		$this->assign('month', $month);
		$this->assign('day', $day);
		$this->assign('user_info',$this->userInfo);
		$this->assign('extend', $extend);
		$this->assign('tmp', $this->tmp);
		$this->assign('sex', $this->sex);
		$webroot = Common::getWebRoot();
		$logoutUrl = Api_Gionee_Oauth::getLogoutUrl($webroot.'/user/login/logout');
		$this->assign('logoutUrl', $logoutUrl);
		$this->assign('title', '个人资料');
	}
	
	/**
	 * 编辑基本资料
	 */
	public function editAction() {
		//生日年份
		$year_list = array();
		$month_list = array();
		$day_list = array();
		for ($i = date('Y', Common::getTime()) ; $i >= 1960; $i--){
			$year_list[] = $i;
		}
		for ($i = 1; $i <= 12; $i++){
			if ($i< 10) $i = '0'.$i;
			$month_list[] = $i;
		}
		for ($i = 1; $i <= 31; $i++){
			if ($i< 10) $i = '0'.$i;
			$day_list[] = $i;
		}
		
		$user_info = $this->userInfo;
		list($year, $month, $day) = explode('-', $user_info['birthday']);
		
		$extend = Gou_Service_UserExtend::getUserExtendByUserId($user_info['id']);
		
		$this->assign('year_list', $year_list);
		$this->assign('month_list', $month_list);
		$this->assign('day_list', $day_list);
		$this->assign('year', $year);
		$this->assign('month', $month);
		$this->assign('day', $day);
		$this->assign('user_info',$this->userInfo);
		$this->assign('extend', $extend);
		$this->assign('tmp', $this->tmp);
		$this->assign('title', '编辑个人资料');
	}
	
	/**
	 * 编辑基本资料 post
	 */
	public function edit_postAction() {
		$user_info = $this->userInfo;
		$info = $this->getPost(array('realname', 'sex'));
		$extend_info = $this->getPost(array('qq', 'job', 'love'));
		$birthday = $this->getPost(array('year','month','day'));
		$info['birthday']  = $birthday['year'] . '-' . $birthday['month'] . '-' . $birthday['day'];
		$extend_info = $this->_cookData($extend_info);
		$extend = Gou_Service_UserExtend::getUserExtendByUserId($user_info['id']);
		
		$result = Gou_Service_User::updateUser($info, $user_info['id']);
		if (!$result) $this->output(-1, '资料修改失败.');
		
		if($extend) {
			$ret = Gou_Service_UserExtend::updateUserExtend($extend_info, $extend['id']);
			if (!$ret) $this->output(-1, '资料修改失败.');
		} else {
			$extend_info['user_id'] = $user_info['id'];
			
			$ret = Gou_Service_UserExtend::addUserExtend($extend_info);
			if (!$ret) $this->output(-1, '资料修改失败.');
		}
		
		$webroot = Common::getWebRoot();
		$this->output(0, '资料修改成功.', array('type'=>'redirect', 'url'=>$webroot.'/user/setting/index'));
	}
	
	private function _cookData($info) {
		//if(!$info['email']) $this->output(-1, '邮箱不能为空.');
		if(!$info['qq']) $this->output(-1, 'qq号码不能为空.');
		if(!$info['job']) $this->output(-1, '请选择职业.');
		if(!$info['love']) $this->output(-1, '请选择爱好.');
		//if(!$info['age']) $this->output(-1, '请选择年龄段.');
		return $info;
	}
}