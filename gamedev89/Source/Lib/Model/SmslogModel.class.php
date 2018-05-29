<?php
class SmslogModel extends Model
{
	protected $trueTableName = 'smslog';
	
	CONST STATUS_INIT = 0;	//待发送
	CONST STATUS_SUC = 1;	//发送成功
	CONST STATUS_FAIL = -1;	//发送失败
	
	//定义短信发送模块
	CONST SMS_MODULE_DEFAULT = 0;		//默认模块
	CONST SMS_MODULE_PERFECT = 1;		//用户完善资料模块
	CONST SMS_MODULE_USEREDIT = 2;		//用户修改资料
	
	/**
	 * 创建并发送短信
	 */
	/**
	 * 
	 * 创建并发送短信
	 * @param string $mobile 手机号
	 * @param int $module 所属模块
	 * @param array $config 配置数据文件
	 *  array(
			'content_tpl' => '您的短信验证码为： {{code}}',		{{手机号，验证码}}
		);
	 */
	public static  function createSend($mobile,$module=self::SMS_MODULE_DEFAULT,$config=array())
	{
		$default_config = array(
			'content_tpl' => '您注册金立游戏开发者平台所需的验证码为： {{code}}，有效时间为5分钟，请保密',
		);
		$config = array_merge($default_config,$config);
		
		//@todo 加入是否还可以发送规则
		
		//生成短信码
		import('ORG.Util.String');
		$code = String::randString(6,1);
		$content = str_replace(array('{{mobile}}','{{code}}'), array($mobile,$code), $config['content_tpl']);
		$sms_data = array('module' => $module, 'itme_id' => $config['item_id']);
		$send_res = helper('Sms')->send($mobile,$content,$sms_data);
		//var_dump($send_res,$mobile,$sms_data,$content);exit;
		//暂时存储在session中
		if ($send_res > 0 )
		{
			$key = $mobile.'_'.$module.'_code';
			$_SESSION[$key] = $code;
		}
		return $send_res;
	}
	
	/**
	 * 
	 * 检查验证码是否正确
	 * @param string $mobile
	 * @param string $code
	 * @param int $module
	 */
	public static function checkCode($mobile,$code,$module=self::SMS_MODULE_DEFAULT)
	{
		if (empty($mobile) || empty($code)) return false;
		$key = $mobile.'_'.$module.'_code';
		//var_dump($code,$_SESSION[$key],func_get_args(),$_SESSION);exit;
		return $code == $_SESSION[$key]?true:false;
	}
	
}