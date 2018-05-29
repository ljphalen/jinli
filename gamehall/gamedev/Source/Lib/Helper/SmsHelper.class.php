<?php
class SmsHelper
{
	private $sms_url = 'http://t-id.gionee.com:10808/sms/mt.do';			//?__tel=138xxxxxx&__content=xxxx
	/**
	 * 发送短信类
	 * @param string $mobile 手机号
	 * @param string $content 内容
	 * @param array $data 其他数据
	 */
	public function send($mobile,$content,$data=array())
	{
		$sms_url = C("SMS_API_URL");
		if(!empty($sms_url))
			$this->sms_url = $sms_url;
		
		if (empty($mobile) || empty($content)) return false;
		
		//发送短信
		$params = array('__ext'=>'DEV','__tel' => $mobile, '__content' => $content);
		$result = curl_get($this->sms_url,$params);
		$status = SmslogModel::STATUS_SUC;
		//写日志
		$sms_data = array(
			'mobile' => $mobile,
			'status' => $status,
			'content' => $content,
			'add_time' => time(),
			'result' => $result,
			'module' => $data['module'],
			'itme_id' => (int) $data['itme_id'],
		);
		D('Smslog')->data($sms_data)->add();
		
		return $status;
	}
	
}