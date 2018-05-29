<?php
/**
 * 验证数据存取类
 *
 * @name SessionModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */

class SessionModel extends Model {
	protected $trueTableName = 'think_session';
	
	/**
	 * 创建一个系统session
	 *
	 * @param integer $uid
	 * @param string $ip
	 * @param integer $expire
	 * @return string|false
	 */
	public function createSession($uid, $ip, $expire = 0) {
		$params = array ();
		$params ['uid'] = $uid;
		$params ['sesskey'] = getSessionKey ( $uid );
		$params ['createip'] = $ip;
		$params ['expire'] = $expire;
		$params ['postid'] = getUniqId ();
		$params ['logoutid'] = getUniqId ();
		$params ['createtime'] = time ();
		$res = $this->data ( $params )->add ();
		if ($res) {
			return $params ['sesskey'];
		} else {
			return false;
		}
	}
	/**
	 * 删除系统session
	 *
	 * @param string $session_key
	 * @return boolean
	 */
	public function deleteSession($sessionkey) {
		return $this->where ( array ('sesskey' => $sessionkey ) )->delete ();
	}
	/**
	 * 得到session全部信息
	 *
	 * @param string $session_key
	 * @return array|false
	 */
	public function getSession($sessionkey) {
		return $this->where ( array ('sesskey' => $sessionkey ) )->find ();
	}
	/**
	 * 更新sessoin信息
	 *
	 * @param string $sesskey
	 * @param array $params
	 * @return boolean
	 */
	public function updateSession($sesskey, $data) {
		$params ['sesskey'] = $sesskey;
		return $this->where ( $params )->data ( $data )->save ();
	}
	public function getUserSessions($uid) {
		return $this->where ( array ('uid' => $uid ) )->select ();
	}
}

?>