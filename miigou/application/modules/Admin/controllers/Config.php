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
		$configs = Gou_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array('cod_url', 'search_keyword'));
		foreach($config as $key=>$value) {
			Gou_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
}
