<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class IndexController extends Front_BaseController {
	
	/**
	 * redirect
	 */
	public function indexAction() {
		$url_id = intval($this->getInput('cid'));
		$ret = Gou_Service_Url::getBy(array('cid'=>$url_id));
		if($ret) {
			Gou_Service_Url::updateTj($ret['id']);
			
			$this->redirect(html_entity_decode($ret['url']));
			exit;
		}
		$this->redirect('http://www.miigou.com');
	}

}
