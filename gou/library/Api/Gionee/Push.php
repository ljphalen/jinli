<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Api_Gionee_Push {
	
	/**
	 * APS向Push平台请求token
	 * @param unknown_type $callback
	 * @return string
	 */
	public static function getToken() {
		$config = Common::getConfig('apiConfig');
		$params = array(
			'applicationID' => $config['appid'],
			'passwd' => $config['password']
		);
		
		$curl = new Util_Http_Curl($config['url'].':8001/sas/requestToken');
		$response = $curl->post($params);
		Common::log(array($params, $config['url'].':8001/sas/requestToken'), 'token.log');
		$ret = json_decode($response, true);
		if ($ret['result'] != 0) {
			Common::log(array($params, $ret), 'token.log');
			return false;
		}
		return $ret;
	}
	
	/**
	 * 本消息用于APS向Push平台验证rid是否有效
	 * @param string $rid
	 * @param string $token
	 * @return boolean
	 */
	public static function checkRid($rid, $token) {
		$config = Common::getConfig('apiConfig');
		$params = array(
				'rid' => $rid,
				'token' => $token
		);
		$curl = new Util_Http_Curl($config['url'].'/sas/checkrid');
		$response = $curl->post($params);
		$ret = json_decode($response, true);
		if ($ret['result'] != 0) {
			Common::log(array($params, $ret), 'checkrid.log');
			return false;
		}
		return true;
	}
	
	/**
	 * 发送单条消息
	 * @param string $rid
	 * @param string $token
	 * @param string $id
	 * @param string $msg
	 * @param string $type
	 * @param string $save
	 * @return array
	 */
	public static function pushMsg($rid, $token, $id, $msg, $type = 'notification', $save=true) {
		$config = Common::getConfig('apiConfig');
		$params = array(
				'from' => $config['appid'],
				'rid' => $rid,
				'to' => $rid,
				'type' =>$type,
				'msg' => urlencode($msg),
				'save' => $save,
				'passwd' => $config['password'],
				'token' => $token,
				'appid' => $config['appid']
		);
		$curl = new Util_Http_Curl($config['url'].':8001/push_servic');
		$response = $curl->post($params);
		$ret = json_decode($response, true);
		
		if ($ret['result'] != 'success') {
			Common::log(array($params, $ret), 'pushmsg.log');
			return false;
		}
		return $ret;	
	}
	
	
	/**
	 * 
	 * 批量发送消息
	 * @param string $rids
	 * @param string $token
	 * @param int $id
	 * @param array $msg
	 * @param string $type
	 * @param boolean $save
	 * @param string $v
	 * @return array
	 */
	public static function pushMsgBatch ($rids = array(), $token, $id, $msg, $type = 'notification', $save='true', $v = '1') {
		$config = Common::getConfig('apiConfig');
		$msgs = array(
			'type' => $type,
			'save'=> $save,
			'expired' => '0',
			'msgid' => $id,
			'msg' => urlencode($msg),
			'rids' => $rids
		);
		
		$params = array(
				'appid' => $config['appid'],
				'token' => $token,
				'msgs' => array($msgs)
		);
		$post_url = sprintf('%s:8001/push_service?v=%s&appid=%s&token=%s', $config['url'], $v, $config['appid'], $token);
		$curl = new Util_Http_Curl($post_url);
		$curl->setDataType('json');
		$response = $curl->post($params);
		$ret = json_decode($response, true);
		
		//logs
		Gou_Service_Pushlogs::addPushlogs(array('push_content'=>json_encode($params), 'return_content'=>$response));
		
		if ($ret['status'] != 0) {
			Common::log(array($params, $ret), 'pushmsg_batch.log');
			return false;
		}
		return $ret;
	}
	
}
