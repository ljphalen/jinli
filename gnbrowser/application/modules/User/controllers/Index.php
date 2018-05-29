<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 签到，目前没有会员中心首页，暂以签到为会员中心首页
 * @author tiansh
 *
 */
class IndexController extends User_BaseController {
	
	public $actions = array(
		'signinUrl' => '/user/index/signin',
		'signinPostUrl' => '/user/index/signin_post',
		'lotteryUrl' => '/user/index/lottery',
		'winprizeUrl' => '/user/index/winprize',
		'showprizeUrl' => '/user/index/showprize',
		'winprizePostUrl' => '/user/index/winprize_post',
		'userinfoUrl' => '/user/index/userinfo',
		'userinfoPostUrl' => '/user/index/userinfo_post',
	);
	
	/**
	 * 
	 * 签到
	 */
	public function signinAction() {
		//检测是否登录
		if($this->userInfo) {		
			//是否有签到记录
			$is_full = false;
			$sign = Gionee_Service_Signin::getSigninByUserId($this->userInfo['id']);
			if($sign && $sign['img_id'] != 0) {
				//当前的签到的拼图
				$img_id = $sign['img_id'];
				
				//是否签满
				$img = Gionee_Service_SigninImg::getSigninImg($img_id);
				
				if($sign['number'] == $img['row']*$img['col']) {
					$is_full = true;
				}
				
			} else {
				$sign_imgs = Gionee_Service_SigninImg::getCanUseImg();
				$sign_imgs = Common::resetKey($sign_imgs, 'id');
				if($sign_imgs) $img_id = array_rand($sign_imgs, 1);
			}
			if($img_id) {
				$signin_img = Gionee_Service_SigninImg::getSigninImg($img_id);
			}
			
			//今天是否已签到
			$info = '';
			$info = Gionee_Service_SigninLog::getLogByUidAndTime($this->userInfo['id'], date('Y-m-d',Common::getTime()));
			
			//历史签到记录
			list(,$user_signin) = Gionee_Service_UserSignin::getList(1, 50, array('user_id'=>$this->userInfo['id']));
			
			$imgs = array();
			if($user_signin) {
				$ids = array();
				foreach ($user_signin as $key=>$value) {
					$ids[] = $value['img_id'];
				}
				
				//签到图片
				$imgs = Gionee_Service_SigninImg::getListByIds($ids);
				$imgs = Common::resetKey($imgs, 'id');
			}
			
			$this->assign('is_signin',$info ? true : false);	 		
	 		$this->assign('sign',$sign);
	 		$this->assign('is_full', $is_full);
	 		$this->assign('user_signin', $user_signin);
	 		$this->assign('imgs', $imgs);
		}else {
			//未登录
			$sign_imgs = Gionee_Service_SigninImg::getCanUseImg();
			$sign_imgs = Common::resetKey($sign_imgs, 'id');
			if($sign_imgs) $img_id = array_rand($sign_imgs, 1);
			$signin_img = Gionee_Service_SigninImg::getSigninImg($img_id);
		}
		$this->assign('user_info',$this->userInfo ? $this->userInfo : '');
		$this->assign('signin_img',$signin_img); 		
 		$this->assign('pageTitle','签到');
 		$this->assign('nav','2-4');
 		
	}
	
	/**
	 * 签到post
	 */
	public function signin_postAction() {
		//检测登录
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if(!$this->userInfo) $this->output(-1, '签到需要先登录哦，这样才能记录您的签到情况!',array('isLogin'=>'0', 'loginUrl'=>$webroot.'/user/login/index'));
		$user_info = Gionee_Service_User::getUser($this->userInfo['id']);
		$img_id = $this->getPost('picId');

		//是否有签到记录
		$sign = Gionee_Service_Signin::getSigninByUserId($this->userInfo['id']);
		if($sign['number']) $img_id = $sign['img_id'];
		
		if(!$img_id) $this->output(-1, '参数错误.');
		//签到图片信息
		$img = Gionee_Service_SigninImg::getSigninImg($img_id);
		
		if(!$img) if(!$img_id) $this->output(-1, '参数错误.');
		
		$ret = Gionee_Service_SigninLog::getLogByUidAndTime($this->userInfo['id'], date('Y-m-d',Common::getTime()));
		
		if($ret) $this->output(-1, '您今天已经签到,明天再来签到吧！');
		
		//当前卡的签到次数
		
		if($sign['number'] >= $img['row']*$img['col'])  $this->output(-1, '您已经拼完整张签到拼图，现在可以去兑奖了！');
		
		$number = $sign ? $sign['number'] + 1 : 1;
		$sign_data = array('user_id'=>$this->userInfo['id'], 'img_id'=>$img_id, 'number'=>$number);
		$sign_log_data = array('user_id'=>$this->userInfo['id'], 'img_id'=>$img_id, 'create_time'=>Common::getTime(), 'create_date'=>date('Y-m-d', common::getTime()));
		
		//签到
		if ($sign) {
			Gionee_Service_Signin::updateSignin($sign_data, $sign['id']);
		}else{
			Gionee_Service_Signin::addSignin($sign_data);
		}
		
		//记录到签到日志
		Gionee_Service_SigninLog::addSigninLog($sign_log_data);
		
		//如果已签满，记录到签到历史记录
		if($number == $img['row'] * $img['col']) Gionee_Service_UserSignin::addUserSignin(array('user_id'=>$this->userInfo['id'], 'img_id'=>$img_id));
		
		$ret = Gionee_Service_User::updateUser(array('signin_num'=>$user_info['signin_num'] + 1), $user_info['id']);
		if (!$ret) $this->output(-1, '签到失败,请稍后再试！');
		
		
		
		$this->output(0, '今日签到成功,又多拼了一块拼图，明天继续哦！', array('picId'=>$img_id, 'maxCheckNum'=> $img['row']*$img['col'], 'hadCheckNum'=>$number));
	}
	
