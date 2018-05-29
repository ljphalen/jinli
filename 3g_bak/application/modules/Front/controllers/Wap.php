<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 功能机模块
 * @author tiger
 *
 */
class WapController extends Front_BaseController {

	/**
	 * 功能机首页
	 */
	public function indexAction() {
		$gionee_machine = Gionee_Service_Config::getValue('gionee_machine');
		$this->assign('gionee_machine', $gionee_machine);
		//echo html_entity_decode($gionee_machine);
	}
}