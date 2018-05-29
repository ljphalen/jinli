<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class ActivityController extends Api_BaseController {
	
	public $actions = array(
				'downloadUrl'=>'/activity/download',
	);
	
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