	/**
	 *
	 * 兑奖
	 */
	public function winprizeAction() {
		
		//是否有签到记录
		$sign = Gionee_Service_Signin::getSigninByUserId($this->userInfo['id']);
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if (!$sign || $sign['number'] == 0) $this->redirect($webroot.$this->actions['signinUrl']);//$this->showMsg(-1, '对不起，您没有签到记录，不能抽奖.');
		
		//是否签满
		$img = Gionee_Service_SigninImg::getSigninImg($sign['img_id']);
				
		if($sign['number'] != $img['row'] * $img['col']) {
			$this->redirect($webroot.$this->actions['signinUrl']);
			//$this->showMsg(-1, '您的签到拼图没有拼完，现在还不能兑奖呢，继续加油哦！');
		}

		//奖品
		$prizes = Gionee_Service_Prize::getCanUsePrize();
		$this->assign('prizes',$prizes);
		$this->assign('img',$img);
		$this->assign('pageTitle','签到奖品');
		$this->assign('nav','2-4');
			
	}
	
	/**
	 * 抽奖
	 */
	public function lotteryAction () {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		//是否有签到记录
		$sign = Gionee_Service_Signin::getSigninByUserId($this->userInfo['id']);
		if (!$sign || $sign['number'] == 0) $this->redirect($webroot.$this->actions['signinUrl']);//$this->output(-1, '对不起，您没有签到记录，不能抽奖.');
		
		//是否签满
		$img = Gionee_Service_SigninImg::getSigninImg($sign['img_id']);
		
		if($sign['number'] != $img['row'] * $img['col']) {
			$this->redirect($webroot.$this->actions['signinUrl']);
			//$this->output(-1, '对不起，该拼图您未签到完成，不能抽奖.');
		}
		$prize = '';
		$user_info = $this->userInfo;
		$fate = Gionee_Service_Fate::fate();
		
		$prize = Gionee_Service_Prize::getPrize($fate);
		
		$data = array(
				'user_id'=>$user_info['id'],
				'username'=>$user_info['username'],
				'mobile'=>$user_info['mobile'],
				'img_id'=>$sign['img_id'],
				'prize_id'=>$prize['id'],
				'is_prize'=>$prize['is_prize'],
				'create_time'=>Common::getTime(),
				'status'=>$prize['is_prize'] == 1 ? 2 : 1,
				'sign_id'=>$sign['id']
				);
		$lottery = Gionee_Service_Fate::lottery($data);
		if(!$lottery) $this->output(-1, '抽奖失败.');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->output(0, '抽奖成功.', array('prizeId'=>$prize['id'], 'redirectUrl'=>$webroot.$this->actions['showprizeUrl']));
	}
	
	/**
	 *
	 * 显示中奖结果
	 */
	public function showprizeAction() {
		
		$prize_id = intval($this->getInput('prize_id'));
		if(!$prize_id) $this->showMsg(-1, '非法的请求.');
		
		//奖品
		$prize = Gionee_Service_Prize::getPrize($prize_id);
		if(!$prize) $this->showMsg(-1, '非法的请求.');
		
		$this->assign('prize',$prize);
		$this->assign('user_info',$this->userInfo);
			
	}
	
	/**
	 * 编辑基本资料
	 */
	public function userinfoAction() {	
		$user_info = $this->userInfo;
		$this->assign('user_info',$user_info);
	}
	
	/**
	 * 编辑基本资料 post
	 */
	public function userinfo_postAction() {
		
		$user_info = $this->userInfo;
		$info = $this->getPost(array('realname', 'mobile', 'qq', 'address'));
		if(!$info['realname']) $this->output(-1, '请填写您的姓名.');
		if(!$info['mobile']) $this->output(-1, '请填写您的手机号.');
		if (!preg_match('/^1[3458]\d{9}$/', $info['mobile'])) $this->output(-1, "手机号码格式不正确");
		if(!$info['address']) $this->output(-1, '请填写您的地址.');
		$result = Gionee_Service_User::updateUser($info, $user_info['id']);
		if (!$result) $this->output(-1, '修改失败,请重试.');
		
		Gionee_Service_User::updateUser($data, $user_info['id']);	
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->output(0, '资料修改成功.', array('redirectUrl'=>$webroot.'/user/index/signin'));
	}
}