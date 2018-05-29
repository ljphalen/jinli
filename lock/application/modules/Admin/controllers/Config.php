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
	
	public $appCacheName = 'APPC_Front_Index_index';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Lock_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array('lock_index_cache','push_number'));
		foreach($config as $key=>$value) {
			Lock_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
}
