<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_ActivityController extends Api_BaseController {
	
	public $actions = array(
				'sendPhoneUrl'=>'/activity/sendPhone',
				'downloadUrl'=>'/activity/download',
	);
	
	public function sendPhoneAction() {
		$phone = $this->getPost('phone');
		$sign = $this->getPost('sign');
		if(!$phone) $this->clientOutput(101, '手机号不能为空');
		if(!Common::checkMobile($phone)) $this->clientOutput(104, '手机号码格式不正确.');
		if(!$sign) $this->clientOutput(102, '签名不能为空.');
		
		//check sign
		$key = '92fe5927095eaac53cd1aa3408da8135';
		if($sign != md5($phone.$key)) $this->clientOutput(103, '非法的请求.');
		
		$info = Activity_Service_ShareQq::getBy(array('qq'=>$phone));
		
		if(!$info) {
			$data = array(
					'qq'=>$phone,
					'status'=>1,
					'create_time'=>Common::getTime()
			);
			
			$ret = Activity_Service_ShareQq::add($data);
			
			if(!$ret) $this->clientOutput(105, '提交失败');
		}
				
		$this->clientOutput(0, 'success');
	}

	public function getShareUrlAction() {
		$phone = $this->getInput('phone');
		if(!$phone) $this->output(-1, '手机号不能为空！');
		if(!Common::checkMobile($phone)) $this->output(-1, '手机号码格式不正确.');
	
		$webroot = Common::getWebRoot();
	
		$share = Activity_Service_Share::getBy(array('phone'=>$phone));
		if($share) {
			$uid = $share['uid'];
		} else {
			$config = Common::getConfig('siteConfig');
			$uid = md5($phone.$config['secretKey'].Common::getTime());
			$uid = Util_String::substr($uid, 0, 3).Util_String::substr($uid, 15, 2).Util_String::substr($uid, 26, 3);
			$data = array(
					'phone'=>$phone,
					'uid'=>$uid,
					'status'=>1,
					'create_time'=>Common::getTime()
			);
			$ret = Activity_Service_Share::add($data);
			if(!$ret) $this->output(-1, '生成地址失败');
		}
	
		$this->output(0, '', array('url'=>$webroot.$this->actions['downloadUrl'].'?uid='.$uid));
	}
}
