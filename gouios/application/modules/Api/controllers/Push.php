<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class PushController extends Api_BaseController {
	
	/**
	 * get token
	 * 
	 */
	public function tokenAction() {
		$token = $this->getPost('token');
		//$sign = $this->getPost('sign');
		
		$key = Common::getConfig('siteConfig', 'secretKey');
		
		/*if($sign == md5($token.$key)) {
			$info = Gou_Service_Token::getByToken($token);
			if(!$info) {
				$ret = Gou_Service_Token::addToken(array('token'=>$token));
				if(!$ret) $this->clientoutput(-1, 'upload fail');
				$this->clientoutput(0, 'upload success');
			} else {
				$this->clientoutput(0, 'upload success');
			}
		}else{
			 $this->clientoutput(-1, 'upload fail');
		}*/
		
		$info = Gou_Service_Token::getByToken($token);
		if($token && !$info) {
			$ret = Gou_Service_Token::addToken(array('token'=>$token));
			if(!$ret) $this->clientoutput(-1, 'upload fail');
			$this->clientoutput(0, 'upload success');
		} else {
			$this->clientoutput(0, 'upload success');
		}
	}
}
