<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RedirectController extends Apk_BaseController {
	
	/**
	 * 第三方链接统计
	 * @return boolean
	 */
	public function indexAction(){
		$url = html_entity_decode(urldecode($this->getInput('url')));
		$this->redirect($url);
	}
}
