<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class WifiController extends Api_BaseController {
	
	public function smsAction() {
		$info = $this->getInput(array('mobile', 'content', 'token'));
		$token = Common::getConfig('apiConfig', 'hotwifi_token');
		if (!$info['mobile'] || !$info['content']) $this->output(-1, 'error params.');
		if ($info['token'] != $token) $this->output(-1, 'error token');
		
		$params = array(
			'__ext'=>'GOUHOT',
			'__tel'=>$info['mobile'],
			'__content'=>$info['content']
		);
		$url = sprintf("%s?%s", Common::getConfig('apiConfig', 'sms_url'), http_build_query($params));
		$ret = Util_Http::get($url);
       	if ($ret->state !== 200 ) $this->output(-1, 'error');
       	$this->output(0, 'success');
	}
}
