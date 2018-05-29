<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ConfigController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/config/index',
		'editPostUrl'=>'/admin/config/edit_post',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array('gionee_news_filter', 'gionee_news_number', 'gionee_picture_filter', 'gionee_picture_number'));
		foreach($config as $key=>$value) {
			Gionee_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
}
